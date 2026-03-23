<?php

namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/PixPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/CallbackService.php';

use Ifthenpay\CallbackService;
use Ifthenpay\PixPayment;
use Ifthenpay\Utils;

class Pix extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'PIX';
	private const STATUS_SUCCESS = '0';
	private const DEADLINE_MINUTES = '30';

	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}

	/**
	 * called in the checkout page when selecting the pix payment method, it loads the template with the confirm button and javascript code responsible for the ajax request to the controller
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/ifthenpay/payment/pix');

		$templateData['button_confirm'] = $this->language->get('button_confirm');
		$templateData['action'] = $this->url->link('extension/ifthenpay/payment/pix|confirm', '', true);

		return $this->load->view('extension/ifthenpay/payment/pixConfirmFormAndBtn', $templateData);
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/pix');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);


		// validate
		$json = [];

		$name = isset($this->request->post['pix_name']) ? $this->request->post['pix_name'] : '';
		$cpf = isset($this->request->post['pix_cpf']) ? $this->request->post['pix_cpf'] : '';
		$email = isset($this->request->post['pix_email']) ? $this->request->post['pix_email'] : '';

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$json['error'] = $this->language->get('error_email_invalid');
		}

		if ($cpf == '' || !preg_match("/^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/", $cpf)) {
			$json['error'] = $this->language->get('error_cpf_invalid');
		}

		if ($name == '' || strlen($name) > 150) {
			$json['error'] = $this->language->get('error_name_required');
		}


		if (!$this->config->get('payment_pix_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'pix.pix') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {

			// get transaction
			$pixKey = $this->config->get('payment_pix_key');
			$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formatedAmount = (string) round($formatedAmount, 2);

			$returnUrl = $this->getReturnUrl($orderId);

			$pixPm = new PixPayment();
			$result = $pixPm->generateTransaction($pixKey, $orderId, $formatedAmount, $returnUrl, $name, $cpf, $email);

			if (!$json && $result['code'] === self::STATUS_SUCCESS) {

				$data = [
					'payment_method' => 'pix',
					'order_id' => $orderId,
					'transaction_id' => $result['transaction_id'],
					'payment_url' => $result['payment_url'],
					'status' => 'pending',
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/pix');

				$this->model_extension_ifthenpay_payment_pix->addPixRecord($data);

				$this->model_checkout_order->addHistory(
					$this->session->data['order_id'],
					$this->config->get('payment_pix_pending_status_id'),
					$this->getPaymentDetailsHtml(true),
					true
				);
				$json['redirect'] = $result['payment_url'];
			}
			if (!$result || $result['code'] != self::STATUS_SUCCESS) {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to generate a Pix transaction. Response got return code = ' . $result['code'] . ', and message = ' . $result['message']);

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
		$this->load->language('extension/ifthenpay/payment/pix');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_pix_status')) {
			return;
		}
		// In case the payment method is not pix, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'pix'
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



	public function injectIconCss(&$route, &$data)
	{
		if (!$this->config->get('payment_pix_show_icon_checkout') || !isset($data['header'])) {
			return;
		}

		$data['header'] .= Utils::getPaymentIconCssInjectionScript('pix');
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
			$this->load->language('extension/ifthenpay/payment/pix');
		}

		$paymentInfo = $this->session->data['ifth_payment_info'];

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($paymentInfo['order_id']);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = number_format($orderInfo['total'], 2, ',', ' ') . "€";

		$params = [
			'is_to_display_in_admin' => $isForAdmin,
			'text_pay_with_method' => $this->language->get('text_pay_with_method'),
			'text_total_to_pay' => $isPaid ? $this->language->get('text_total_paid') : $this->language->get('text_total_to_pay'),
			'text_transaction_id' => $this->language->get('text_transaction_id'),
			'text_payment_url' => $this->language->get('text_payment_url'),
			'order_id' => $paymentInfo['order_id'],
			'total' => $formatedAmount,
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/pix.png',
		];

		if ($isForAdmin) {
			$params['transaction_id'] = $paymentInfo['transaction_id'];
		}

		return $this->load->view('extension/ifthenpay/payment/pixSuccessInfo', $params);
	}



	/**
	 * Callback function for Pix payment method (called externaly by Ifthenpay)
	 */
	public function callback()
	{
		(new CallbackService($this->registry))->HandleFromPix($this->request);
	}


	private function getReturnUrl(string $orderId)
	{
		$args = ['order_id' => $orderId];

		return $this->url->link('extension/ifthenpay/payment/pix.handleCallbackReturn', $args, false);
	}


	public function handleCallbackReturn()
	{
		$orderId = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : '';

		try {

			if (!isset($this->session->data['ifth_payment_info'])) {
				$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
			}

			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/pix');
			$this->load->language('extension/ifthenpay/payment/pix');

			$this->model_checkout_order->addHistory(
				$orderId,
				$this->config->get('payment_pix_pending_status_id'),
				$this->getPaymentDetailsHtml(true, false),
				true
			);

			$this->session->data['ifth_payment_info']['status'] = 'pending';


			$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));
		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : PIX init data: ' . json_encode(['orderId' => $orderId, 'status' => 'error', 'message' => $th->getMessage()]));

			$this->session->data['ifth_payment_info']['status'] == 'failed';
			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}
	}


	/**
	 * Cronjob for canceling order that passed the payment deadline
	 * accessible in <baseURL>/index.php?route=extension/ifthenpay/payment/pix|cronCancelOrder
	 */
	public function cronCancelOrder()
	{
		$this->load->language('extension/ifthenpay/payment/pix');
		$this->load->model('checkout/order');
		$this->load->model('extension/ifthenpay/payment/pix');

		if (!$this->config->get('payment_pix_cancel_cronjob')) {
			return;
		}

		$pendingPixOrders = $this->model_extension_ifthenpay_payment_pix->getPixRecordsByPendingStatus();

		$canceledStatusId = $this->config->get('payment_pix_canceled_status_id');

		$orderIdArray = [];


		foreach ($pendingPixOrders as $order) {

			// calculate deadline by adding DEADLINE_MINUTES minutes to the order date
			$deadline = \DateTime::createFromFormat('Y-m-d H:i:s', $order['date_added']);
			$deadline->add(new \DateInterval('PT' . self::DEADLINE_MINUTES . 'M'));
			$deadlineStr = $deadline->format('Y-m-d H:i:s');


			// if deadline has expired
			if (strtotime($deadlineStr) < strtotime(date('Y-m-d H:i:s'))) {
				// update order status
				$this->model_checkout_order->addHistory($order['order_id'], (int) $canceledStatusId, $this->language->get('comment_canceled_by_cron'), true);

				// update pix table record
				$this->model_extension_ifthenpay_payment_pix->updatePixRecordStatus($order['order_id'], 'canceled');

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
