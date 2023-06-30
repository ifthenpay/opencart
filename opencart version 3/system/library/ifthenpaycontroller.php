<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use Ifthenpay\Contracts\Utility\MailInterface;
use GuzzleHttp\Client;
use Ifthenpay\Request\WebService;



require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class IfthenpayController extends Controller
{
	protected $ifthenpayContainer;
	public $error = [];
	protected $paymentMethod;
	protected $dynamicModelName;
	protected $configData;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->dynamicModelName = 'model_extension_payment_' . $this->paymentMethod;
		$this->ifthenpayContainer = new IfthenpayContainer();
		$this->load->language('extension/payment/' . $this->paymentMethod);
		$this->load->model('extension/payment/' . $this->paymentMethod);
		$this->load->model('setting/setting');
		$this->configData = $this->model_setting_setting->getSetting('payment_' . $this->paymentMethod);
	}

	protected function createUpdateAccountUserToken(): string
	{
		// if token already exists, assign it
		$storedToken = $this->config->get('payment_' . $this->paymentMethod . '_updateUserAccountToken');
		$updateUserToken = $storedToken ? $storedToken : md5((string) rand());
		$this->model_setting_setting->editSetting(
			'payment_' . $this->paymentMethod,
			array_merge($this->configData, [
				'payment_' . $this->paymentMethod . '_updateUserAccountToken' => $updateUserToken
			])
		);
		return $updateUserToken;
	}

	public function index()
	{
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$this->load->language('extension/payment/' . $this->paymentMethod);
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/ifthenpay/' . $mix->create('ifthenpayConfig.css'));
		$this->document->addScript('view/javascript/ifthenpay/' . $mix->create('adminConfigPage.js'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty($this->request->post)) {
			try {
				$this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
					->setIfthenpayController($this)
					->setConfigData($this->configData)
					->setPaymentMethod($this->paymentMethod)
					->processForm();
			} catch (\Throwable $th) {
				$this->session->data['error_warning'] = $th->getMessage();
				$this->{$this->dynamicModelName}->log([
					'configData' => $this->configData,
					'errorMessage' => $th->getMessage()
				], 'Error saving payment configuration');
				$this->response->redirect(
					$this->url->link('extension/payment/' . $this->paymentMethod, 'user_token=' .
						$this->session->data['user_token'], true)
				);
			}
		}
		try {
			$paymentFormData = $this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
				->setIfthenpayController($this)
				->setConfigData($this->configData)
				->setPaymentMethod($this->paymentMethod)
				->buildForm();
			$this->{$this->dynamicModelName}->log('', 'Payment Form Loaded with success');
			$this->response->setOutput($this->load->view('extension/payment/' . $this->paymentMethod, $paymentFormData));
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'configData' => $this->configData,
				'errorMessage' => $th->getMessage()
			], 'Error Loading Payment Form');
			$data['error_warning'] = $th->getMessage();
			$this->response->setOutput($this->load->view('extension/payment/' . $this->paymentMethod, $data));
		}
	}

	public function install()
	{
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent(
			'ifthenpayCheckoutSuccessPage' . $this->paymentMethod,
			'catalog/view/common/success/before',
			'extension/payment/' . $this->paymentMethod . '/changeSuccessPage'
		);
		$this->model_setting_event->addEvent(
			'ifthenpayOrderEmailAdd' . $this->paymentMethod,
			'catalog/view/mail/order_add/before',
			'extension/payment/' . $this->paymentMethod . '/changeMailOrderAdd'
		);
		$this->model_setting_event->addEvent(
			'removeBrFromIfthenpayOrderComment' . $this->paymentMethod,
			'admin/view/sale/order_info/after',
			'extension/payment/' . $this->paymentMethod . '/removeBrFromComment'
		);
		$this->model_setting_event->addEvent(
			'ifthenpayApiOrderEdit' . $this->paymentMethod,
			'catalog/controller/api/order/edit/after',
			'extension/payment/' . $this->paymentMethod . '/backofficeOrderEdit'
		);
		$this->model_setting_event->addEvent(
			'ifthenpayApiOrderAdd' . $this->paymentMethod,
			'catalog/controller/api/order/add/after',
			'extension/payment/' . $this->paymentMethod . '/backofficeOrderAdd'
		);
		$this->model_setting_event->addEvent(
			'insertMbwayInputAdminOrderCreate',
			'admin/view/sale/order_form/after',
			'extension/payment/mbway/insertMbwayInputAdminOrderCreate'
		);
		$this->model_setting_event->addEvent(
			'insertResendPaymentDataButton' . $this->paymentMethod,
			'admin/view/sale/order_info/after',
			'extension/payment/' . $this->paymentMethod . '/insertResendPaymentDataButton'
		);
	}

	public function uninstall()
	{
		$this->load->model('setting/event');
		$this->{$this->dynamicModelName}->uninstall($this->paymentMethod);
		$this->model_setting_setting->deleteSetting('payment_' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('ifthenpayCheckoutSuccessPage' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('ifthenpayOrderEmailAdd' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('removeBrFromIfthenpayOrderComment' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('ifthenpayApiOrderEdit' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('ifthenpayApiOrderAdd' . $this->paymentMethod);
		$this->model_setting_event->deleteEventByCode('insertMbwayInputAdminOrderCreate');
		$this->model_setting_event->deleteEventByCode('insertResendPaymentDataButton' . $this->paymentMethod);
	}

	public function requestNewAccount()
	{
		try {
			$mailService = $this->ifthenpayContainer->getIoc()->make(MailInterface::class)
				->setIfthenpayController($this)
				->setPaymentMethod($this->paymentMethod)
				->setUserToken($this->createUpdateAccountUserToken())
				->setSubject('[Opencart] Associar conta ' . $this->paymentMethod . ' ao contrato');

			// add the html template with data to the mail service
			$data = [
				'backofficeKey' => $this->config->get('payment_' . $this->paymentMethod . '_backofficeKey'),
				'customerEmail' => $this->config->get('config_email'),
				'paymentMethod' => $this->paymentMethod,
				'storeName' => $this->config->get('config_name'),
				'ecommercePlatform' => 'Opencart ' . VERSION,
				'updateUserAccountUrl' => $mailService->getUpdateUserAccountUrl(),
			];

			$mailService->setHtmlMessageBody($this->load->view('extension/payment/ifthenpayAccountRequest', $data))
				->setMessageBody("Associar conta " . $this->paymentMethod . " ao contrato \n\n")
				->sendEmail();

			$this->{$this->dynamicModelName}->log('Email requesting new account sent with success');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('request_new_account_success')
			]));
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'errorMessage' => $th->getMessage()
			], 'Error sending email requesting new account');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'error' => $this->language->get('request_new_account_error')
			]));
		}
	}

	/**
	 * Resets a single payment method to the point where it is still installed,
	 * but you are required to input the backoffice key again
	 */
	public function resetUserAccounts()
	{
		try {
			$backofficeKey = $this->configData['payment_' . $this->paymentMethod . '_backofficeKey'] ?? NULL;

			// handle error if there is no backoffice key installed
			if (!$backofficeKey) {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode([
					'success' => null,
					'validationError' => $this->language->get('error_backofficeKey_required')
				]));
				throw new Exception("reset_account_error");
			}
			$gateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
			$gateway->authenticate($backofficeKey);

			// deleting the payment method from database
			$this->model_setting_setting->deleteSetting('payment_' . $this->paymentMethod);
			// log the account reset
			$this->{$this->dynamicModelName}->log('Ifthenpay account reseted with success');

			// prepare and set the response...before redirecting??
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('reset_account_success')
			]));
		} catch (Throwable $e) {
			$this->{$this->dynamicModelName}->log([
				'backofficeKey' => $backofficeKey,
				'errorMessage' => $e->getMessage()
			], 'Error reseting ifthenpay account');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'error' => $this->language->get($e->getMessage())
			]));
		}
	}

	public function removeBrFromComment(&$route, &$data, &$output)
	{
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$output .= '<script src="./view/javascript/ifthenpay/' . $mix->create('adminOrderPage.js') . '" type="text/javascript"></script>';
	}

	public function insertResendPaymentDataButton(&$route, &$data, &$output)
	{

		if (!$this->config->get('payment_' . $this->paymentMethod . '_backofficeKey')) {
			return;
		}


		$url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG);
		$ifthenpayPayment = $this->{$this->dynamicModelName}->getPaymentByOrderId($data['order_id'])->row;
		if (!empty($ifthenpayPayment) && $ifthenpayPayment['status'] === 'pending') {
			$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
			$variablesForJavascript = [
				'catalogUrl' => $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG,
				'ovalSvgUrl' => 'admin/view/image/payment/ifthenpay/oval.svg',
				'paymentMethodLanguage' => [
					'required' => $this->language->get('error_payment_mbway_input_required'),
					'invalid' => $this->language->get('error_payment_mbway_input_invalid'),
					'adminResendMbwayNotification' => $this->language->get('adminResendMbwayNotification'),
					'resendPaymentData' => $this->language->get('resendPaymentData')
				]
			];
			if ($this->paymentMethod === Gateway::MBWAY) {
				$variablesForJavascript['resendMbwayNotificationUrl'] = $url->link(
					'extension/payment/' . Gateway::MBWAY . '/resendMbwayNotification',
					[
						'orderId' => $data['order_id'],
						'mbwayTelemovel' => $ifthenpayPayment['telemovel']
					]
				);
				$variablesForJavascript['mbwaySvgUrl'] = 'admin/view/image/payment/ifthenpay/mbway.svg';
				$output .= '<script type="text/javascript">var phpVariables =' . json_encode($variablesForJavascript) . ';</script>';
			}
			$variablesForJavascript['resendPaymentDataUrl'] = $url->link(
				'extension/payment/' . $this->paymentMethod . '/resendPaymentData',
				[
					'orderId' => $data['order_id']
				],
				true
			);
			$variablesForJavascript['paymentMethod'] = $this->paymentMethod;
			$output .= '<script type="text/javascript"> var phpVariables =' . json_encode($variablesForJavascript) . ';</script>';
			$output .= '<script src="./view/javascript/ifthenpay/' . $mix->create('adminOrderInfoPage.js') .
				'" type="text/javascript"></script>';
		}
	}




	/**
	 * Controller function to validate and build callback url in order to call it using the webservice as a test
	 * will set a response and die
	 *
	 * @return void
	 */
	public function testCallback()
	{
		try {

			$method = $this->paymentMethod;

			$reference = isset($this->request->post['reference']) ? $this->request->post['reference'] : '';
			$amount = isset($this->request->post['amount']) ? $this->request->post['amount'] : '';
			$mbwayTransactionId = isset($this->request->post['mbway_transaction_id']) ? $this->request->post['mbway_transaction_id'] : '';
			$payshopTransactionId = isset($this->request->post['payshop_transaction_id']) ? $this->request->post['payshop_transaction_id'] : '';

			$isCallbackActive = $this->config->get('payment_' . $this->paymentMethod . '_callback_activated') === '1' ? true : false;

			if (!$isCallbackActive) {
				die(json_encode([
					'status' => 'warning',
					'message' => 'Callback is not active'
				]));
			}

			$callbackUrl = $this->config->get('payment_' . $this->paymentMethod . '_urlCallback');

			if (!$callbackUrl) {
				die(json_encode([
					'status' => 'warning',
					'message' => 'Callback url is not set'
				]));
			}

			$callbackUrl .= '&test=true';
			$antiPhishingKey = $this->config->get('payment_' . $this->paymentMethod . '_chaveAntiPhishing');


			$callbackUrl = str_replace('[CHAVE_ANTI_PHISHING]', $antiPhishingKey, $callbackUrl);
			$callbackUrl = str_replace('[VALOR]', $amount, $callbackUrl);



			// set callback url for multibanco
			if ($method === 'multibanco') {

				$entity = $this->config->get('payment_' . $this->paymentMethod . '_entidade');
				$callbackUrl = str_replace('[ENTIDADE]', $entity, $callbackUrl);
				$callbackUrl = str_replace('[REFERENCIA]', $reference, $callbackUrl);
			}

			// set callback url for mbway

			if ($method === 'mbway') {
				$callbackUrl = str_replace('[ID_TRANSACAO]', $mbwayTransactionId, $callbackUrl);
				$callbackUrl = str_replace('[REFERENCIA]', $reference, $callbackUrl);
				$callbackUrl = str_replace('[ESTADO]', 'PAGO', $callbackUrl);
			}

			// set callback url for payshop

			if ($method === 'payshop') {
				$callbackUrl = str_replace('[ID_TRANSACAO]', $payshopTransactionId, $callbackUrl);
				$callbackUrl = str_replace('[REFERENCIA]', $reference, $callbackUrl);
				$callbackUrl = str_replace('[ESTADO]', 'PAGO', $callbackUrl);
			}

			$webservice = new WebService(new Client());

			$request = $webservice->getRequest($callbackUrl);

			$responseBody = $request->getResponseJson();

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($responseBody));

		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'errorMessage' => $th->getMessage()
			], 'Error testing the callback in backoffice');

			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');

			$response = [
				'status' => 'error',
				'message' => $this->module->l('Invalid data, order not found', pathinfo(__FILE__)['filename']),
			];

			$this->response->setOutput(json_encode($response));
		}
	}
}