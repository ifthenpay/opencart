<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Factory\Payment\PaymentStatusFactory;

class ControllerExtensionPaymentIfthenpaygateway extends IfthenpayControllerCatalog
{
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;

	public function index()
	{
		$variablesForJavascript = [
			'paymentMethodLanguage' => [
				'required' => $this->language->get('error_payment_ifthenpaygateway_input_required'),
				'invalid' => $this->language->get('error_payment_ifthenpaygateway_input_invalid'),
				'ifthenpaygatewayPhoneNumber' => $this->language->get('ifthenpaygatewayPhoneNumber'),
			],
			'ifthenpaygatewaySvgUrl' => 'image/payment/ifthenpay/ifthenpaygateway.svg'
		];

		$data['phpVariables'] = json_encode($variablesForJavascript);


		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$data['continue'] = $this->url->link('checkout/success');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['ifthenpaygatewayPhoneNumber'] = $this->language->get('ifthenpaygatewayPhoneNumber');
		$data['ifthenpaygatewayScript'] = 'catalog/view/javascript/ifthenpay/' . $mix->create('checkoutIfthenpaygatewayPage.js');
		$data['ifthenpaygatewayStyle'] = 'catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css');
		$data['ifthenpaygatewayLanguage'] = json_encode([
			'required' => $this->language->get('error_payment_ifthenpaygateway_input_required'),
			'invalid' => $this->language->get('error_payment_ifthenpaygateway_input_invalid')
		]);



		return $this->load->view('extension/payment/ifthenpaygateway', $data);
	}


	public function cancelIfthenpaygatewayOrder()
	{
		try {
			if (isset($this->request->post['orderId']) && $this->request->post['orderId'] !== '') {
				$this->load->model('checkout/order');
				$this->load->model('setting/setting');
				$ifthenpaygatewayPayment = $this->model_extension_payment_ifthenpaygateway->getPaymentByOrderId($this->request->post['orderId'])->row;
				$configData = $this->model_setting_setting->getSetting('payment_ifthenpaygateway');
				$gatewayDataBuilder = $this->ifthenpayContainer->getIoc()->make(GatewayDataBuilder::class);
				$ifthenpaygatewayPaymentStatus = $this->ifthenpayContainer->getIoc()->make(PaymentStatusFactory::class)->setType($this->paymentMethod)->build();
				$gatewayDataBuilder->setIfthenpaygatewayKey($configData['payment_ifthenpaygateway_ifthenpaygatewayKey']);
				$gatewayDataBuilder->setIdPedido($ifthenpaygatewayPayment['id_transacao']);


				$ifthenpaygatewayKey = $configData['payment_ifthenpaygateway_ifthenpaygatewayKey'];
				$requestId = $ifthenpaygatewayPayment['id_transacao'];

				$ifthenpaygatewayPaymentStatus = $ifthenpaygatewayPaymentStatus->getPaymentStatusWithArgs($ifthenpaygatewayKey, $requestId);


				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($ifthenpaygatewayPaymentStatus));

				return;
			} else {
				$this->model_extension_payment_ifthenpaygateway->log([
					'requestData' => $this->request->post,
					'errorMessage' => 'orderId is required!'
				], 'Error cancel ifthenpaygateway order from countdown');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->addHeader('HTTP/1.0 400 Bad Request');
				$this->response->setOutput(json_encode([
					'error' => 'orderId is required!'
				]));
			}
		} catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpaygateway->log([
				'requestData' => $this->request->post,
				'errorMessage' => $th->getMessage()
			], 'Error cancel ifthenpaygateway order from countdown');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'error' => $th->getMessage()
			]));
		}
	}
}
