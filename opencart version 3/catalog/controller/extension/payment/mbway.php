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
		return $this->load->view('extension/payment/mbway', $data);
	}


	public function resendMbwayNotification()
	{
		$orderId = $this->request->get['orderId'];
		$this->model_extension_payment_mbway->log($orderId, 'Start resending mbway notification');
		$this->load->model('setting/setting');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($orderId);
		$configData =  $this->model_setting_setting->getSetting('payment_' . $this->paymentMethod);
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
				$configData =  $this->model_setting_setting->getSetting('payment_mbway');
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
