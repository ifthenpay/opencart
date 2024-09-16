<?php
namespace Opencart\Catalog\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/CofidisPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/CallbackService.php';



use Ifthenpay\ApiService;
use Ifthenpay\CofidisPayment;
use Ifthenpay\Utils;
use Ifthenpay\CallbackService;


class Cofidis extends \Opencart\System\Engine\Controller
{
	private const PAYMENTMETHOD = 'COFIDIS';
	private const STATUS_SUCCESS = '0';

	public const INIT_STATUS_TRUE = 'True';
	public const INIT_STATUS_FALSE = 'False';

	// cofidis status
	public const COFIDIS_STATUS_INITIATED = 'INITIATED';  // this is pending (pending)
	public const COFIDIS_STATUS_CANCELED = 'CANCELED'; // 1 (canceled)
	public const COFIDIS_STATUS_PENDING_INVOICE = 'PENDING_INVOICE'; // approved mas aguarda a fatura, ainda pode falhar (pending)
	public const COFIDIS_STATUS_NOT_APPROVED = 'NOT_APPROVED'; // (depois do pending invoice) (failed)
	public const COFIDIS_STATUS_FINANCED = 'FINANCED'; // (depois do pending invoice) this is payed (processed)
	public const COFIDIS_STATUS_EXPIRED = 'EXPIRED'; // not sure what this is (expired)
	public const COFIDIS_STATUS_TECHNICAL_ERROR = 'TECHNICAL_ERROR'; // (failed)


	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}


	/**
	 * called in the checkout page when selecting the Cofidis payment method, it loads the template with the confirm button and javascript code responsible for the redirect to the payment gateway
	 * @return string
	 */
	public function index(): string
	{
		$this->load->language('extension/ifthenpay/payment/cofidis');

		$templateData['button_confirm'] = $this->language->get('button_confirm');
		$templateData['action'] = $this->url->link('extension/ifthenpay/payment/cofidis|confirm', '', true);

		$templateData['checkout_message'] = '';
		if (isset($this->session->data['ifth_message'])) {
			$templateData['checkout_message'] = $this->session->data['ifth_message'];
			unset($this->session->data['ifth_message']);
		}

		return $this->load->view('extension/ifthenpay/payment/cofidisConfirmFormAndBtn', $templateData);
	}



	/**
	 * confirm the order and redirect to the success page
	 * @return void
	 */
	public function confirm(): void
	{
		$this->load->language('extension/ifthenpay/payment/cofidis');

		$orderId = $this->session->data['order_id'] ?? 0;

		$this->load->model('checkout/order');
		$orderInfo = $this->model_checkout_order->getOrder($orderId);

		// validate
		$json = [];

		if (!$orderInfo) {
			$json['error'] = $this->language->get('error_order');
		}


		if (!$this->config->get('payment_cofidis_status') || !isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'cofidis.cofidis') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!isset($orderInfo['total']) || $orderInfo['total'] <= 0) {
			$json['error'] = $this->language->get('error_total');
		}

		if (!$json) {

			// get transaction url
			$cofidisKey = $this->config->get('payment_cofidis_key');
			$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
			$formatedAmount = (string) round($formatedAmount, 2);


			$hash = Utils::generateString(20);

			$returnUrl = $this->getReturnUrl($orderId, $hash);


			$customerData = $this->orderInfoToCustomerData($orderInfo);



			$cofidisPm = new CofidisPayment();
			$result = $cofidisPm->generateUrl($cofidisKey, $returnUrl, $customerData);

			if (!$json && $result['code'] === self::STATUS_SUCCESS) {

				$data = [
					'payment_method' => 'cofidis',
					'order_id' => $orderId,
					'transaction_id' => $result['transaction_id'],
					'hash' => $hash,
					'status' => 'pending',
				];
				$this->session->data['ifth_payment_info'] = $data;

				$this->load->model('extension/ifthenpay/payment/cofidis');

				$this->model_extension_ifthenpay_payment_cofidis->addCofidisRecord($data);

				// log the transaction id
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : COFIDIS init data: ' . json_encode($data));


				$json['redirect'] = $result['payment_url'];
			}
			if (!$result || $result['code'] != self::STATUS_SUCCESS) {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : failed to generate a Cofidis transaction. Response got return code = ' . $result['code'] . ', and message = ' . $result['message']);

				$json['error'] = $this->language->get('error_get_transaction');
				$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function orderInfoToCustomerData(array $orderInfo): array
	{
		$customerData = [];

		$customerData['orderId'] = $orderInfo['order_id'];
		$customerData['amount'] = $orderInfo['total'];
		$customerData['customerName'] = $orderInfo['shipping_firstname'] . " " . $orderInfo['shipping_lastname'];
		$customerData['customerEmail'] = $orderInfo['email'];
		$customerData['customerPhone'] = $orderInfo['telephone'];
		$customerData['deliveryAddress'] = $orderInfo['shipping_address_1'] . " " . $orderInfo['shipping_address_2'];
		$customerData['deliveryZipCode'] = $orderInfo['shipping_postcode'];
		$customerData['deliveryCity'] = $orderInfo['shipping_city'];

		if (isset($orderInfo['billing_address_1']) && isset($orderInfo['billing_address_2'])) {
			$customerData['billingAddress'] = $orderInfo['billing_address_1'] . " " . $orderInfo['billing_address_2'];
		} else {
			$customerData['billingAddress'] = $orderInfo['shipping_address_1'] . " " . $orderInfo['shipping_address_2'];
		}

		if (isset($orderInfo['billing_postcode'])) {
			$customerData['billingZipCode'] = $orderInfo['billing_postcode'];
		} else {
			$customerData['billingZipCode'] = $orderInfo['shipping_postcode'];
		}

		if (isset($orderInfo['billing_city'])) {
			$customerData['billingCity'] = $orderInfo['billing_city'];
		} else {
			$customerData['billingCity'] = $orderInfo['shipping_city'];
		}

		return $customerData;
	}



	public function callback()
	{
		(new CallbackService($this->registry))->HandleFromCofidis($this->request);
	}



	public function returnToStore()
	{
		$success = isset($this->request->get['Success']) ? $this->request->get['Success'] : '';
		$orderId = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : '';
		$hash = isset($this->request->get['hash']) ? $this->request->get['hash'] : '';


		try {
			// exit if does not have parameters
			if ($success == '' || $orderId == '' || $hash == '') {
				throw new \Exception('Invalid returnToStore data.', 5);
			}

			$this->load->model('checkout/order');
			$this->load->model('extension/ifthenpay/payment/cofidis');
			$this->load->language('extension/ifthenpay/payment/cofidis');


			$storedPaymentData = $this->model_extension_ifthenpay_payment_cofidis->getCofidisRecordByOrderIdAndHash($orderId, $hash);
			$cofidisKey = $this->config->get('payment_cofidis_key');


			if ($success === self::INIT_STATUS_TRUE) {
				$this->validateReturnToStore($storedPaymentData, $orderId);

				$this->model_checkout_order->addHistory(
					$storedPaymentData['order_id'],
					$this->config->get('payment_cofidis_pending_status_id'),
					$this->getPaymentDetailsHtml(true, false),
					true
				);


				// update cofidis table record
				$this->model_extension_ifthenpay_payment_cofidis->updateCofidisRecordStatusByOrderIdAndHash($orderId, $hash, 'pending');
				$this->session->data['ifth_payment_info']['status'] = $success;

				// redirect to success page
				$this->response->redirect($this->url->link('checkout/success', 'language=' . $this->config->get('config_language')));

			} else {

				// internal verification of status to verify precise reason for not success
				$status = $this->getCofidisPaymentStatus($cofidisKey, $storedPaymentData['transaction_id']);
				$this->session->data['ifth_payment_info']['statusCode'] = $status;

				// log result of cofidis
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - DEBUG : COFIDIS init data: ' . json_encode(['orderId' => $orderId, 'hash' => $hash, 'status' => $status]));

				$this->model_extension_ifthenpay_payment_cofidis->updateCofidisRecordStatusByOrderIdAndHash($orderId, $hash, $status);
				$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
			}

		} catch (\Throwable $th) {

			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : COFIDIS init data: ' . json_encode(['orderId' => $orderId, 'hash' => $hash, 'status' => 'error', 'error' => $th]));

			$this->response->redirect($this->url->link('checkout/failure', 'language=' . $this->config->get('config_language')));
		}

		return;
	}


	private function getCofidisPaymentStatus(string $key, string $transactionId): string
	{

		$statusArray = [];

		// sleep 5 seconds because error, cancel, not approved may not be present right after returning with error from cofidis
		for ($i = 0; $i < 2; $i++) {

			sleep(5);
			$statusArray = json_decode((new ApiService())->requestCheckCofidisPaymentStatus($key, $transactionId), true);

			if (count($statusArray) > 1) {
				break;
			}
		}

		// return $statusArray;
		if (count($statusArray) < 1) {
			return 'ERROR';
		}

		if ($statusArray[0]['statusCode'] == self::COFIDIS_STATUS_PENDING_INVOICE) {
			return self::COFIDIS_STATUS_PENDING_INVOICE;
		}
		if ($statusArray[0]['statusCode'] == self::COFIDIS_STATUS_NOT_APPROVED) {
			return self::COFIDIS_STATUS_NOT_APPROVED;
		}
		if ($statusArray[0]['statusCode'] == self::COFIDIS_STATUS_TECHNICAL_ERROR) {
			return self::COFIDIS_STATUS_TECHNICAL_ERROR;
		}
		if ($statusArray[0]['statusCode'] == self::COFIDIS_STATUS_CANCELED) {
			foreach ($statusArray as $status) {
				if ($status['statusCode'] == self::COFIDIS_STATUS_TECHNICAL_ERROR) {
					return self::COFIDIS_STATUS_TECHNICAL_ERROR;
				}
			}
			return self::COFIDIS_STATUS_CANCELED;
		}

		return 'ERROR';
	}



	private function validateReturnToStore($storedPaymentData, $orderId): void
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
		$this->load->language('extension/ifthenpay/payment/cofidis');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_cofidis_status')) {
			return;
		}
		// In case the payment method is not cofidis, do nothing
		if (
			!(
				isset($this->session->data['ifth_payment_info']) &&
				isset($this->session->data['ifth_payment_info']['payment_method']) &&
				$this->session->data['ifth_payment_info']['payment_method'] == 'cofidis'
			)
		) {
			return;
		}


		// $tt = strpos($output, 'Failed Payment!');

		if ($this->session->data['ifth_payment_info']['status'] == self::INIT_STATUS_TRUE) {
			$content = $this->getPaymentDetailsHtml(false, false);
			$find = '<div class="text-end">';
			$output = str_replace($find, $content . $find, $output);
		} else if ($this->session->data['ifth_payment_info']['statusCode'] == self::COFIDIS_STATUS_CANCELED) {
			$content = $this->language->get('text_payment_canceled_by_user');
			$find = '<div class="text-end">';
			$output = str_replace($find, $content . $find, $output);
		} else if ($this->session->data['ifth_payment_info']['statusCode'] == self::COFIDIS_STATUS_TECHNICAL_ERROR) {
			$content = $this->language->get('text_payment_technical_error_ocurred');
			$find = '<div class="text-end">';
			$output = str_replace($find, $content . $find, $output);
		} else if ($this->session->data['ifth_payment_info']['statusCode'] == self::COFIDIS_STATUS_NOT_APPROVED) {
			$content = $this->language->get('text_payment_not_approved');
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
			$this->load->language('extension/ifthenpay/payment/cofidis');
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
			'transaction_id' => $paymentInfo['transaction_id'],
			'total' => $formatedAmount,
			'payment_method_icon' => HTTP_SERVER . 'extension/ifthenpay/catalog/view/image/cofidis.png'
		];

		return $this->load->view('extension/ifthenpay/payment/cofidisSuccessInfo', $params);
	}



	/**
	 * generate the success callback url, adding the hash to thi lis
	 * @return string
	 */
	private function getReturnUrl(string $orderId, string $hash)
	{
		return $this->url->link('extension/ifthenpay/payment/cofidis.returnToStore', ['order_id' => $orderId, 'hash' => $hash], true);
	}
}
