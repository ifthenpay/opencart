<?php
namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/MbwayPayment.php';

use Ifthenpay\MbwayPayment;


class Mbway extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'MBWAY';
	private const STATUS_SUCCESS = '000';
	private const DEADLINE_MINUTES = '30';
	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}

	/**
	 * called in the checkout page when selecting the mbway payment method, it loads the template with the confirm button and javascript code responsible for the ajax request to the controller
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		$templateData['button_confirm'] = $this->language->get('button_confirm');
		$templateData['action'] = $this->url->link('extension/ifthenpay/payment/mbway|confirm', '', true);


		$language = $this->language->get('code');
		$templateData['country_code_options'] = MbwayPayment::generateCountryCodeOptions($language);

		return $this->load->view('extension/ifthenpay/payment/mbwayConfirmFormAndBtn', $templateData);
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);


		// validate
		$json = [];

		$countryCode = isset($this->request->post['mbway_country_code']) ? $this->request->post['mbway_country_code'] : '';
		$phoneNumber = isset($this->request->post['mbway_phone_number']) ? $this->request->post['mbway_phone_number'] : '';

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		// validate phone number country code
		if ($countryCode == '') {
			$json['error'] = $this->language->get('error_country_code_empty');
		}

		// validate phone number
		if (
			$phoneNumber == '' ||
			filter_var($phoneNumber, FILTER_VALIDATE_INT) === false ||
			$phoneNumber < 1
		) {
			$json['error'] = $this->language->get('error_phone_number_invalid');
		}


		if (!$this->config->get('payment_mbway_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'mbway.mbway') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {

			// get transaction
			$mbwayKey = $this->config->get('payment_mbway_key');
			$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formatedAmount = (string) round($formatedAmount, 2);

			$mbwayPm = new MbwayPayment();
			$result = $mbwayPm->generateTransaction($mbwayKey, $orderId, $formatedAmount, $countryCode . '#' . $phoneNumber);

			if (!$json && $result['code'] === self::STATUS_SUCCESS) {

				$data = [
					'payment_method' => 'mbway',
					'order_id' => $orderId,
					'transaction_id' => $result['transaction_id'],
					'phone_number' => $countryCode . '#' . $phoneNumber,
					'status' => 'pending',
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/mbway');

				$this->model_extension_ifthenpay_payment_mbway->addMbwayRecord($data);

				$this->model_checkout_order->addHistory(
					$this->session->data['order_id'],
					$this->config->get('payment_mbway_pending_status_id'),
					$this->getPaymentDetailsHtml(true),
					true
				);
				$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
			}
			if (!$result || $result['code'] != self::STATUS_SUCCESS) {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to generate a MB WAY transaction. Response got return code = ' . $result['code'] . ', and message = ' . $result['message']);

				$json['error'] = $this->language->get('error_get_transaction');
				$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	/**
	 * Event function to add payment information to the success page
	 *
	 * @param mixed $route
	 * @param mixed $data
	 * @param mixed $output
	 *
	 * @return void
	 */
	public function success_payment_info(&$route, &$data, &$output)
	{
		$this->load->model('setting/setting');
		$this->load->language('extension/ifthenpay/payment/mbway');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_mbway_status')) {
			return;
		}
		// In case the payment method is not mbway, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'mbway'
			)
		) {
			return;
		}

		$content = $this->getPaymentDetailsHtml();


		$find = '<div class="text-end">';
		$output = str_replace($find, $content . $find, $output);

		// clear session data to avoid showing the payment info in other pages
		if (isset($this->session->data['ifth_payment_info'])) {
			unset($this->session->data['ifth_payment_info']);
		}
	}



	/**
	 * get the payment details html, it generates the html for the payment details using the success_payment_info.twig template
	 * the $isForAdmin parameter is used to change the template inside the twig file, the reason for this is that, in admin, br tags are generated for every end of line
	 * @param bool $isForAdmin
	 */
	private function getPaymentDetailsHtml($isForAdmin = false)
	{
		if (!isset($this->model_checkout_order)) {
			$this->load->model('checkout/order');
		}
		if (!isset($this->language)) {
			$this->load->language('extension/ifthenpay/payment/mbway');
		}

		$paymentInfo = $this->session->data['ifth_payment_info'];

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($paymentInfo['order_id']);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = number_format($orderInfo['total'], 2, ',', ' ') . "€";

		$formatedPhoneNumber = $paymentInfo['phone_number'];
		$phoneNumberParts = explode('#', $paymentInfo['phone_number']);
		if (count($phoneNumberParts) > 1) {
			$formatedPhoneNumber = '+' . $phoneNumberParts[0] . ' ' . $phoneNumberParts[1];
		}

		$params = [
			'is_to_display_in_admin' => $isForAdmin,
			'text_pay_with_method' => $this->language->get('text_pay_with_method'),
			'text_transaction_id' => $this->language->get('text_transaction_id'),
			'text_total_to_pay' => $this->language->get('text_total_to_pay'),
			'text_phone_number' => $this->language->get('text_phone_number'),
			'text_error' => $this->language->get('text_error'),
			'text_did_not_receive_notification' => $this->language->get('text_did_not_receive_notification'),
			'text_resend_notification' => $this->language->get('text_resend_notification'),
			'error_resending_notification' => $this->language->get('error_resending_notification'),
			'phone_number' => $formatedPhoneNumber,
			'transaction_id' => $paymentInfo['transaction_id'],
			'order_id' => $paymentInfo['order_id'],
			'total' => $formatedAmount,
			'show_countdown' => $this->config->get('payment_mbway_show_countdown'),
			'url_check_mbway_status_ctrl' => $this->url->link('extension/ifthenpay/payment/mbway.ajaxCheckMbwayPaymentStatus'),
			'url_resend_notification_ctrl' => $this->url->link('extension/ifthenpay/payment/mbway.ajaxResendMbwayNotification'),
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/mbway.png',
		];

		return $this->load->view('extension/ifthenpay/payment/mbwaySuccessInfo', $params);
	}



	/**
	 * Callback function for Mbway payment method (called externaly by Ifthenpay)
	 * must finish with die('ok') or die('fail - <code>');
	 * code represents the error code
	 * 10 - StoredPaymentData not found in local table.
	 * 30 - Callback is not active.
	 * 40 - Invalid anti-phishing key.
	 * 50 - Order not found.
	 * 60 - Invalid amount.
	 */
	public function callback()
	{
		try {
			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/mbway');
			$this->load->language('extension/ifthenpay/payment/mbway');

			if (!isset($this->request->get['transaction_id'])) {

				throw new \Exception('StoredPaymentData not found in local table.', 10);
			}

			$storedPaymentData = $this->model_extension_ifthenpay_payment_mbway->getMbwayRecordByTransactionId($this->request->get['transaction_id']);

			if ($storedPaymentData['status'] === 'paid') {
				http_response_code(200);
				die('ok - encomenda já se encontra paga');
			}

			$this->validateCallback($this->request->get, $storedPaymentData);

			// update order history status
			$this->model_checkout_order->addHistory($storedPaymentData['order_id'], (int) $this->config->get('payment_mbway_paid_status_id'), $this->language->get('comment_paid'), true);

			// update mbway table record
			$this->model_extension_ifthenpay_payment_mbway->updateMbwayRecordStatus($storedPaymentData['order_id'], 'paid');


		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : Callback execution failed with message of ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}
		http_response_code(200);
		die('ok');
	}



	/**
	 * Validate the callback data sent by Ifthenpay, and throws an exception with a code if something is wrong
	 */
	private function validateCallback($callbackData, $storedPaymentData): void
	{
		if (!$storedPaymentData) {
			throw new \Exception('StoredPaymentData not found in local table.', 10);
		}

		// is callback active?
		if (!$this->config->get('payment_mbway_activate_callback')) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->config->get('payment_mbway_anti_phishing_key'))) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}



	/**
	 * Cronjob for canceling order that passed the payment deadline
	 * accessible in <baseURL>/index.php?route=extension/ifthenpay/payment/mbway|cronCancelOrder
	 */
	public function cronCancelOrder()
	{
		$this->load->language('extension/ifthenpay/payment/mbway');
		$this->load->model('checkout/order');
		$this->load->model('extension/ifthenpay/payment/mbway');

		if (!$this->config->get('payment_mbway_cancel_cronjob')) {
			return;
		}

		$pendingMbwayOrders = $this->model_extension_ifthenpay_payment_mbway->getMbwayRecordsByPendingStatus();

		$canceledStatusId = $this->config->get('payment_mbway_canceled_status_id');

		$orderIdArray = [];


		foreach ($pendingMbwayOrders as $order) {

			// calculate deadline by adding DEADLINE_MINUTES minutes to the order date
			$deadline = \DateTime::createFromFormat('Y-m-d H:i:s', $order['date_added']);
			$deadline->add(new \DateInterval('PT' . self::DEADLINE_MINUTES . 'M'));
			$deadlineStr = $deadline->format('Y-m-d H:i:s');


			// if deadline has expired
			if (strtotime($deadlineStr) < strtotime(date('Y-m-d H:i:s'))) {
				// update order status
				$this->model_checkout_order->addHistory($order['order_id'], (int) $canceledStatusId, $this->language->get('comment_canceled_by_cron'), true);

				// update mbway table record
				$this->model_extension_ifthenpay_payment_mbway->updateMbwayRecordStatus($order['order_id'], 'canceled');

				$orderIdArray[] = $order['order_id'];
			}
		}

		$ordersString = implode(', ', $orderIdArray);

		// will only log if an order was canceled
		if ($ordersString != '') {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Cancel cron executed. The following orders were canceled: ' . $ordersString);
		}
	}



	/**
	 * get mbway payment status using the transaction id and the mbway key
	 * responds to ajax request
	 * @return void
	 */
	public function ajaxCheckMbwayPaymentStatus(): void
	{
		$mbwayKey = $this->config->get('payment_mbway_key');
		$transactionId = $this->request->post['transaction_id'];

		$mbwayPm = new MbwayPayment();
		$result = $mbwayPm->checkMbwayPaymentStatus($transactionId, $mbwayKey);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}



	/**
	 * resend new mbway notification to the customer
	 * responds to ajax request
	 * @return void
	 */
	public function ajaxResendMbwayNotification(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');
		$this->load->model('checkout/order');
		$this->load->model('extension/ifthenpay/payment/mbway');


		// default failure response
		$json = [
			'success' => false,
			'message' => $this->language->get('error_resending_notification'),
			'transaction_id' => ''
		];

		$mbwayKey = $this->config->get('payment_mbway_key');
		$orderId = $this->request->post['order_id'];
		$phoneNumber = $this->request->post['phone_number'];
		$phoneNumber = str_replace(' ', '#', $phoneNumber);

		// get ammount from order
		$order = $this->model_checkout_order->getOrder($orderId);
		$formattedAmount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formattedAmount = (string) number_format($formattedAmount, 2, '.', '');


		$mbwayPm = new MbwayPayment();
		$result = $mbwayPm->generateTransaction($mbwayKey, $orderId, $formattedAmount, $phoneNumber);


		if ($result['code'] === self::STATUS_SUCCESS) {

			// update history
			$this->model_checkout_order->addHistory(
				$orderId,
				$this->config->get('payment_mbway_pending_status_id'),
				$this->language->get('text_resend_notification_with') . $result['transaction_id'],
				true
			);

			// update local database with new transaction id
			$this->model_extension_ifthenpay_payment_mbway->updateMbwayRecordTransactionId($orderId, $result['transaction_id']);

			$json['success'] = true;
			$json['message'] = $this->language->get('text_resend_notification_success');
			$json['transaction_id'] = $result['transaction_id'];
		} else {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to resend a MB WAY notification. Request response = ' . json_encode($result));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
