<?php
namespace Opencart\Admin\Controller\Extension\ifthenpay\Payment;



require_once DIR_EXTENSION . 'ifthenpay/system/library/Gateway.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';

use Ifthenpay\Gateway;
use Ifthenpay\Utils;
use Ifthenpay\ApiService;

class Multibanco extends \Opencart\System\Engine\Controller
{

	private const PAYMENTMETHOD = 'MULTIBANCO';
	private const DYNAMIC_ENTITY_NAME = 'MB';

	private $logger;
	private $json = [];

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->logger = new \Opencart\System\Library\Log('ifthenpay.log');
	}



	/**
	 * generate the multibanco payment configuration page
	 * this is the page where the merchant can configure the multibanco payment
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
		$data['url_get_sub_entities'] = $this->url->link('extension/ifthenpay/payment/multibanco.ajaxGetSubEntities', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_clear_configuration'] = $this->url->link('extension/ifthenpay/payment/multibanco.ajaxClearConfiguration', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_request_account'] = $this->url->link('extension/ifthenpay/payment/multibanco.ajaxRequestAccount', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_refresh_accounts'] = $this->url->link('extension/ifthenpay/payment/multibanco.ajaxRefreshAccounts', 'user_token=' . $this->session->data['user_token'], true);
		$data['url_test_callback'] = $this->url->link('extension/ifthenpay/payment/multibanco.ajaxTestCallback', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->language('extension/ifthenpay/payment/multibanco');

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
			'href' => $this->url->link('extension/ifthenpay/payment/multibanco', 'user_token=' . $this->session->data['user_token'])
		];
		$data['save'] = $this->url->link('extension/ifthenpay/payment/multibanco.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');


		// header button group
		$data['userManualUrl'] = Utils::MODULE_INSTRUCTIONS_URL;
		$data['supportUrl'] = Utils::IFTHENPAY_SUPPORT_URL;
		$data['requestIfthenpayAccountUrl'] = Utils::REQUEST_IFTHENPAY_ACCOUNT_URL;

		// backofficekey, status, callback activation, and cron related values
		$data['backoffice_key'] = $this->config->get('payment_multibanco_backoffice_key');
		$data['multibanco_status'] = $this->config->get('payment_multibanco_status');
		$data['multibanco_activate_callback'] = $this->config->get('payment_multibanco_activate_callback');

		$data['multibanco_cancel_cronjob'] = $this->config->get('payment_multibanco_cancel_cronjob');
		$urlCronCancel = str_replace(HTTP_SERVER, HTTP_CATALOG, $this->url->link('extension/ifthenpay/payment/multibanco|cronCancelOrder'));
		$data['multibanco_cancel_cronjob_url'] = 'wget -q -O /dev/null "' . $urlCronCancel . '" --read-timeout=5400';

		// entities/keys and deadline related values
		$accounts = $this->config->get('payment_multibanco_accounts');
		$data['multibanco_entity'] = $this->config->get('payment_multibanco_entity');
		$data['multibanco_entities_options'] = Gateway::accountsToEntityOptions(json_decode($accounts, true), $this->language->get('text_multibanco_dynamic_references'));

		$data['multibanco_sub_entity'] = $this->config->get('payment_multibanco_sub_entity');
		$data['multibanco_sub_entities_options'] = Gateway::accountsToSubEntityOptions(json_decode($accounts, true), $data['multibanco_entity']);

		$data['multibanco_dynamic_name'] = self::DYNAMIC_ENTITY_NAME;
		$data['multibanco_deadline'] = $this->config->get('payment_multibanco_deadline');
		$data['multibanco_deadline_options'] = $this->generateDeadlineOptions();

		$data['has_dynamic'] = Gateway::hasDynamicMultibanco(json_decode($accounts, true));
		$data['has_static'] = Gateway::hasStaticMultibanco(json_decode($accounts, true));



		$data['multibanco_min_value'] = $this->config->get('payment_multibanco_min_value');
		$data['multibanco_max_value'] = $this->config->get('payment_multibanco_max_value');

		// title related values
		$title = $this->config->get('payment_multibanco_title');
		$data['multibanco_title'] = $title != '' ? $title : $this->language->get('heading_title');



		// order status related values
		$pendingStatus = $this->config->get('payment_multibanco_pending_status_id');
		$paidStatus = $this->config->get('payment_multibanco_paid_status_id');
		$canceledStatus = $this->config->get('payment_multibanco_canceled_status_id');
		$data['multibanco_pending_status_id'] = $pendingStatus !== '' ? $pendingStatus : 1;
		$data['multibanco_paid_status_id'] = $paidStatus !== '' ? $paidStatus : 2;
		$data['multibanco_canceled_status_id'] = $canceledStatus !== '' ? $canceledStatus : 7;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// geo zone related values
		$data['multibanco_geo_zone_id'] = $this->config->get('payment_multibanco_geo_zone_id');
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['multibanco_sort_order'] = $this->config->get('payment_multibanco_sort_order');

		// callback info related values
		$data['multibanco_url_callback'] = $this->config->get('payment_multibanco_url_callback');
		$data['multibanco_anti_phishing_key'] = $this->config->get('payment_multibanco_anti_phishing_key');

		$data['module_upgrade'] = $this->getUpgradeModuleData();

		// load opencarts header, column_left and footer views
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ifthenpay/payment/multibanco', $data));
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
		$this->load->language('extension/ifthenpay/payment/multibanco');
		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/multibanco')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_multibanco');

		$savedBackofficeKey = $this->config->get('payment_multibanco_backoffice_key');
		if (empty($savedBackofficeKey)) {
			// if backoffice key is not saved, validate it and get accounts from ifthenpay

			$inputtedBackofficeKey = $this->request->post['payment_multibanco_backoffice_key'] ?? '';

			if ($this->validateBackofficeKey($inputtedBackofficeKey)) {
				// if backoffice key format is valid, get accounts from ifthenpay
				$gateway = new Gateway();
				$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($inputtedBackofficeKey, self::PAYMENTMETHOD);
				if ($accounts !== []) {
					$savedSettings['payment_multibanco_accounts'] = json_encode($accounts);
				}
			}

			if (!isset($this->json['error'])) {
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);

				$this->model_setting_setting->editSetting('payment_multibanco', $mergedConfiguration);
				$this->json['success'] = $this->language->get('success_backoffice_key_saved');

			}

		} else {

			if ($this->validate($this->request->post)) {

				$this->activateCallbackIfConditionsAreMet();
				$mergedConfiguration = array_merge($savedSettings, $this->request->post);
				$this->model_setting_setting->editSetting('payment_multibanco', $mergedConfiguration);
			}
		}
		// this redirect reloads admin config page with flash message
		if (!isset($this->json['error'])) {
			$this->session->data['success'] = $this->json['success'] ?? $this->language->get('success_admin_configuration');
			unset($this->json['success']);
			$this->json['redirect'] = $this->url->link('extension/ifthenpay/payment/multibanco', 'user_token=' . $this->session->data['user_token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * activate callback if conditions are met, the conditions are:
	 * - callback is not activated
	 * - callback is activated but the entity or subentity changed
	 * @return void
	 */
	private function activateCallbackIfConditionsAreMet(): void
	{
		$activateCallback = $this->request->post['payment_multibanco_activate_callback'];
		$savedActivateCallback = $this->config->get('payment_multibanco_activate_callback');

		$backofficeKey = $this->config->get('payment_multibanco_backoffice_key');
		$entity = $this->request->post['payment_multibanco_entity'];
		$subEntity = $this->request->post['payment_multibanco_sub_entity'];

		$savedEntity = $this->config->get('payment_multibanco_entity');
		$savedSubEntity = $this->config->get('payment_multibanco_sub_entity');


		if (
			($activateCallback === '1' && $savedActivateCallback !== '1') ||
			($activateCallback === '1' && $savedActivateCallback === '1' && ($entity !== $savedEntity || $subEntity !== $savedSubEntity))
		) {

			$antiPhishingKey = md5((string) rand());
			try {
				// get callback url for catalog
				$urlCallback = $this->url->link('extension/ifthenpay/payment/multibanco|callback', '', true) . Gateway::MULTIBANCO_CALLBACK_STRING;
				$urlCallback = str_replace(HTTP_SERVER, HTTP_CATALOG, $urlCallback);

				$gateway = new Gateway();
				$result = $gateway->requestActivateCallback($backofficeKey, $entity, $subEntity, $antiPhishingKey, $urlCallback);

				if (strpos($result, 'OK') === false) {
					throw new \Exception("error activating callback");
				}

			} catch (\Throwable $th) {
				// if it fails set to activate callback to 0 and set error message
				$this->request->post['payment_multibanco_activate_callback'] = '0';
				$this->json['error'] = $this->language->get('error_callback_activation');
			}

			if (!isset($this->json['error'])) {
				// set $antiPhishingKey and $urlCallback to the request to save to database
				$this->request->post['payment_multibanco_anti_phishing_key'] = $antiPhishingKey;
				$this->request->post['payment_multibanco_url_callback'] = $urlCallback;
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

		if ($formData['payment_multibanco_entity'] == '') {
			$this->json['error'] = $this->language->get('error_entity_empty');
			return false;
		}

		if ($formData['payment_multibanco_sub_entity'] == '') {
			$this->json['error'] = $this->language->get('error_sub_entity_empty');
			return false;
		}

		if ($formData['payment_multibanco_min_value'] !== '') {

			if (!is_numeric($formData['payment_multibanco_min_value'])) {
				$this->json['error'] = $this->language->get('error_min_value_format');
				return false;
			}

			if ($formData['payment_multibanco_min_value'] <= 0) {
				$this->json['error'] = $this->language->get('error_min_value_greater_than_zero');
				return false;
			}
		}

		if ($formData['payment_multibanco_max_value'] !== '') {

			if (!is_numeric($formData['payment_multibanco_max_value'])) {
				$this->json['error'] = $this->language->get('error_max_value_format');
				return false;
			}

			if ($formData['payment_multibanco_max_value'] <= 0) {
				$this->json['error'] = $this->language->get('error_max_value_greater_than_zero');
				return false;
			}
		}

		if ($formData['payment_multibanco_min_value'] !== '' && $formData['payment_multibanco_max_value'] !== '' && $formData['payment_multibanco_min_value'] > $formData['payment_multibanco_max_value']) {
			$this->json['error'] = $this->language->get('error_min_value_greater_than_max_value');
			return false;
		}

		return true;
	}



	private function generateDeadlineOptions(): array
	{
		return [
			['value' => '', 'name' => $this->language->get('text_no_deadline')],
			['value' => '0', 'name' => '0'],
			['value' => '1', 'name' => '1'],
			['value' => '2', 'name' => '2'],
			['value' => '3', 'name' => '3'],
			['value' => '4', 'name' => '4'],
			['value' => '5', 'name' => '5'],
			['value' => '6', 'name' => '6'],
			['value' => '7', 'name' => '7'],
			['value' => '8', 'name' => '8'],
			['value' => '9', 'name' => '9'],
			['value' => '10', 'name' => '10'],
			['value' => '11', 'name' => '11'],
			['value' => '12', 'name' => '12'],
			['value' => '13', 'name' => '13'],
			['value' => '14', 'name' => '14'],
			['value' => '15', 'name' => '15'],
			['value' => '16', 'name' => '16'],
			['value' => '17', 'name' => '17'],
			['value' => '18', 'name' => '18'],
			['value' => '19', 'name' => '19'],
			['value' => '20', 'name' => '20'],
			['value' => '21', 'name' => '21'],
			['value' => '22', 'name' => '22'],
			['value' => '23', 'name' => '23'],
			['value' => '24', 'name' => '24'],
			['value' => '25', 'name' => '25'],
			['value' => '26', 'name' => '26'],
			['value' => '27', 'name' => '27'],
			['value' => '28', 'name' => '28'],
			['value' => '29', 'name' => '29'],
			['value' => '30', 'name' => '30'],
			['value' => '31', 'name' => '31'],
			['value' => '32', 'name' => '32'],
			['value' => '45', 'name' => '45'],
			['value' => '60', 'name' => '60'],
			['value' => '90', 'name' => '90'],
			['value' => '120', 'name' => '120']
		];
	}



	public function install(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/multibanco');

			$this->model_extension_ifthenpay_payment_multibanco->install();
		}
	}



	public function uninstall(): void
	{
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('extension/ifthenpay/payment/multibanco');

			$this->model_extension_ifthenpay_payment_multibanco->uninstall();
		}
	}



	/**
	 * get the array of subentity options for  of sub entities
	 * responds to ajax request
	 * @return void
	 */
	public function ajaxGetSubEntities(): void
	{
		$entity = $this->request->get['entity'] ?? '';
		$subentities = Gateway::accountsToSubEntityOptions(json_decode($this->config->get('payment_multibanco_accounts'), true), $entity);

		$json['success'] = $subentities;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}



	/**
	 * ajax request to manually trigger the callback
	 * @return void
	 */
	public function ajaxTestCallback(): void
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/multibanco')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->get['reference']) || (isset($this->request->get['reference']) && $this->request->get['reference'] == '')) {
			$json['error'] = $this->language->get('error_reference_empty');
		}

		if (filter_var($this->request->get['reference'], FILTER_VALIDATE_INT) !== false || strlen($this->request->get['reference']) != 9) {
			$json['error'] = $this->language->get('error_reference_invalid');
		}

		if (!isset($this->request->get['amount']) || (isset($this->request->get['amount']) && $this->request->get['amount'] == '')) {
			$json['error'] = $this->language->get('error_amount_empty');
		}
		if (!is_numeric($this->request->get['amount']) || $this->request->get['amount'] < 0) {
			$json['error'] = $this->language->get('error_amount_invalid');
		}

		if (!isset($json)) {

			$callbackUrl = $this->config->get('payment_multibanco_url_callback');
			$antiPhishingKey = $this->config->get('payment_multibanco_anti_phishing_key');
			$reference = $this->request->get['reference'];
			$amount = $this->request->get['amount'];


			// populate the url with the variables
			$callbackUrl = str_replace('[CHAVE_ANTI_PHISHING]', $antiPhishingKey, $callbackUrl);
			$callbackUrl = str_replace('[REFERENCIA]', $reference, $callbackUrl);
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
	 * clear the configuration of the extension, deleting all the settings from database related to multibanco
	 * responds to ajax request
	 * @return void
	 */
	public function ajaxClearConfiguration(): void
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/multibanco')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSetting('payment_multibanco');

		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Configuration cleared');

		// TODO: this message being passed both in the json and the session is a bit redundant, but it is currently needed to pass message of success while reloading the page
		$this->json['success'] = $this->language->get('success_clear_configuration');
		$this->session->data['success'] = $this->language->get('success_clear_configuration');

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}



	/**
	 * ajax request to request a multibanco account by sending an email to Ifthenpay
	 * @return void
	 */
	public function ajaxRequestAccount(): void
	{
		$this->load->language('extension/ifthenpay/payment/multibanco');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/multibanco')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');


		$opencartVersion = defined('VERSION') ? VERSION : '';

		$data = [];
		$data['backoffice_key'] = $this->config->get('payment_multibanco_backoffice_key');
		$data['store_email'] = $this->config->get('config_email');
		$data['store_name'] = $this->config->get('config_name');
		$data['payment_method'] = 'Multibanco Referências Dinâmicas';
		$data['ecommerce_platform'] = 'Opencart ' . $opencartVersion;
		$data['module_version'] = Utils::getModuleVersion();
		$data['refresh_accounts_url'] = $this->url->link('extension/ifthenpay/payment/multibanco.refreshAccountsCtrl', 'user_token=' . $this->session->data['user_token']);


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
		$this->load->language('extension/ifthenpay/payment/multibanco');

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/multibanco')) {
			$this->json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_multibanco');

		$savedBackofficeKey = $this->config->get('payment_multibanco_backoffice_key');


		if (empty($savedBackofficeKey)) {
			$json['error'] = 'User account backoffice key is not present';
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			$json['error'] = 'No Multibanco accounts were found for this backoffice key';
		}

		if ($accounts !== []) {
			$savedSettings['payment_multibanco_accounts'] = json_encode($accounts);
		}

		if (!isset($json)) {
			$this->model_setting_setting->editSetting('payment_multibanco', $savedSettings);
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
		$this->load->language('extension/ifthenpay/payment/multibanco');

		$this->load->model('setting/setting');
		$savedSettings = $this->model_setting_setting->getSetting('payment_multibanco');

		$savedBackofficeKey = $this->config->get('payment_multibanco_backoffice_key');

		if (empty($savedBackofficeKey)) {
			http_response_code(400);
			die('User account backoffice key is not present');
		}

		$gateway = new Gateway();
		$accounts = $gateway->getAccountsByBackofficeKeyAndMethod($savedBackofficeKey, self::PAYMENTMETHOD);

		if ($accounts === []) {
			http_response_code(400);
			die('No Multibanco accounts were found for this backoffice key');
		}

		if ($accounts !== []) {
			$savedSettings['payment_multibanco_accounts'] = json_encode($accounts);
		}

		$this->model_setting_setting->editSetting('payment_multibanco', $savedSettings);
		$this->logger->write('IFTHENPAY - ' . self::PAYMENTMETHOD . ' - INFO : Accounts refreshed with success from email request');

		http_response_code(200);
		die('Accounts refreshed with success');
	}

}
