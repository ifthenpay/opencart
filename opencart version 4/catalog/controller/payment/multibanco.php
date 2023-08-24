<?php
namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/MultibancoPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Gateway.php';

use Ifthenpay\MultibancoPayment;
use Ifthenpay\Gateway;


class Multibanco extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'MULTIBANCO';

	private const STATUS_SUCCESS = '0';

	private const DEADLINE_HOURS = 23;
	private const DEADLINE_MINUTES = 59;

	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}

	/**
	 * called in the checkout page when selecting the multibanco payment method, it loads the template with the confirm button and javascript code responsible for the ajax request to the controller
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');

		$templateData['button_confirm'] = $this->language->get('button_confirm');
		$templateData['action'] = $this->url->link('extension/ifthenpay/payment/multibanco|confirm', '', true);

		return $this->load->view('extension/ifthenpay/payment/multibancoConfirmBtn', $templateData);
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);


		// validate
		$json = [];

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		if (!$this->config->get('payment_multibanco_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'multibanco.multibanco') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {

			// generate static reference
			$multibancoPm = new MultibancoPayment();
			$entity = $this->config->get('payment_multibanco_entity');
			$subEntity = $this->config->get('payment_multibanco_sub_entity');
			$formattedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formattedAmount = (string) round($formattedAmount, 2);
			$deadline = $this->config->get('payment_multibanco_deadline');


			if ($entity == MultibancoPayment::DYNAMIC_ENTITY_NAME) {
				$multibancoPm = new MultibancoPayment();
				$result = $multibancoPm->generateReference($entity, $subEntity, $orderId, $formattedAmount, $deadline);

				if ($result['code'] != self::STATUS_SUCCESS) {
					$json['error'] = $result['message'];
				} else {
					$entity = $result['entity'];
					$reference = $result['reference'];
					$requestId = $result['transaction_id'];
					$deadline = $result['deadline'];
				}

			} else {
				$reference = $multibancoPm->generateStaticReference($orderId, $formattedAmount, $entity, $subEntity);
				$requestId = null;
				$deadline = null;
			}

			if (!$json) {

				$data = [
					'payment_method' => 'multibanco',
					'order_id' => $orderId,
					'entity' => $entity,
					'reference' => $reference,
					'request_id' => $requestId,
					'deadline' => $deadline,
					'status' => 'pending',
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/multibanco');

				$this->model_extension_ifthenpay_payment_multibanco->addMultibancoRecord($data);

				$this->model_checkout_order->addHistory(
					$this->session->data['order_id'],
					$this->config->get('payment_multibanco_pending_status_id'),
					$this->getPaymentDetailsHtml(true),
					true
				);


				$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
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
		$this->load->language('extension/ifthenpay/payment/multibanco');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_multibanco_status')) {
			return;
		}
		// In case the payment method is not multibanco, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'multibanco'
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
			$this->load->language('extension/ifthenpay/payment/multibanco');
		}

		$paymentInfo = $this->session->data['ifth_payment_info'];

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($paymentInfo['order_id']);

		$formatedReference = substr($paymentInfo['reference'], 0, 3) . " " . substr($paymentInfo['reference'], 3, 3) . " " . substr($paymentInfo['reference'], 6);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = number_format($orderInfo['total'], 2, ',', ' ') . "€";

		$params = [
			'is_to_display_in_admin' => $isForAdmin,
			'text_pay_with_method' => $this->language->get('text_pay_with_method'),
			'text_entity' => $this->language->get('text_entity'),
			'text_reference' => $this->language->get('text_reference'),
			'text_total_to_pay' => $this->language->get('text_total_to_pay'),
			'entity' => $paymentInfo['entity'],
			'reference' => $formatedReference,
			'total' => $formatedAmount,
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/multibanco.png'
		];

		if ($paymentInfo['deadline'] != '') {
			$params['text_deadline'] = $this->language->get('text_deadline');
			$params['deadline'] = $paymentInfo['deadline'];
		}

		return $this->load->view('extension/ifthenpay/payment/multibancoSuccessInfo', $params);
	}



	/**
	 * Callback function for Multibanco payment method (called externaly by Ifthenpay)
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
			$this->load->model('extension/ifthenpay/payment/multibanco');
			$this->load->language('extension/ifthenpay/payment/multibanco');

			if (!isset($this->request->get['reference'])) {

				throw new \Exception('StoredPaymentData not found in local table.', 10);
			}

			$storedPaymentData = $this->model_extension_ifthenpay_payment_multibanco->getMultibancoRecordByReference($this->request->get['reference']);

			if ($storedPaymentData['status'] === 'paid') {
				http_response_code(200);
				die('ok - encomenda já se encontra paga');
			}

			$this->validateCallback($this->request->get, $storedPaymentData);

			// update order history status
			$this->model_checkout_order->addHistory($storedPaymentData['order_id'], (int) $this->config->get('payment_multibanco_paid_status_id'), $this->language->get('comment_paid'), true);

			// update multibanco table record
			$this->model_extension_ifthenpay_payment_multibanco->updateMultibancoRecordStatus($storedPaymentData['order_id'], 'paid');


		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}



	/**
	 * Validate the callback data sent by Ifthenpay, and throws an exception with a code if something is wrong
	 * @param array $callbackData
	 * @param array $storedPaymentData
	 * @return void
	 * @throws \Exception
	 */
	private function validateCallback($callbackData, $storedPaymentData): void
	{
		if (!$storedPaymentData) {
			throw new \Exception('StoredPaymentData not found in local table.', 10);
		}

		// is callback active?
		if (!$this->config->get('payment_multibanco_activate_callback')) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->config->get('payment_multibanco_anti_phishing_key'))) {
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
	 * accessible in <baseURL>/index.php?route=extension/ifthenpay/payment/multibanco|cronCancelOrder
	 */
	public function cronCancelOrder()
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');
		$this->load->model('checkout/order');
		$this->load->model('extension/ifthenpay/payment/multibanco');

		if (!$this->config->get('payment_multibanco_cancel_cronjob')) {
			return;
		}

		$pendingMultibancoOrders = $this->model_extension_ifthenpay_payment_multibanco->getMultibancoRecordsByPendingStatus();

		$canceledStatusId = $this->config->get('payment_multibanco_canceled_status_id');

		$orderIdArray = [];

		foreach ($pendingMultibancoOrders as $order) {
			if ($order['deadline'] != '') {

				$deadline = \DateTime::createFromFormat('d-m-Y', $order['deadline']);
				$deadline->setTime(self::DEADLINE_HOURS, self::DEADLINE_MINUTES);
				$deadlineStr = $deadline->format('Y-m-d H:i:s');

				// if deadline has expired
				if (strtotime($deadlineStr) < strtotime(date('Y-m-d H:i:s'))) {

					// update order history status
					$this->model_checkout_order->addHistory($order['order_id'], (int) $canceledStatusId, $this->language->get('comment_canceled_by_cron'), true);

					// update multibanco table record
					$this->model_extension_ifthenpay_payment_multibanco->updateMultibancoRecordStatus($order['order_id'], 'canceled');

					$orderIdArray[] = $order['order_id'];
				}
			}
		}

		$ordersString = implode(', ', $orderIdArray);

		// will only log if an order was canceled
		if ($ordersString != '') {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Cancel cron executed. The following orders were canceled: ' . $ordersString);
		}
	}
}
