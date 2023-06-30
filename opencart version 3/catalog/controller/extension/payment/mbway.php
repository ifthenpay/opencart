<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Factory\Payment\PaymentStatusFactory;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;

class ControllerExtensionPaymentMbway extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::MBWAY;
	private const COUNTRY_CODES_PATH = DIR_SYSTEM . 'library/ifthenpay/utility/CountryCodes.json';

	public function index()
	{
		$variablesForJavascript = [
			'paymentMethodLanguage' => [
				'required' => $this->language->get('error_payment_mbway_input_required'),
				'invalid' => $this->language->get('error_payment_mbway_input_invalid'),
				'mbwayPhoneNumber' => $this->language->get('mbwayPhoneNumber'),
			],
			'mbwaySvgUrl' => 'image/payment/ifthenpay/mbway.svg'
		];

		$data['phpVariables'] = json_encode($variablesForJavascript);


		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$data['continue'] = $this->url->link('checkout/success');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['mbwayPhoneNumber'] = $this->language->get('mbwayPhoneNumber');
		$data['mbwayScript'] = 'catalog/view/javascript/ifthenpay/' . $mix->create('checkoutMbwayPage.js');
		$data['mbwayStyle'] = 'catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css');
		$data['mbwayLanguage'] = json_encode([
			'required' => $this->language->get('error_payment_mbway_input_required'),
			'invalid' => $this->language->get('error_payment_mbway_input_invalid')
		]);
		$data['mbwayCountryCodeOptions'] = $this->generateCountryCodeOptions();



		return $this->load->view('extension/payment/mbway', $data);
	}

	/**
	 * loads country codes from json file and returns an array of options for the select input
	 * will select the correct language based on the current language, and if not found, will default to english
	 * @return array
	 */
	private function generateCountryCodeOptions(): array
	{
		$lang = $this->language->get('code');

		// Read JSON file contents
		$jsonData = file_get_contents(self::COUNTRY_CODES_PATH);

		// Parse JSON data into an associative array
		$countryCodes = json_decode($jsonData, true);

		// get correct language key
		$lang = strtoupper($lang);
		$lang = (isset($countryCodes['mobile_prefixes']) && isset($countryCodes['mobile_prefixes'][0]) && isset($countryCodes['mobile_prefixes'][0][$lang])) ? $lang : 'EN';


		$countryCodeOptions = [];
		foreach ($countryCodes['mobile_prefixes'] as $country) {

			if ($country['Ativo'] != 1) {
				continue; // skip this one
			}

			$countryCodeOptions[] = [
				'value' => $country['Indicativo'],
				'name' => $country[$lang] . ' (+' . $country['Indicativo'] . ')'
			];
		}

		return $countryCodeOptions;
	}


	public function resendMbwayNotification()
	{

		if (isset($this->request->get['mbwayTelemovel'])) {
			$this->request->get['mbwayTelemovel'] = str_replace('-', '#', $this->request->get['mbwayTelemovel']);
		}


		$orderId = $this->request->get['orderId'];
		$this->model_extension_payment_mbway->log($orderId, 'Start resending mbway notification');
		$this->load->model('setting/setting');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($orderId);
		$configData = $this->model_setting_setting->getSetting('payment_' . $this->paymentMethod);
		try {
			$ifthenpayPaymentReturn = $this->ifthenpayContainer
				->getIoc()
				->make(IfthenpayPaymentReturn::class)
				->setOrder($order_info)
				->setIfthenpayController($this)
				->setConfigData($configData)
				->execute();
			$this->session->data['ifthenpayPaymentReturn'] = $ifthenpayPaymentReturn->getTwigVariables()->setStatus('ok')->toArray();
			$this->setOrderCommentHistory($order_info['order_id'], $configData['payment_' . $this->paymentMethod . '_order_status_id']);
			$this->model_extension_payment_mbway->log($orderId, 'Mbway notification resend with success.');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('mbwayResend_success')
			]));
		} catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log([
				'requestData' => $this->request->get,
				'orderInfo' => $order_info,
				'configData' => $configData
			], 'Error Processing Payment');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'error' => $this->language->get('mbwayResend_error')
			]));
		}
	}

	public function cancelMbwayOrder()
	{
		try {
			if (isset($this->request->post['orderId']) && $this->request->post['orderId'] !== '') {
				$this->load->model('checkout/order');
				$this->load->model('setting/setting');
				$mbwayPayment = $this->model_extension_payment_mbway->getPaymentByOrderId($this->request->post['orderId'])->row;
				$configData = $this->model_setting_setting->getSetting('payment_mbway');
				$gatewayDataBuilder = $this->ifthenpayContainer->getIoc()->make(GatewayDataBuilder::class);
				$mbwayPaymentStatus = $this->ifthenpayContainer->getIoc()->make(PaymentStatusFactory::class)->setType($this->paymentMethod)->build();
				$gatewayDataBuilder->setMbwayKey($configData['payment_mbway_mbwayKey']);
				$gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);


				$mbwayKey = $configData['payment_mbway_mbwayKey'];
				$requestId = $mbwayPayment['id_transacao'];

				$mbwayPaymentStatus = $mbwayPaymentStatus->getPaymentStatusWithArgs($mbwayKey, $requestId);


				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($mbwayPaymentStatus));

				return;
			} else {
				$this->model_extension_payment_mbway->log([
					'requestData' => $this->request->post,
					'errorMessage' => 'orderId is required!'
				], 'Error cancel mbway order from countdown');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->addHeader('HTTP/1.0 400 Bad Request');
				$this->response->setOutput(json_encode([
					'error' => 'orderId is required!'
				]));
			}
		} catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log([
				'requestData' => $this->request->post,
				'errorMessage' => $th->getMessage()
			], 'Error cancel mbway order from countdown');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'error' => $th->getMessage()
			]));
		}
	}
}