<?php

namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/IfthenpaygatewayPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/CallbackService.php';

use Ifthenpay\CallbackService;

use Ifthenpay\IfthenpaygatewayPayment;
use Ifthenpay\Utils;


class Ifthenpaygateway extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'IFTHENPAYGATEWAY';


	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}


	/**
	 * called in the checkout page when selecting the Ifthenpay Gateway method, it loads the template with the confirm button and javascript code responsible for the redirect to the payment gateway
	 * @return string
	 */
	public function index(): string
	{
		try {

			$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

			$templateData['button_confirm'] = $this->language->get('button_confirm');
			$templateData['action'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway|confirm', '', true);

			$templateData['checkout_message'] = '';
			if (isset($this->session->data['ifth_message'])) {
				$templateData['checkout_message'] = $this->session->data['ifth_message'];
				unset($this->session->data['ifth_message']);
			}

			return $this->load->view('extension/ifthenpay/payment/ifthenpaygatewayConfirmFormAndBtn', $templateData);
		} catch (\Throwable $th) {
			throw $th;
		}
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);

		// validate
		$json = [];

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}


		if (!$this->config->get('payment_ifthenpaygateway_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'ifthenpaygateway.ifthenpaygateway') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		if (!$json) {

			// get transaction url
			$ifthenpaygatewayKey = $this->config->get('payment_ifthenpaygateway_key');
			$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formatedAmount = (string) round($formatedAmount, 2);

			$language = $this->language->get('code');


			// $transactionId = Utils::generateString(20);
			$transactionId = Utils::generateTransactionId($orderId, $this->config->get('payment_ifthenpaygateway_transaction_token'));



			$btnCloseLabel = $this->language->get('text_button_close_btn');
			$btnCloseUrl = $this->getOrderConfirmUrl($orderId);
			$successCallbackUrl = $this->getSuccessCallbackUrl($transactionId);
			$cancelCallbackUrl = $this->getCancelCallbackUrl($transactionId);
			$errorCallbackUrl = $this->getErrorCallbackUrl($transactionId);


			$description = 'Opencart order #' . $orderId;

			$daysToDeadline = $this->config->get('payment_ifthenpaygateway_deadline');
			$deadline = Utils::dateAfterDays($daysToDeadline);


			$methods = array_map(function ($item) {
				if (isset($item['is_active']) && $item['is_active'] === '1') {
					return $item['account'];
				}
			}, $this->config->get('payment_ifthenpaygateway_methods'));


			$methodsStr = implode(';', str_replace(' ', '', $methods));

			$selectedMethod = $this->config->get('payment_ifthenpaygateway_default_method');



			$ifthenpaygatewayPm = new IfthenpaygatewayPayment();
			$result = $ifthenpaygatewayPm->generateUrl($ifthenpaygatewayKey, $orderId, $formatedAmount, $description, $language, $deadline, $methodsStr, $selectedMethod, $btnCloseUrl, $btnCloseLabel, $successCallbackUrl, $cancelCallbackUrl, $errorCallbackUrl);

			if (!$json && $result['payment_url'] !== '') {


				$data = [
					'payment_method' => 'ifthenpaygateway',
					'transaction_id' => $transactionId,
					'order_id' => $orderId,
					'status' => 'pending',
					'payment_url' => $result['payment_url'],
					'deadline' => Utils::convertDateFormat($deadline, 'Ymd', 'd/m/Y'),
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

				$this->model_extension_ifthenpay_payment_ifthenpaygateway->addIfthenpaygatewayRecord($data);

				// log the data
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode($data));


				$json['redirect'] = $result['payment_url'];
			}
			if (!$result || $result['payment_url'] === '') {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to generate an Ifthenpay Gateway transaction. Response got return pin_code = ' . $result['pin_code'] ?? '' . ', and payment_url = ' . $result['payment_url'] ?? '');

				$json['error'] = $this->language->get('error_get_transaction');
				$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	public function callback(){

		(new CallbackService($this->registry))->HandleFromIfthenpayGateway($this->request);
	}



	/**
	 * callback function called by the payment gateway after clicking the close button in the gateway page
	 * this sets the payment status to pending and redirects to the success page
	 * this is expected, when a customer chooses an offline payment method like multibanco or payshop and clicks the close button
	 */
	public function handleCallbackReturn()
	{
		$orderId = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : '';

		try {

			if (!isset($this->session->data['ifth_payment_info'])) {
				$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
			}

			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');
			$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

			$this->model_checkout_order->addHistory(
				$orderId,
				$this->config->get('payment_ifthenpaygateway_pending_status_id'),
				$this->getPaymentDetailsHtml(true, false),
				true
			);

			$this->session->data['ifth_payment_info']['status'] = 'pending';


			$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['orderId' => $orderId, 'status' => 'error', 'message' => $th->getMessage()]));

			$this->session->data['ifth_payment_info']['status'] == 'failed';
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}
	}



	public function handleCallbackReturnSuccess()
	{

		$transactionId = isset($this->request->get['transaction_id']) ? $this->request->get['transaction_id'] : '';

		try {

			// exit if does not have parameters
			if ($transactionId == '') {
				throw new \Exception('Invalid callback data.', 5);
			}

			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');
			$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

			$storedPaymentData = $this->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpaygatewayRecordByTransactionId($transactionId);


			$this->validateSuccessCallback($this->request->get, $storedPaymentData);

			$this->model_checkout_order->addHistory(
				$storedPaymentData['order_id'],
				$this->config->get('payment_ifthenpaygateway_paid_status_id'),
				$this->language->get('comment_paid'),
				true
			);

			// update ifthenpaygateway table record
			$this->model_extension_ifthenpay_payment_ifthenpaygateway->updateIfthenpaygatewayRecordStatusByTransactionId($transactionId, 'paid');


			$this->session->data['ifth_payment_info']['status'] = 'paid';

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['orderId' => $storedPaymentData['order_id'], 'status' => 'paid']));


			// redirect to success page
			$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['orderId' => $storedPaymentData['order_id'], 'status' => 'error', 'message' => $th->getMessage()]));

			$this->session->data['ifth_payment_info']['status'] == 'failed';
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}

		return;
	}



	public function handleCallbackReturnCancel()
	{
		$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

		$transactionId = isset($this->request->get['transaction_id']) ? $this->request->get['transaction_id'] : '';

		try {

			// set session message that will be used back in the checkout page
			$this->session->data['ifth_message'] = 'Ifthenpay Gateway canceled by user.';

			// registration of the cancelation
			$this->model_extension_ifthenpay_payment_ifthenpaygateway->updateIfthenpaygatewayRecordStatusByTransactionId($transactionId, 'canceled');

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['transactionId' => $transactionId, 'status' => 'canceled']));


			// redirect to checkout page
			$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['transactionId' => $transactionId, 'status' => 'error', 'message' => $th->getMessage()]));

			$this->session->data['ifth_payment_info']['status'] == 'failed';
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}

		return;
	}



	public function handleCallbackReturnError()
	{
		$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

		$transactionId = isset($this->request->get['transaction_id']) ? $this->request->get['transaction_id'] : '';

		try {

			// set session message that will be used back in the checkout page
			$this->session->data['ifth_message'] = 'Ifthenpay Gateway failed, please try again or choose a different payment method.';

			// registration of the cancelation
			$this->model_extension_ifthenpay_payment_ifthenpaygateway->updateIfthenpaygatewayRecordStatusByTransactionId($transactionId, 'failed');

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['transactionId' => $transactionId, 'status' => 'error']));

			// redirect to checkout page
			$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));
		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : IFTHENPAYGATEWAY init data: ' . json_encode(['transactionId' => $transactionId, 'status' => 'error', 'message' => $th->getMessage()]));

			$this->session->data['ifth_payment_info']['status'] == 'failed';
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}

		return;
	}



	/**
	 * validate the callback arguments and throw an exception if any of the arguments is invalid
	 * @param array $storedPaymentData
	 * @param string $orderId
	 * @param string $amount
	 * @return void
	 */
	private function validateSuccessCallback($requestData, $storedPaymentData): void
	{

		$order = $this->model_checkout_order->getOrder($storedPaymentData['order_id']);

		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		if ($storedPaymentData['order_id'] != $requestData['id']) {
			throw new \Exception('StoredPaymentData order_id does not match with callback order_id.', 10);
		}

		// is order amount valid?
		$callbackAmount = $requestData['amount'];
		$formatedAmount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}

	}



	/**
	 * Event function to add payment information to the success page, this function is called by the event 'catalog/view/common/success/after'
	 * apparently this triggers for the failure as well
	 * @param mixed $route
	 * @param mixed $data
	 * @param mixed $output
	 * @return void
	 */
	public function success_payment_info(&$route, &$data, &$output)
	{
		$this->load->model('setting/setting');
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_ifthenpaygateway_status')) {
			return;
		}

		// In case the payment method is not ifthenpaygateway, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'ifthenpaygateway'
			)
		) {
			return;
		}



		if ($this->session->data['ifth_payment_info']['status'] == 'paid' || $this->session->data['ifth_payment_info']['status'] == 'pending') {

			$isPaid = ($this->session->data['ifth_payment_info']['status'] == 'paid') ? true : false;

			$content = $this->getPaymentDetailsHtml(false, $isPaid);

			$find = '<div class="text-end">';
			$output = str_replace($find, $content . $find, $output);
		}

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
	private function getPaymentDetailsHtml($isForAdmin = false, $isPaid = true)
	{
		if (!isset($this->model_checkout_order)) {
			$this->load->model('checkout/order');
		}
		if (!isset($this->language)) {
			$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');
		}

		$paymentInfo = $this->session->data['ifth_payment_info'];

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($paymentInfo['order_id']);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = number_format($orderInfo['total'], 2, ',', ' ') . "€";

		$params = [
			'is_paid' => $isPaid,
			'is_to_display_in_admin' => $isForAdmin,
			'text_pay_with_method' => $this->language->get('text_paid_with_method'),
			'text_payment_url' => $this->language->get('text_payment_url'),
			'text_goto_gateway_btn' => $this->language->get('text_goto_gateway_btn'),
			'text_total_to_pay' => $isPaid ? $this->language->get('text_total_paid') : $this->language->get('text_total_to_pay'),
			'total' => $formatedAmount,
			'transaction_id' => $paymentInfo['transaction_id'],
			'payment_url' => $paymentInfo['payment_url'],
			'deadline' => $paymentInfo['deadline'],
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/ifthenpaygateway.png'
		];

		return $this->load->view('extension/ifthenpay/payment/ifthenpaygatewaySuccessInfo', $params);
	}


	private function getOrderConfirmUrl(string $orderId)
	{
		$args = ['order_id' => $orderId];

		return $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.handleCallbackReturn', $args, false);
	}



	/**
	 * generate the success callback url, adding the success token to the url
	 * @return string
	 */
	private function getSuccessCallbackUrl(string $transactionId)
	{
		$args = [
			'transaction_id' => $transactionId
		];
		$queryStr = '&' . http_build_query($args);

		return $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.handleCallbackReturnSuccess') . $queryStr;
	}



	/**
	 * generate the error callback url, adding the error token to the url
	 * @return string
	 */
	private function getErrorCallbackUrl(string $transactionId)
	{
		$args = [
			'transaction_id' => $transactionId
		];
		$queryStr = '&' . http_build_query($args);

		return $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.handleCallbackReturnError') . $queryStr;
	}



	/**
	 * generate the cancel callback url, adding the cancel token to the url
	 * @return string
	 */
	private function getCancelCallbackUrl(string $transactionId)
	{
		$args = [
			'transaction_id' => $transactionId
		];
		$queryStr = '&' . http_build_query($args);

		return $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.handleCallbackReturnCancel') . $queryStr;
	}



	public function cronCancelOrder()
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');
		$this->load->model('checkout/order');
		$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->config->get('payment_ifthenpaygateway_cancel_cronjob')) {
			return;
		}

		$pendingIfthenpaygatewayOrders = $this->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpaygatewayRecordsByPendingStatus();

		$canceledStatusId = $this->config->get('payment_ifthenpaygateway_canceled_status_id');

		$orderIdArray = [];


		foreach ($pendingIfthenpaygatewayOrders as $order) {

			// calculate deadline by adding DEADLINE_MINUTES minutes to the order date
			$deadline = \DateTime::createFromFormat('d/m/Y', $order['deadline']);
			$deadlineStr = $deadline->format('Y-m-d');

			// if deadline has expired
			if (strtotime($deadlineStr) < strtotime(Utils::timeStamp())) {
				// update order status
				$this->model_checkout_order->addHistory($order['order_id'], (int) $canceledStatusId, $this->language->get('comment_canceled_by_cron'), true);

				// update ifthenpaygateway table record

				$this->model_extension_ifthenpay_payment_ifthenpaygateway->updateIfthenpaygatewayRecordStatus($order['order_id'], 'canceled');

				$orderIdArray[] = $order['order_id'];
			}
		}

		$ordersString = implode(', ', $orderIdArray);

		// will only log if an order was canceled
		if ($ordersString != '') {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Cancel cron executed. The following orders were canceled: ' . $ordersString);
		}
	}
}
