<?php

namespace Opencart\Admin\Controller\Extension\ifthenpay\Payment;



require_once DIR_EXTENSION . 'ifthenpay/system/library/Gateway.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';


use Ifthenpay\Gateway;
use Ifthenpay\Utils;
use Ifthenpay\ApiService;

class Ifthenpaygateway extends \Opencart\System\Engine\Controller
{

	private const PAYMENTMETHOD = 'IFTHENPAYGATEWAY';
	private const STATUS_PAID = 'paid';

	private $json = [];
	private $logger;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}



	/**
	 * generate the ifthenpaygateway payment configuration page
	 * this is the page where the merchant can configure the ifthenpaygateway payment
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
		$data['url_get_gateway_accounts'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxGetGatewayAccounts', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_clear_configuration'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxClearConfiguration', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_request_account'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxRequestAccount', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_refresh_accounts'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxRefreshAccounts', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_test_callback'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxTestCallback', 'user_token=' . $this->session->data['user_token'], true);



		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

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
			'href' => $this->url->link('extension/ifthenpay/payment/ifthenpaygateway', 'user_token=' . $this->session->data['user_token'])
		];
		$data['save'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		// header button group
		$data['userManualUrl'] = Utils::MODULE_INSTRUCTIONS_URL;
		$data['supportUrl'] = Utils::IFTHENPAY_SUPPORT_URL;
		$data['requestIfthenpayAccountUrl'] = Utils::REQUEST_IFTHENPAY_ACCOUNT_URL;

		// backofficekey, status, and cron related values
		$data['backoffice_key'] = $this->config->get('payment_ifthenpaygateway_backoffice_key');

		$data['ifthenpaygateway_status'] = $this->config->get('payment_ifthenpaygateway_status');
		$data['ifthenpaygateway_activate_callback'] = $this->config->get('payment_ifthenpaygateway_activate_callback');
		// ifthenpaygateway_anti_phishing_key and ifthenpaygateway_url_callback and ifthenpaygateway_activate_callback

		// entities/keys related values
		$accounts = $this->config->get('payment_ifthenpaygateway_accounts');

		// currently selected
		$data['ifthenpaygateway_key'] = $this->config->get('payment_ifthenpaygateway_key');
		// array of selectable keys
		$data['ifthenpaygateway_keys_options'] = Gateway::accountsToGatewayKeyOptions(json_decode($accounts, true));



		$data['ifthenpaygateway_cancel_cronjob'] = $this->config->get('payment_ifthenpaygateway_cancel_cronjob');
		$urlCronCancel = str_replace(HTTP_SERVER, HTTP_CATALOG, $this->url->link('extension/ifthenpay/payment/ifthenpaygateway|cronCancelOrder'));
		$data['ifthenpaygateway_cancel_cronjob_url'] = 'wget -q -O /dev/null "' . $urlCronCancel . '" --read-timeout=5400';



		$gateway = new Gateway();
		$gatewayAccountsArray = $gateway->getPaymentMethodsDataByBackofficeKeyAndGatewayKey($data['backoffice_key'], $data['ifthenpaygateway_key']);

		$storedIfthenpayGatewayMethodsArray = $this->config->get('payment_ifthenpaygateway_methods');
		if ($storedIfthenpayGatewayMethodsArray == '') {
			$storedIfthenpayGatewayMethodsArray = [];
		}

		if (!empty($data['ifthenpaygateway_key'])) {
			// generate the paymentMethods available in the gateway_key
			$gatewayKeySettings = $this->getGatewayKeySettingsFromConfig($data['ifthenpaygateway_key']);
			$data['ifthenpaygateway_method_accounts_html'] = $this->generateIfthenpaygatewayPaymentMethodsHtml($gatewayAccountsArray, $gatewayKeySettings, $storedIfthenpayGatewayMethodsArray);
		}

		$storedDefaultPaymentMethod = $this->config->get('payment_ifthenpaygateway_default_method');
		$selectedDefaultHtml = $this->generateSelectedDefaultHtml($gatewayAccountsArray, $storedIfthenpayGatewayMethodsArray, $storedDefaultPaymentMethod);

		$data['ifthenpaygateway_selected_default_html'] = $selectedDefaultHtml;

		$data['ifthenpaygateway_deadline'] = $this->config->get('payment_ifthenpaygateway_deadline');

		$data['ifthenpaygateway_min_value'] = $this->config->get('payment_ifthenpaygateway_min_value');
		$data['ifthenpaygateway_max_value'] = $this->config->get('payment_ifthenpaygateway_max_value');

		// title related values
		$title = $this->config->get('payment_ifthenpaygateway_title');
		$data['ifthenpaygateway_title'] = $title != '' ? $title : $this->language->get('heading_title');



		// order status related values
		$pendingStatus = $this->config->get('payment_ifthenpaygateway_pending_status_id');
		$paidStatus = $this->config->get('payment_ifthenpaygateway_paid_status_id');
		$canceledStatus = $this->config->get('payment_ifthenpaygateway_canceled_status_id');
		$data['ifthenpaygateway_pending_status_id'] = $pendingStatus !== '' ? $pendingStatus : 1;
		$data['ifthenpaygateway_paid_status_id'] = $paidStatus !== '' ? $paidStatus : 2;
		$data['ifthenpaygateway_canceled_status_id'] = $canceledStatus !== '' ? $canceledStatus : 7;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// geo zone related values
		$data['ifthenpaygateway_geo_zone_id'] = $this->config->get('payment_ifthenpaygateway_geo_zone_id');
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['ifthenpaygateway_sort_order'] = $this->config->get('payment_ifthenpaygateway_sort_order');


		// callback info related values
		$data['ifthenpaygateway_url_callback'] = $this->config->get('payment_ifthenpaygateway_url_callback');
		$data['ifthenpaygateway_anti_phishing_key'] = $this->config->get('payment_ifthenpaygateway_anti_phishing_key');


		$data['module_upgrade'] = $this->getUpgradeModuleData();

		// load opencarts header, column_left and footer views
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ifthenpay/payment/ifthenpaygateway', $data));
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
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');
		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_ifthenpaygateway');

		$savedBackofficeKey = $this->config->get('payment_ifthenpaygateway_backoffice_key');
		if (empty($savedBackofficeKey)) {
			// if backoffice key is not saved, validate it and get accounts from ifthenpay

			$inputtedBackofficeKey = $this->request->post['payment_ifthenpaygateway_backoffice_key'] ?? '';

			if ($this->validateBackofficeKey($inputtedBackofficeKey)) {
				// if backoffice key format is valid, get accounts from ifthenpay
				$gateway = new Gateway();
				$accounts = $gateway->getGatewayKeysByBackofficeKey($inputtedBackofficeKey);
				if ($accounts !== []) {
					$savedSettings['payment_ifthenpaygateway_accounts'] = json_encode($accounts);
				}
			}

			if (!isset($this->json['error'])) {
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);
				$mergedConfiguration['payment_ifthenpaygateway_transaction_token'] = Utils::generateString(20);

				$this->model_setting_setting->editSetting('payment_ifthenpaygateway', $mergedConfiguration);
				$this->json['success'] = $this->language->get('success_backoffice_key_saved');
			}
		} else {

			if ($this->validate($this->request->post)) {
				$this->activateCallbackIfConditionsAreMet($this->request->post);
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);
				$this->model_setting_setting->editSetting('payment_ifthenpaygateway', $mergedConfiguration);
			}
		}
		// this redirect reloads admin config page with flash message
		if (!isset($this->json['error'])) {
			$this->session->data['success'] = $this->json['success'] ?? $this->language->get('success_admin_configuration');
			unset($this->json['success']);
			$this->json['redirect'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway', 'user_token=' . $this->session->data['user_token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	private function activateCallbackIfConditionsAreMet(): void
	{
		try {
			$backofficeKey = $this->config->get('payment_ifthenpaygateway_backoffice_key');
			$activateCallback = $this->request->post['payment_ifthenpaygateway_activate_callback'];
			$savedActivateCallback = $this->config->get('payment_ifthenpaygateway_activate_callback');

			$paymentMethods = $this->request->post['payment_ifthenpaygateway_methods'];
			$savedPaymentMethods = $this->config->get('payment_ifthenpaygateway_methods');

			$antiPhishingKey = $this->config->get('payment_ifthenpaygateway_anti_phishing_key');
			$antiPhishingKey = $antiPhishingKey == '' ? md5((string) rand()) : $antiPhishingKey;

			$paymentMethodsToActivate = [];
			if ($activateCallback === '1' && $savedActivateCallback !== '1') {
				// activate all
				$paymentMethodsToActivate = array_filter($paymentMethods, fn ($item) => $item['is_active'] === '1');
				// reset $antiPhishingKey
				$antiPhishingKey = md5((string) rand());
			} else if ($activateCallback === '1' && $savedActivateCallback === '1') {
				// activate only the selected payment methods, will be empty if no changes to selected payment methods
				$paymentMethodsToActivate = $this->paymentMethodsToActivateCallback($paymentMethods, $savedPaymentMethods);
			}

			if (empty($paymentMethodsToActivate)) {
				return;
			}

			// get callback url for catalog
			$urlCallback = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway|callback', '', true) . Gateway::IFTHENPAYGATEWAY_CALLBACK_STRING;
			$urlCallback = str_replace(HTTP_SERVER, HTTP_CATALOG, $urlCallback);
			$urlCallback = str_replace('{ec}', defined('VERSION') ? VERSION : 'unknown', $urlCallback);
			$urlCallback = str_replace('{mv}', Utils::getModuleVersion(), $urlCallback);


			foreach ($paymentMethodsToActivate as $key => $settings) {

				$paymentMethodEntitySubentity = explode('|', $settings['account']);

				$paymentMethodEntity = trim($paymentMethodEntitySubentity[0]);
				$paymentMethodSubentity = trim($paymentMethodEntitySubentity[1]);

				$gateway = new Gateway();
				$result = $gateway->requestActivateCallback($backofficeKey, $paymentMethodEntity, $paymentMethodSubentity, $antiPhishingKey, $urlCallback);

				if (strpos($result, 'OK') === false) {
					throw new \Exception("error activating callback");
				}
			}

			if (!isset($this->json['error'])) {
				// set $antiPhishingKey and $urlCallback to the request to save to database
				$this->request->post['payment_ifthenpaygateway_anti_phishing_key'] = $antiPhishingKey;
				$this->request->post['payment_ifthenpaygateway_url_callback'] = $urlCallback;
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - ERROR : ' . $th->getMessage());

			// if it fails set to activate callback to 0 and set error message
			$this->request->post['payment_ifthenpaygateway_activate_callback'] = '0';
			$this->json['error'] = $this->language->get('error_callback_activation');
		}
	}


	private function paymentMethodsToActivateCallback(array $PaymentMethods, array $savedPaymentMethods): array
	{

		$paymentMethodsToActivate = [];

		foreach ($PaymentMethods as $key => $settings) {
			if (!isset($savedPaymentMethods[$key])) {
				throw new \Exception("Invalid payment method", 9);
			}

			if (isset($savedPaymentMethods[$key]) && array_diff_assoc($settings, $savedPaymentMethods[$key]) && $settings['is_active'] === '1') {

				$paymentMethodsToActivate[$key] = $settings;
			}
		}
		return $paymentMethodsToActivate;
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

		if ($formData['payment_ifthenpaygateway_key'] == '') {
			$this->json['error'] = $this->language->get('error_key_empty');
			return false;
		}

		if ($formData['payment_ifthenpaygateway_min_value'] !== '' && !is_numeric($formData['payment_ifthenpaygateway_min_value'])) {
			$this->json['error'] = $this->language->get('error_min_value_format');
			return false;
		}

		if ($formData['payment_ifthenpaygateway_max_value'] !== '' && !is_numeric($formData['payment_ifthenpaygateway_max_value'])) {
			$this->json['error'] = $this->language->get('error_max_value_format');
			return false;
		}

		if ($formData['payment_ifthenpaygateway_min_value'] !== '' && $formData['payment_ifthenpaygateway_max_value'] !== '' && $formData['payment_ifthenpaygateway_min_value'] > $formData['payment_ifthenpaygateway_max_value']) {
			$this->json['error'] = $this->language->get('error_min_value_greater_than_max_value');
			return false;
		}

		return true;
	}



	public function install(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

			$this->model_extension_ifthenpay_payment_ifthenpaygateway->install();
		}
	}



	public function uninstall(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/ifthenpaygateway');

			$this->model_extension_ifthenpay_payment_ifthenpaygateway->uninstall();
		}
	}



	public function ajaxTestCallback(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->get['order_id']) || (isset($this->request->get['order_id']) && $this->request->get['order_id'] == '')) {
			$json['error'] = $this->language->get('error_order_id_empty');
		}

		if (!isset($this->request->get['amount']) || (isset($this->request->get['amount']) && $this->request->get['amount'] == '')) {
			$json['error'] = $this->language->get('error_amount_empty');
		}
		if (!is_numeric($this->request->get['amount']) || $this->request->get['amount'] < 0) {
			$json['error'] = $this->language->get('error_amount_invalid');
		}

		if (!isset($json)) {

			$callbackUrl = $this->config->get('payment_ifthenpaygateway_url_callback');
			$antiPhishingKey = $this->config->get('payment_ifthenpaygateway_anti_phishing_key');
			$orderId = $this->request->get['order_id'];
			$amount = $this->request->get['amount'];

			// populate the url with the variables
			$callbackUrl = str_replace('[ANTI_PHISHING_KEY]', $antiPhishingKey, $callbackUrl);
			$callbackUrl = str_replace('[ORDER_ID]', $orderId, $callbackUrl);
			$callbackUrl = str_replace('[AMOUNT]', $amount, $callbackUrl);

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



	private function getGatewayKeySettingsFromConfig(string $gatewayKey): array
	{
		$ifthenpaygatewayAccounts = json_decode($this->config->get('payment_ifthenpaygateway_accounts'), true) ?? [];

		$gatewayKeySettings = array_filter($ifthenpaygatewayAccounts, function ($item) use ($gatewayKey) {
			if ($item['GatewayKey'] === $gatewayKey) {
				return true;
			}
		});

		$gatewayKeySettings = reset($gatewayKeySettings);
		return $gatewayKeySettings;
	}



	public function ajaxGetGatewayAccounts(): void
	{
		$gatewayKey = $this->request->get['gateway_key'] ?? '';
		$backofficeKey = $this->config->get('payment_ifthenpaygateway_backoffice_key');

		if ($gatewayKey !== '') {
			$gateway = new Gateway();
			$gatewayAccountsArray = $gateway->getPaymentMethodsDataByBackofficeKeyAndGatewayKey($backofficeKey, $gatewayKey);

			$gatewayKeySettings = $this->getGatewayKeySettingsFromConfig($gatewayKey);

			$paymentMethodsHtml = $this->generateIfthenpaygatewayPaymentMethodsHtml($gatewayAccountsArray, $gatewayKeySettings, []);
			$json['payment_methods_html'] = $paymentMethodsHtml;

			$defaultSelectedHtml = $this->generateSelectedDefaultHtml($gatewayAccountsArray, [], '0');
			$json['default_selected_html'] = $defaultSelectedHtml;
		} else {
			$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');
			$json['payment_methods_html'] = '<p class="mb-0 mt-2">' . $this->language->get('entry_plh_methods') . '</p>';
		}

		$json['success'] = true;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	private function isGatewayKeyStatic(array $gatewayKeySettings): bool
	{
		return $gatewayKeySettings['Tipo'] === 'Estáticas';
	}




	private function generateSelectedDefaultHtml(array $paymentMethodGroupArray, array $storedMethods = [], string $storedDefaultPaymentMethod): string
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		$html = '';

		$index = 0;
		$accountOptions = '<option value="' . $index . '">' . $this->language->get('entry_plh_method_selected_default_none') . '</option>';

		foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
			$index++;

			$isDisabled = '';
			if (isset($storedMethods[$paymentMethodGroup['Entity']]['is_active'])) {
				$isDisabled = $storedMethods[$paymentMethodGroup['Entity']]['is_active'] ? '' : 'disabled';
			}

			$selectedStr = $index == $storedDefaultPaymentMethod ? 'selected' : '';

			$accountOptions .= <<<HTML
			'<option value="{$index}" data-method="{$paymentMethodGroup['Entity']}" {$selectedStr} {$isDisabled}>{$paymentMethodGroup['Method']}</option>'
			HTML;
		}


		$html = <<<HTML
		<select name="payment_ifthenpaygateway_default_method" id="payment_ifthenpaygateway_default" class="form-select">
			{$accountOptions}
		</select>
		HTML;


		return $html;
	}


	private function generateIfthenpaygatewayPaymentMethodsHtml(array $paymentMethodGroupArray, array $gatewayKeySettings, array $storedMethods = []): string
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		$isStaticGatewayKey = $this->isGatewayKeyStatic($gatewayKeySettings);
		$html = '';

		foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
			$accountOptions = '';

			$entity = $paymentMethodGroup['Entity']; // unique identifier code like 'MB' or 'MULTIBANCO'


			foreach ($paymentMethodGroup['accounts'] as $account) {

				// set selected payment method key
				$selectedStr = '';
				if (isset($storedMethods[$entity]['account'])) {
					$selectedStr = $account['Conta'] == $storedMethods[$entity]['account'] ? 'selected' : '';
				}


				$accountOptions .= <<<HTML
				'<option value="{$account['Conta']}" {$selectedStr}>{$account['Alias']}</option>'
				HTML;
			}

			$isDisabled = $isStaticGatewayKey ? 'disabled' : '';

			// defaults:
			// if dynamic all accounts with options are checked
			// if static all accounts with options are checked but disabled

			if ($isStaticGatewayKey) {
				$isChecked = 'checked';
				$isDisabled = 'disabled';
				if ($accountOptions === '') {
					$isChecked = '';
				}
			} else {
				$isChecked = 'checked';
				$isDisabled = '';
				if ($accountOptions === '') {
					$isChecked = '';
					$isDisabled = 'disabled';
				}
			}



			if ($accountOptions !== '') {
				// show method account select
				$selectOrActivate = <<<HTML
				<select {$isDisabled} name="payment_ifthenpaygateway_methods[{$paymentMethodGroup['Entity']}][account]" id="{$paymentMethodGroup['Entity']}" class="form-select method_account">
					{$accountOptions}
				</select>
				HTML;

				// if the isActive is saved use it
				$isChecked = (isset($storedMethods[$entity]['is_active']) && $storedMethods[$entity]['is_active'] == '1') || !$storedMethods ? 'checked' : '';
			} else {
				// show request button
				$url = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxRequestIfthenpaygatewayMethod', 'user_token=' . $this->session->data['user_token'], true);
				$selectOrActivate = <<<HTML
				<button type="button" title="request payment method" class="btn btn-primary min_w_300 request_ifthenpaygateway_method" data-method="{$paymentMethodGroup['Entity']}" data-url="{$url}">
				{$this->language->get('text_request_ifthenpaygateway_method_btn')} {$paymentMethodGroup['Method']}
					<i class="fa-solid fa-paper-plane"></i>
				</button>
				HTML;
			}
			$data['url_request_ifthenpaygateway_method'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.ajaxRequestIfthenpaygatewayMethod', 'user_token=' . $this->session->data['user_token'], true);

			$html .= <<<HTML
			<div class="method_line row align-items-center mb-3 border-0">
				<div class="col-12 col-sm-auto d-flex align-items-center mb-2 mb-sm-0">
					<div class="form-check form-switch form-switch-lg">
						<input type="hidden" name="payment_ifthenpaygateway_methods[{$paymentMethodGroup['Entity']}][is_active]" value="0"/>
						<input type="checkbox" name="payment_ifthenpaygateway_methods[{$paymentMethodGroup['Entity']}][is_active]" value="1" class="form-check-input is_active" {$isChecked} {$isDisabled} data-method="{$paymentMethodGroup['Entity']}" onchange="updateDefaultSelect(this)"/>
					</div>
					<div class="payment_logo_div ml-3">
						<img src="{$paymentMethodGroup['ImageUrl']}" alt="{$paymentMethodGroup['Method']}" class="img-fluid"/>
					</div>
				</div>
				<div class="col-12 col-md-6">
				{$selectOrActivate}
				</div>
			</div>
			HTML;
		}

		return $html;
	}



	/**
	 * clear the configuration of the extension, deleting all the settings from database related to ifthenpaygateway
	 * @return void
	 */
	public function ajaxClearConfiguration(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSetting('payment_ifthenpaygateway');

		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Configuration cleared');

		// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
		$this->json['success'] = $this->language->get('success_clear_configuration');
		$this->session->data['success'] = $this->language->get('success_clear_configuration');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * send a request to ifthenpay to create a credit card account
	 * @return void
	 */
	public function ajaxRequestAccount(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$opencartVersion = defined('VERSION') ? VERSION : '';

		$data = [];
		$data['backoffice_key'] = $this->config->get('payment_ifthenpaygateway_backoffice_key');
		$data['store_email'] = $this->config->get('config_email');
		$data['store_name'] = $this->config->get('config_name');
		$data['payment_method'] = 'Cartão de Crédito';
		$data['ecommerce_platform'] = 'Opencart ' . $opencartVersion;
		$data['module_version'] = Utils::getModuleVersion();
		$data['refresh_accounts_url'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.refreshAccountsCtrl', 'user_token=' . $this->session->data['user_token']);


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



	public function ajaxRequestIfthenpaygatewayMethod(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$paymentMethod = $this->request->get['payment_method'] ?? '';
		$gatewayKey = $this->request->get['gateway_key'] ?? '';
		if ($paymentMethod === '' || $gatewayKey === '') {
			$this->json['error'] = $this->language->get('error_invalid_request');
		}


		$opencartVersion = defined('VERSION') ? VERSION : '';

		$data = [];
		$data['backoffice_key'] = $this->config->get('payment_ifthenpaygateway_backoffice_key');
		$data['gateway_key'] = $gatewayKey;
		$data['store_email'] = $this->config->get('config_email');
		$data['store_name'] = $this->config->get('config_name');
		$data['payment_method'] = $paymentMethod;
		$data['ecommerce_platform'] = 'Opencart ' . $opencartVersion;
		$data['module_version'] = Utils::getModuleVersion();
		$data['refresh_accounts_url'] = $this->url->link('extension/ifthenpay/payment/ifthenpaygateway.refreshAccountsCtrl', 'user_token=' . $this->session->data['user_token']);


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
			$mail->setSubject($paymentMethod . ': Ativação de Serviço');
			$mail->setHtml($this->load->view('extension/ifthenpay/payment/mail/requestGatewayMethod', $data));
			$mail->send();
		}


		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Configuration cleared');

		// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
		$this->json['success'] = $this->language->get('success_request_gateway_method');
		$this->session->data['success'] = $this->language->get('success_request_gateway_method');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * refresh the accounts from ifthenpay and save them in the database (used in the admin config page)
	 * @return void
	 */
	public function ajaxRefreshAccounts(): void
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/ifthenpaygateway')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_ifthenpaygateway');

		$savedBackofficeKey = $this->config->get('payment_ifthenpaygateway_backoffice_key');


		if (empty($savedBackofficeKey)) {
			$json['error'] = 'User account backoffice key is not present';
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			$json['error'] = 'No Ifthenpay Gateway accounts were found for this backoffice key';
		}

		if ($accounts !== []) {
			$savedSettings['payment_ifthenpaygateway_accounts'] = json_encode($accounts);
		}

		if (!isset($json)) {
			$this->model_setting_setting->editSetting('payment_ifthenpaygateway', $savedSettings);
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
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_ifthenpaygateway');

		$savedBackofficeKey = $this->config->get('payment_ifthenpaygateway_backoffice_key');

		if (empty($savedBackofficeKey)) {
			http_response_code(400);
			die('User account backoffice key is not present');
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			http_response_code(400);
			die('No Ifthenpay Gateway accounts were found for this backoffice key');
		}

		if ($accounts !== []) {
			$savedSettings['payment_ifthenpaygateway_accounts'] = json_encode($accounts);
		}

		$this->model_setting_setting->editSetting('payment_ifthenpaygateway', $savedSettings);
		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Accounts refreshed with success from email request');

		http_response_code(200);
		die('Accounts refreshed with success');
	}
}
