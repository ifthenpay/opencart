<?php
namespace Opencart\Admin\Controller\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/Gateway.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/MbwayPayment.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';

use Ifthenpay\Gateway;
use Ifthenpay\Utils;
use Ifthenpay\MbwayPayment;
use Ifthenpay\ApiService;

class Mbway extends \Opencart\System\Engine\Controller
{

	private const PAYMENTMETHOD = 'MBWAY';
	private const STATUS_PAID = 'paid';
	private const STATUS_REFUNDED = 'refunded';

	private $json = [];
	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}

	/**
	 * generate the mbway payment configuration page
	 * this is the page where the merchant can configure the mbway payment
	 * @return void
	 */
	public function index(): void
	{
		// get flash message from session
		$data['flash_message'] = Utils::getFlashMessageAssocArray($this->session->data);
		// add scripts and styles to admin config page
		$this->document->addStyle('../extension/ifthenpay/admin/view/css/styles.css');
		$this->document->addScript('../extension/ifthenpay/admin/view/javascript/adminConfig.js');

		// add url variables for javascript
		$data['url_clear_configuration'] = $this->url->link('extension/ifthenpay/payment/mbway.ajaxClearConfiguration', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_request_account'] = $this->url->link('extension/ifthenpay/payment/mbway.ajaxRequestAccount', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_refresh_accounts'] = $this->url->link('extension/ifthenpay/payment/mbway.ajaxRefreshAccounts', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_test_callback'] = $this->url->link('extension/ifthenpay/payment/mbway.ajaxTestCallback', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->language('extension/ifthenpay/payment/mbway');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['version'] = Utils::getModuleVersion();

		// default breadcrumbs from opencart
		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/ifthenpay/payment/mbway', 'user_token=' . $this->session->data['user_token'])
		];
		$data['save'] = $this->url->link('extension/ifthenpay/payment/mbway.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		// header button group
		$data['userManualUrl'] = Utils::MODULE_INSTRUCTIONS_URL;
		$data['supportUrl'] = Utils::IFTHENPAY_SUPPORT_URL;
		$data['requestIfthenpayAccountUrl'] = Utils::REQUEST_IFTHENPAY_ACCOUNT_URL;

		// backofficekey, status, callback activation, and cron related values
		$data['backoffice_key'] = $this->config->get('payment_mbway_backoffice_key');

		$data['mbway_status'] = $this->config->get('payment_mbway_status');
		$data['mbway_activate_callback'] = $this->config->get('payment_mbway_activate_callback');
		$data['mbway_show_countdown'] = $this->config->get('payment_mbway_show_countdown');
		$data['mbway_show_refund_form'] = $this->config->get('payment_mbway_show_refund_form');

		$data['mbway_cancel_cronjob'] = $this->config->get('payment_mbway_cancel_cronjob');
		$urlCronCancel = str_replace(HTTP_SERVER, HTTP_CATALOG, $this->url->link('extension/ifthenpay/payment/mbway|cronCancelOrder'));
		$data['mbway_cancel_cronjob_url'] = 'wget -q -O /dev/null "' . $urlCronCancel . '" --read-timeout=5400';

		// entities/keys related values
		$accounts = $this->config->get('payment_mbway_accounts');
		$data['mbway_key'] = $this->config->get('payment_mbway_key');
		$data['mbway_keys_options'] = Gateway::accountsToKeyOptions(json_decode($accounts, true));

		$data['mbway_min_value'] = $this->config->get('payment_mbway_min_value');
		$data['mbway_max_value'] = $this->config->get('payment_mbway_max_value');

		// title related values
		$title = $this->config->get('payment_mbway_title');
		$data['mbway_title'] = $title != '' ? $title : $this->language->get('heading_title');



		// order status related values
		$pendingStatus = $this->config->get('payment_mbway_pending_status_id');
		$paidStatus = $this->config->get('payment_mbway_paid_status_id');
		$canceledStatus = $this->config->get('payment_mbway_canceled_status_id');
		$refundedStatus = $this->config->get('payment_mbway_refunded_status_id');
		$data['mbway_pending_status_id'] = $pendingStatus !== '' ? $pendingStatus : 1;
		$data['mbway_paid_status_id'] = $paidStatus !== '' ? $paidStatus : 2;
		$data['mbway_canceled_status_id'] = $canceledStatus !== '' ? $canceledStatus : 7;
		$data['mbway_refunded_status_id'] = $refundedStatus !== '' ? $refundedStatus : 11;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// geo zone related values
		$data['mbway_geo_zone_id'] = $this->config->get('payment_mbway_geo_zone_id');
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['mbway_sort_order'] = $this->config->get('payment_mbway_sort_order');

		// callback info related values
		$data['mbway_url_callback'] = $this->config->get('payment_mbway_url_callback');
		$data['mbway_anti_phishing_key'] = $this->config->get('payment_mbway_anti_phishing_key');

		$data['module_upgrade'] = $this->getUpgradeModuleData();

		// load opencarts header, column_left and footer views
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ifthenpay/payment/mbway', $data));
	}


	/**
	 * get upgrade module data from file
	 * @return array
	 */
	private function getUpgradeModuleData(): array
	{
		$checkIfModuleUpgradeResult = (new ApiService())->requestCheckModuleUpgrade();

		if (version_compare($checkIfModuleUpgradeResult['version'], Utils::getModuleVersion(false), '>')) {
			return [
				'upgrade' => true,
				'body' => $checkIfModuleUpgradeResult['description'],
				'download' => $checkIfModuleUpgradeResult['download']
			];
		}
		return ['upgrade' => false];
	}



	/**
	 * save payment method configuration
	 * @return void
	 */
	public function save(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');
		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_mbway');

		$savedBackofficeKey = $this->config->get('payment_mbway_backoffice_key');
		if (empty($savedBackofficeKey)) {
			// if backoffice key is not saved, validate it and get accounts from ifthenpay

			$inputtedBackofficeKey = $this->request->post['payment_mbway_backoffice_key'] ?? '';

			if ($this->validateBackofficeKey($inputtedBackofficeKey)) {
				// if backoffice key format is valid, get accounts from ifthenpay
				$gateway = new Gateway();
				$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($inputtedBackofficeKey, self::PAYMENTMETHOD);
				if ($accounts !== []) {
					$savedSettings['payment_mbway_accounts'] = json_encode($accounts);
				}
			}

			if (!isset($this->json['error'])) {
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);

				$this->model_setting_setting->editSetting('payment_mbway', $mergedConfiguration);
				$this->json['success'] = $this->language->get('success_backoffice_key_saved');
			}

		} else {

			if ($this->validate($this->request->post)) {

				$this->activateCallbackIfConditionsAreMet();
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);
				$this->model_setting_setting->editSetting('payment_mbway', $mergedConfiguration);
			}
		}
		// this redirect reloads admin config page with flash message
		if (!isset($this->json['error'])) {
			$this->session->data['success'] = $this->json['success'] ?? $this->language->get('success_admin_configuration');
			unset($this->json['success']);
			$this->json['redirect'] = $this->url->link('extension/ifthenpay/payment/mbway', 'user_token=' . $this->session->data['user_token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * activate callback if conditions are met, the conditions are:
	 * - callback is not activated
	 * - callback is activated but the key changed
	 * @return void
	 */
	private function activateCallbackIfConditionsAreMet(): void
	{
		$activateCallback = $this->request->post['payment_mbway_activate_callback'];
		$savedActivateCallback = $this->config->get('payment_mbway_activate_callback');

		$backofficeKey = $this->config->get('payment_mbway_backoffice_key');
		$key = $this->request->post['payment_mbway_key'];

		$savedKey = $this->config->get('payment_mbway_key');


		if (
			($activateCallback === '1' && $savedActivateCallback !== '1') ||
			($activateCallback === '1' && $savedActivateCallback === '1' && $key !== $savedKey)
		) {

			$antiPhishingKey = md5((string) rand());
			try {
				// get callback url for catalog
				$urlCallback = $this->url->link('extension/ifthenpay/payment/mbway|callback', '', true) . Gateway::MBWAY_CALLBACK_STRING;
				$urlCallback = str_replace(HTTP_SERVER, HTTP_CATALOG, $urlCallback);

				$gateway = new Gateway();
				$result = $gateway->requestActivateCallback($backofficeKey, self::PAYMENTMETHOD, $key, $antiPhishingKey, $urlCallback);

				if (strpos($result, 'OK') === false) {
					throw new \Exception("error activating callback");
				}

			} catch (\Throwable $th) {
				// if it fails set to activate callback to 0 and set error message
				$this->request->post['payment_mbway_activate_callback'] = '0';
				$this->json['error'] = $this->language->get('error_callback_activation');
			}

			if (!isset($this->json['error'])) {
				// set $antiPhishingKey and $urlCallback to the request to save to database
				$this->request->post['payment_mbway_anti_phishing_key'] = $antiPhishingKey;
				$this->request->post['payment_mbway_url_callback'] = $urlCallback;
			}
		}
	}



	/**
	 * validate the backoffice key
	 * sets the error message if the backoffice key is invalid in $this->json['error']
	 * @param string $backofficeKey
	 * @return bool
	 */
	private function validateBackofficeKey($backofficeKey): bool
	{
		if (empty($backofficeKey)) {
			$this->json['error'] = $this->language->get('error_backoffice_key_empty');
			return false;
		}
		if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $backofficeKey)) {
			$this->json['error'] = $this->language->get('error_backoffice_key_format');
			return false;
		}

		return true;
	}



	/**
	 * validate the configuration form except the backoffice key
	 *
	 * @param array $formData
	 * @return bool
	 */
	private function validate($formData): bool
	{

		if ($formData['payment_mbway_key'] == '') {
			$this->json['error'] = $this->language->get('error_key_empty');
			return false;
		}

		if ($formData['payment_mbway_min_value'] !== '' && !is_numeric($formData['payment_mbway_min_value'])) {
			$this->json['error'] = $this->language->get('error_min_value_format');
			return false;
		}

		if ($formData['payment_mbway_max_value'] !== '' && !is_numeric($formData['payment_mbway_max_value'])) {
			$this->json['error'] = $this->language->get('error_max_value_format');
			return false;
		}

		if ($formData['payment_mbway_min_value'] !== '' && $formData['payment_mbway_max_value'] !== '' && $formData['payment_mbway_min_value'] > $formData['payment_mbway_max_value']) {
			$this->json['error'] = $this->language->get('error_min_value_greater_than_max_value');
			return false;
		}

		return true;
	}



	/* -------------------------------------------------------------------------- */
	/*                                   events                                   */
	/* -------------------------------------------------------------------------- */

	/**
	 * event function for rendering refund info and form in the admin sale/order
	 */
	public function eventRenderRefundForm(&$route, &$data, &$output)
	{
		$this->load->model('setting/setting');
		$this->load->language('extension/ifthenpay/payment/mbway');

		// In case the extension is disabled, do nothing
		if (!$this->model_setting_setting->getValue('payment_mbway_status')) {
			return;
		}

		$this->load->model('sale/order');
		$orderInfo = $this->model_sale_order->getOrder((int) $data['order_id']);

		// validate if mbway payment method was used
		if ($orderInfo['payment_method']['code'] !== 'mbway.mbway') {
			return;
		}

		$this->load->model('extension/ifthenpay/payment/mbway');
		$storedPaymentData = $this->model_extension_ifthenpay_payment_mbway->getMbwayRecordByOrderId($data['order_id']);


		if (
			!(
				isset($storedPaymentData['transaction_id']) && $storedPaymentData['transaction_id'] !== '' &&
				isset($storedPaymentData['status'])
			)
		) {
			return;
		}

		$storedRefundRecords = $this->model_extension_ifthenpay_payment_mbway->getAllMbwayRefundRecordsByOrderId($data['order_id']);

		$formatedRefundData = $this->formatRefundRecords($storedRefundRecords, $orderInfo);

		$formatedAmount = $this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false);
		$formatedAmount = (string) number_format($formatedAmount, 2, '.', '');

		$totalAmountRefunded = $this->model_extension_ifthenpay_payment_mbway->getTotalAmountRefunded($data['order_id']);
		if ($totalAmountRefunded !== '') {
			$totalAmountRefunded = (string) number_format((float) $totalAmountRefunded, 2, '.', '');
		} else {
			$totalAmountRefunded = '0.00€';
		}

		$templateData = [
			'order_id' => $storedPaymentData['order_id'],
			'transaction_id' => $storedPaymentData['transaction_id'],
			'payment_status' => $storedPaymentData['status'],
			'payment_status_paid' => self::STATUS_PAID,
			'payment_status_refunded' => self::STATUS_REFUNDED,
			'show_refund_form' => $this->config->get('payment_mbway_show_refund_form'),
			'order_total' => $formatedAmount,
			'refund_data' => $formatedRefundData,
			'total_amount_refunded' => $totalAmountRefunded,
			'url_refund_token_ctrl' => $this->url->link('extension/ifthenpay/payment/mbway.ajaxRequestToken', 'user_token=' . $this->session->data['user_token'], true),
			'url_refund_ctrl' => $this->url->link('extension/ifthenpay/payment/mbway.ajaxRefund', 'user_token=' . $this->session->data['user_token'], true)
		];

		$content = $this->load->view('extension/ifthenpay/payment/refundForm', $templateData);

		$data['tabs'][] = [
			'code' => 'mbway_refund',
			'title' => $this->language->get('text_tab_refund'),
			'content' => $content
		];
	}



	/**
	 * Formats amount value to 2 decimal places for each element of the $records array, and returns an array of refund records like
	 * [
	 * 'amount' => '7.20',
	 * 'description' => 'description of refund',
	 * 'date_added' => 2023-07-10 14:15:41
	 * ]
	 * @param array $records
	 * @return array
	 */
	private function formatRefundRecords(array $records): array
	{
		$refundArray = [];
		foreach ($records as $record) {


			if ($record['amount'] !== '') {
				$refundAmount = (string) number_format($record['amount'], 2, '.', '');

				$refundArray[] = [
					'amount' => $refundAmount,
					'description' => $record['description'],
					'date_added' => $record['date_added']
				];
			}

		}
		return $refundArray;
	}



	public function install(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/mbway');

			$this->model_extension_ifthenpay_payment_mbway->install();
		}


	}



	public function uninstall(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/mbway');

			$this->model_extension_ifthenpay_payment_mbway->uninstall();
		}
	}


	/**
	 * ajax request to manually trigger the callback
	 * @return void
	 */
	public function ajaxTestCallback(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->get['transaction_id']) || (isset($this->request->get['transaction_id']) && $this->request->get['transaction_id'] == '')) {
			$json['error'] = $this->language->get('error_reference_empty');
		}

		if (!isset($this->request->get['amount']) || (isset($this->request->get['amount']) && $this->request->get['amount'] == '')) {
			$json['error'] = $this->language->get('error_amount_empty');
		}
		if (!is_numeric($this->request->get['amount']) || $this->request->get['amount'] < 0) {
			$json['error'] = $this->language->get('error_amount_invalid');
		}

		if (!isset($json)) {

			$callbackUrl = $this->config->get('payment_mbway_url_callback');
			$antiPhishingKey = $this->config->get('payment_mbway_anti_phishing_key');
			$transactionId = $this->request->get['transaction_id'];
			$amount = $this->request->get['amount'];

			// populate the url with the variables
			$callbackUrl = str_replace('[CHAVE_ANTI_PHISHING]', $antiPhishingKey, $callbackUrl);
			$callbackUrl = str_replace('[ID_TRANSACAO]', $transactionId, $callbackUrl);
			$callbackUrl = str_replace('[VALOR]', $amount, $callbackUrl);

			// call the url
			$apiService = new ApiService();
			$result = $apiService->requestTestCallback($callbackUrl);


			if ($result === 'ok') {
				$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Callback test success for ' . $callbackUrl);
				$json['success'] = $this->language->get('success_callback_test');
			} else if (str_contains($result, 'ok - ')) {
				$json['success'] = $this->language->get('warning_callback_test_already_paid');
			} else {
				$json['error'] = $this->language->get('error_callback_test');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	/**
	 * clear the configuration of the extension, deleting all the settings from database related to mbway
	 * @return void
	 */
	public function ajaxClearConfiguration(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSetting('payment_mbway');

		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Configuration cleared');

		// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
		$this->json['success'] = $this->language->get('success_clear_configuration');
		$this->session->data['success'] = $this->language->get('success_clear_configuration');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * ajax request to request a MB WAY account by sending an email to Ifthenpay
	 * @return void
	 */
	public function ajaxRequestAccount(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$opencartVersion = defined('VERSION') ? VERSION : '';

		$data = [];
		$data['backoffice_key'] = $this->config->get('payment_mbway_backoffice_key');
		$data['store_email'] = $this->config->get('config_email');
		$data['store_name'] = $this->config->get('config_name');
		$data['payment_method'] = 'MB WAY';
		$data['ecommerce_platform'] = 'Opencart ' . $opencartVersion;
		$data['module_version'] = Utils::getModuleVersion();
		$data['refresh_accounts_url'] = $this->url->link('extension/ifthenpay/payment/mbway.refreshAccountsCtrl', 'user_token=' . $this->session->data['user_token']);


		// send token to user email
		if ($this->config->get('config_mail_engine')) {
			$mail_option = [
				'parameter' => $this->config->get('config_mail_parameter'),
				'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
				'smtp_username' => $this->config->get('config_mail_smtp_username'),
				'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
				'smtp_port' => $this->config->get('config_mail_smtp_port'),
				'smtp_timeout' => $this->config->get('config_mail_smtp_timeout')
			];

			$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
			$mail->setTo('suporte@ifthenpay.com');
			$mail->setFrom($data['store_email']);
			$mail->setSender($data['store_name']);
			$mail->setSubject('Pedido de conta Ifthenpay');
			$mail->setHtml($this->load->view('extension/ifthenpay/payment/mail/requestAccount', $data));
			$mail->send();
		}


		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Configuration cleared');

		// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
		$this->json['success'] = $this->language->get('success_request_account');
		$this->session->data['success'] = $this->language->get('success_request_account');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * ajax request to refresh the accounts from ifthenpay (used in the admin config page)
	 * @return void
	 */
	public function ajaxRefreshAccounts(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_mbway');

		$savedBackofficeKey = $this->config->get('payment_mbway_backoffice_key');


		if (empty($savedBackofficeKey)) {
			$json['error'] = 'User account backoffice key is not present';
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			$json['error'] = 'No MB WAY accounts were found for this backoffice key';
		}

		if ($accounts !== []) {
			$savedSettings['payment_mbway_accounts'] = json_encode($accounts);
		}

		if (!isset($json)) {
			$this->model_setting_setting->editSetting('payment_mbway', $savedSettings);
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Accounts refreshed with success internally');

			// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
			$json['success'] = $this->language->get('success_refresh_accounts');
			$this->session->data['success'] = $this->language->get('success_refresh_accounts');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/**
	 * refresh the accounts from ifthenpay and save them in the database used in the email request through the link
	 * @return void
	 */
	public function refreshAccountsCtrl()
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_mbway');

		$savedBackofficeKey = $this->config->get('payment_mbway_backoffice_key');

		if (empty($savedBackofficeKey)) {
			http_response_code(400);
			die('User account backoffice key is not present');
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			http_response_code(400);
			die('No MB WAY accounts were found for this backoffice key');
		}

		if ($accounts !== []) {
			$savedSettings['payment_mbway_accounts'] = json_encode($accounts);
		}

		$this->model_setting_setting->editSetting('payment_mbway', $savedSettings);
		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Accounts refreshed with success from email request');

		http_response_code(200);
		die('Accounts refreshed with success');
	}



	/**
	 * validate amount and status, and if expired, send a token by may and store the hash in the session data for later validation
	 * @return void
	 */
	public function ajaxRequestToken(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$json['error'] = $this->language->get('error_permission');
		} else if ($this->request->post['amount'] == '') {
			$json['error'] = $this->language->get('error_refund_amount_required');
		}

		$orderId = $this->request->post['order_id'];
		$amount = $this->request->post['amount'];

		if (!is_numeric($amount)) {
			$json['error'] = $this->language->get('error_refund_amount_invalid');
		} else {

			// validate amount
			$this->load->model('extension/ifthenpay/payment/mbway');
			$totalAmountRefunded = $this->model_extension_ifthenpay_payment_mbway->getTotalAmountRefunded($orderId);


			$this->load->model('sale/order');
			$orderInfo = $this->model_sale_order->getOrder((int) $orderId);
			$orderTotalAmount = round($this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false), 2);

			if ($totalAmountRefunded + $amount > $orderTotalAmount) {
				$json['error'] = $this->language->get('error_refund_amount_exceeds_order_amount');
			}
		}

		if (
			!isset($json) &&
			(!isset($this->session->data['ifth_refund_token_hash']) ||
				!isset($this->session->data['ifth_refund_token_expire_date']) ||
				$this->session->data['ifth_refund_token_expire_date'] < time())
		) {
			// generate token
			$token = sprintf('%05d', mt_rand(0, 99999));

			// hash token
			$tokenHash = password_hash($token, PASSWORD_DEFAULT);

			// save token hash to session
			$this->session->data['ifth_refund_token_hash'] = $tokenHash;
			$this->session->data['ifth_refund_token_expire_date'] = time() + 30 * 60; // 30 minutes

			// send token to user email
			if ($this->config->get('config_mail_engine')) {
				$mail_option = [
					'parameter' => $this->config->get('config_mail_parameter'),
					'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
					'smtp_username' => $this->config->get('config_mail_smtp_username'),
					'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
					'smtp_port' => $this->config->get('config_mail_smtp_port'),
					'smtp_timeout' => $this->config->get('config_mail_smtp_timeout')
				];

				$mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
				$mail->setTo($this->user->getEmail());
				$mail->setFrom('no-reply@mail.com');
				$mail->setSender('ifthenpay');
				$mail->setSubject('Secret Token');
				$mail->setHtml($this->load->view('extension/ifthenpay/payment/mail/refundToken', ['token' => $token]));
				$mail->send();
			}


		}

		if (!isset($json)) {
			$json['success'] = $this->language->get('success_refund');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	/**
	 * Verifies the token and uses refund API to refund the value for that order, changes the order history with refunded status, and updates the ifthenpay database with refunded status
	 * @return void
	 */
	public function ajaxRefund(): void
	{
		$this->load->language('extension/ifthenpay/payment/mbway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/mbway')) {
			$json['error'] = $this->language->get('error_permission');
		} else if (!password_verify($this->request->post['token'], $this->session->data['ifth_refund_token_hash'])) {
			$json['error'] = $this->language->get('error_invalid_token');
		}

		if (!isset($json)) {

			$orderId = $this->request->post['order_id'];
			$backofficeKey = $this->config->get('payment_mbway_backoffice_key');
			$transactionId = $this->request->post['transaction_id'];
			$amount = $this->request->post['amount'];
			$description = $this->request->post['description'];

			// validate amount
			$this->load->model('extension/ifthenpay/payment/mbway');
			$totalAmountRefunded = $this->model_extension_ifthenpay_payment_mbway->getTotalAmountRefunded($orderId);

			$this->load->model('sale/order');
			$orderInfo = $this->model_sale_order->getOrder((int) $orderId);
			$orderTotalAmount = round($this->currency->format($orderInfo['total'], $orderInfo['currency_code'], $orderInfo['currency_value'], false), 2);

			if ($totalAmountRefunded + $amount > $orderTotalAmount) {
				$json['error'] = $this->language->get('error_refund_amount_exceeds_order_amount');
			}

			$mbwayPm = new MbwayPayment();
			$result = $mbwayPm->generateRefund($backofficeKey, $transactionId, $amount);

			if ($result['code'] === '0') {
				$json['error'] = $this->language->get('error_refund');
			}
			if ($result['code'] === '-1') {
				$json['error'] = $this->language->get('error_refund_no_funds');
			}

			if (!isset($json)) {
				// update order history status
				$this->model_extension_ifthenpay_payment_mbway->addOrderHistory($orderId, $this->config->get('payment_mbway_refunded_status_id'), $this->language->get('comment_refunded'));
				// add record to refund table
				$this->model_extension_ifthenpay_payment_mbway->addMbwayRefundRecord($orderId, $amount, $description);
				// update mbway record
				$this->model_extension_ifthenpay_payment_mbway->updateMbwayRecordStatus($orderId, 'refunded');

				$json['success'] = $this->language->get('success_refund');
			}

		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
