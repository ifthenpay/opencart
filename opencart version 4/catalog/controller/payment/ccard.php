<?php
namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/CcardPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';

use Ifthenpay\CcardPayment;
use Ifthenpay\Utils;


class Ccard extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'CCARD';
	private const STATUS_SUCCESS = '0';
	public const CALLBACK_ARGS = 'qn=[QN]';
	public const SUCCESS_STATUS = '6dfcbb0428e4f89c';
	public const ERROR_STATUS = '101737ba0aa2e7c5';
	public const CANCEL_STATUS = 'd4d26126c0f39bf2';

	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}


	/**
	 * called in the checkout page when selecting the credit card payment method, it loads the template with the confirm button and javascript code responsible for the redirect to the payment gateway
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/ifthenpay/payment/ccard');

		$templateData['button_confirm'] = $this->language->get('button_confirm');
		$templateData['action'] = $this->url->link('extension/ifthenpay/payment/ccard|confirm', '', true);

		if (isset($this->session->data['ifth_message'])) {
			$templateData['checkout_message'] = $this->session->data['ifth_message'];
			unset($this->session->data['ifth_message']);
		}

		return $this->load->view('extension/ifthenpay/payment/ccardConfirmFormAndBtn', $templateData);
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/ccard');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);

		// validate
		$json = [];

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}


		if (!$this->config->get('payment_ccard_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'ccard.ccard') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		if (!$json) {

			// get transaction url
			$ccardKey = $this->config->get('payment_ccard_key');
			$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formatedAmount = (string) round($formatedAmount, 2);

			$language = $this->language->get('code');

			$successCallbackUrl = $this->getSuccessCallbackUrl();
			$cancelCallbackUrl = $this->getCancelCallbackUrl();
			$errorCallbackUrl = $this->getErrorCallbackUrl();


			$ccardPm = new CcardPayment();
			$result = $ccardPm->generateUrl($ccardKey, $orderId, $formatedAmount, $language, $successCallbackUrl, $cancelCallbackUrl, $errorCallbackUrl);

			if (!$json && $result['code'] === self::STATUS_SUCCESS) {

				$data = [
					'payment_method' => 'ccard',
					'order_id' => $orderId,
					'transaction_id' => $result['transaction_id'],
					'status' => 'pending',
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/ccard');

				$this->model_extension_ifthenpay_payment_ccard->addCcardRecord($data);

				// log the transaction id
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : CCARD init data: ' . json_encode($data));


				$json['redirect'] = $result['payment_url'];
			}
			if (!$result || $result['code'] != self::STATUS_SUCCESS) {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to generate a Credit Card transaction. Response got return code = ' . $result['code'] . ', and message = ' . $result['message']);

				$json['error'] = $this->language->get('error_get_transaction');
				$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	/**
	 * callback function called by the payment gateway after the payment is done or canceled, or in case of error
	 */
	public function callback()
	{

		$qn = isset($this->request->get['qn']) ? $this->request->get['qn'] : '';
		$transactionId = isset($this->request->get['requestId']) ? $this->request->get['requestId'] : '';
		$amount = isset($this->request->get['amount']) ? $this->request->get['amount'] : '';
		$orderId = isset($this->request->get['id']) ? $this->request->get['id'] : '';


		try {

			// exit if does not have parameters
			if ($qn == '' || $transactionId == '' || $amount == '' || $orderId == '') {
				throw new \Exception('Invalid callback data.', 5);
			}

			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/ccard');
			$this->load->language('extension/ifthenpay/payment/ccard');

			$storedPaymentData = $this->model_extension_ifthenpay_payment_ccard->getCcardRecordByTransactionId($transactionId);

			$paymentStatus = Utils::decrypt($qn);

			if ($paymentStatus === self::SUCCESS_STATUS) {

				$this->validateCallback($storedPaymentData, $orderId, $amount);

				$this->model_checkout_order->addHistory(
					$storedPaymentData['order_id'],
					$this->config->get('payment_ccard_pending_status_id'),
					$this->getPaymentDetailsHtml(true, false),
					true
				);
				usleep(1000000); // this is an ugly hack to avoid the order history to be saved in incorrect order

				$this->model_checkout_order->addHistory(
					$storedPaymentData['order_id'],
					$this->config->get('payment_ccard_paid_status_id'),
					$this->language->get('comment_paid'),
					true
				);

				// update ccard table record
				$this->model_extension_ifthenpay_payment_ccard->updateCcardRecordStatusByTransactionId($transactionId, 'paid');


				$this->session->data['ifth_payment_info']['status'] = 'paid';

				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : CCARD init data: ' . json_encode(['transactionId' => $transactionId, 'orderId' => $orderId, 'amount' => $amount, 'status' => 'paid']));


				// redirect to success page
				$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));


			} else if ($paymentStatus === self::CANCEL_STATUS) {

				// set session message that will be used back in the checkout page
				$this->session->data['ifth_message'] = 'Credit card payment canceled by user.';

				// registration of the cancelation
				$this->model_extension_ifthenpay_payment_ccard->updateCcardRecordStatusByTransactionId($transactionId, 'canceled');

				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : CCARD init data: ' . json_encode(['transactionId' => $transactionId, 'orderId' => $orderId, 'amount' => $amount, 'status' => 'canceled']));


				// redirect to checkout page
				$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));

			} else { // error status or any other status

				// set session message that will be used back in the checkout page
				$this->session->data['ifth_message'] = 'Credit card payment failed, please try again or choose a different payment method.';

				// registration of the cancelation
				$this->model_extension_ifthenpay_payment_ccard->updateCcardRecordStatusByTransactionId($transactionId, 'failed');

				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : CCARD init data: ' . json_encode(['transactionId' => $transactionId, 'orderId' => $orderId, 'amount' => $amount, 'status' => 'error']));

				// redirect to checkout page
				$this->response->redirect($this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language')));


			}

		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : CCARD init data: ' . json_encode(['transactionId' => $transactionId, 'orderId' => $orderId, 'amount' => $amount, 'status' => 'error']));

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
	private function validateCallback($storedPaymentData, $orderId, $amount): void
	{
		// does stored payment data exist for this transaction id?
		if (!$storedPaymentData) {
			throw new \Exception('StoredPaymentData not found in local table.', 10);
		}

		// is callback order id same as the stored order id?
		if ($orderId != $storedPaymentData['order_id']) {
			throw new \Exception('Order not found.', 55);
		}

		// is order id valid? does it exist?
		$order = $this->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $amount;
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
		$this->load->language('extension/ifthenpay/payment/ccard');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_ccard_status')) {
			return;
		}
		// In case the payment method is not ccard, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'ccard'
			)
		) {
			return;
		}

		if ($this->session->data['ifth_payment_info']['status'] == 'paid') {

			$content = $this->getPaymentDetailsHtml();

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
			$this->load->language('extension/ifthenpay/payment/ccard');
		}

		$paymentInfo = $this->session->data['ifth_payment_info'];

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($paymentInfo['order_id']);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = number_format($orderInfo['total'], 2, ',', ' ') . "€";

		$params = [
			'is_to_display_in_admin' => $isForAdmin,
			'text_pay_with_method' => $this->language->get('text_paid_with_method'),
			'text_total_to_pay' => $isPaid ? $this->language->get('text_total_paid') : $this->language->get('text_total_to_pay'),
			'total' => $formatedAmount,
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/ccard.png'
		];

		return $this->load->view('extension/ifthenpay/payment/ccardSuccessInfo', $params);
	}



	/**
	 * generate the success callback url, adding the success token to the url
	 * @return string
	 */
	private function getSuccessCallbackUrl()
	{
		$successToken = Utils::encrypt(self::SUCCESS_STATUS);
		$args = str_replace('[QN]', $successToken, self::CALLBACK_ARGS);
		return $this->url->link('extension/ifthenpay/payment/ccard.callback', $args, true);
	}



	/**
	 * generate the error callback url, adding the error token to the url
	 * @return string
	 */
	private function getErrorCallbackUrl()
	{
		$errorToken = Utils::encrypt(self::ERROR_STATUS);
		$args = str_replace('[QN]', $errorToken, self::CALLBACK_ARGS);
		return $this->url->link('extension/ifthenpay/payment/ccard.callback', $args, true);
	}



	/**
	 * generate the cancel callback url, adding the cancel token to the url
	 * @return string
	 */
	private function getCancelCallbackUrl()
	{
		$cancelToken = Utils::encrypt(self::CANCEL_STATUS);
		$args = str_replace('[QN]', $cancelToken, self::CALLBACK_ARGS);
		return $this->url->link('extension/ifthenpay/payment/ccard.callback', $args, true);
	}
}
