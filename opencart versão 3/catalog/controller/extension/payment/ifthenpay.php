<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentIfthenpay extends Controller
{
	private $ifthenpayContainer;

	public function index()
	{
		$this->load->language('extension/payment/ifthenpay');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->document->addScript('extension/payment/javascript/ifthenpay/checkoutPaymentMethodPage.js');

		return $this->load->view('extension/payment/ifthenpay', $data);
	}

	public function updateUserAccount(): void
	{
		$this->load->model('extension/payment/ifthenpay');
		$requestUserToken = $this->request->get['user_token'];

        if (!$requestUserToken || $requestUserToken !== $this->config->get('payment_ifthenpay_updateUserAccountToken')) {
            http_response_code(403);
            die('Not Authorized');
        }

        try {
			require('admin/model/setting/setting.php');
         
            $modelAdmin = new ModelSettingSetting( $this->registry );

            $backofficeKey = $this->config->get('payment_ifthenpay_backofficeKey');

            if (!$backofficeKey) {
                die('Backoffice key is required!');
            }
			$this->ifthenpayContainer = new IfthenpayContainer();
            $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

            $ifthenpayGateway->authenticate($backofficeKey);
			$modelAdmin->editSettingValue(
				'payment_ifthenpay', 
				'payment_ifthenpay_userPaymentMethods', 
				serialize($ifthenpayGateway->getPaymentMethods())
			); 
			$modelAdmin->editSettingValue(
				'payment_ifthenpay', 
				'payment_ifthenpay_userAccount', 
				serialize($ifthenpayGateway->getAccount())
			);
			$modelAdmin->editSettingValue(
				'payment_ifthenpay',
				'payment_ifthenpay_updateUserAccountToken',
				''
			);			
            http_response_code(200);
			$this->model_extension_payment_ifthenpay->log('', 'User Account updated with success!');
            die('User Account updated with success!');
        } catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error Updating User Account');
            http_response_code(400);
            die($th->getMessage());
        }
		
	}

	public function method_common(){
        return $this->load->view('extension/payment/ifthenpay', $this->session->data);
    }
    public function method_multibanco(){
        return $this->method_common();
    }

	public function method_mbway(){
        return $this->method_common();
    }

	public function method_payshop(){
        return $this->method_common();
    }

	public function method_ccard(){
        return $this->method_common();
    }

	public function confirm()
	{
		$this->load->model('extension/payment/ifthenpay');
		$json['error'] = 'Error Processing Payment Method';
        $payment = current(explode('/', $this->session->data['payment_method']['code']));
		if ($payment == 'ifthenpay') {
            $parts = explode('_', $this->session->data['payment_method']['code']);
            $paymentMethod = end($parts);		
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        	$configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
			
			try {
				$this->ifthenpayContainer = new IfthenpayContainer();
				$ifthenpayPaymentReturn = $this->ifthenpayContainer
					->getIoc()
					->make(IfthenpayPaymentReturn::class)
					->setOrder($order_info)
					->setIfthenpayController($this)
					->setConfigData($configData)
					->execute();
				$this->session->data['ifthenpayPaymentReturn'] = $ifthenpayPaymentReturn->getTwigVariables()->setStatus('ok')->toArray();
				$this->session->data['ifthenpayPaymentReturn']['paymentLogoUrl'] = $this->config->get('site_url') . 'admin/view/image/payment/' . $paymentMethod . '.png';
				$comment = $this->load->view('extension/payment/ifthenpay_comment_payment_detail', $this->session->data['ifthenpayPaymentReturn']);
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'], 
					$configData['payment_ifthenpay_' . $paymentMethod . '_order_status_id'],
					$comment,
					true,
					true
				);
				$this->session->data['payment_method']['comment'] = $comment;
				$redirect = $ifthenpayPaymentReturn->getRedirectUrl();
				$json['redirect'] = $redirect['redirect'] ?  $redirect['url'] : $this->url->link('checkout/success');
				$json['error'] = '';
				$this->model_extension_payment_ifthenpay->log('', 'Payment Processed with success!');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			} catch (\Throwable $th) {
				$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error Processing Payment');
				$json['error'] = $th->getMessage();
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
			
	}

	public function resendMbwayNotification()
	{
		try {
			$this->load->model('extension/payment/ifthenpay');
			$this->load->language('extension/payment/ifthenpay');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->request->get['orderId']);
			$mbwayTelemovel = $this->request->get['mbwayTelemovel'];
			$totalToPay = $order_info['total'];
			$this->load->model('setting/setting');
			$this->load->model('extension/payment/ifthenpay');
			
			$this->ifthenpayContainer = new IfthenpayContainer();
			$paymentData = $this->ifthenpayContainer
				->getIoc()
				->make(GatewayDataBuilder::class)
				->setMbwayKey($this->model_setting_setting->getSettingValue('payment_ifthenpay_mbway_mbwayKey'))
				->setTelemovel($mbwayTelemovel);

        
            $gatewayResult = $this->ifthenpayContainer
				->getIoc()
				->make(Gateway::class)
				->execute('mbway', $paymentData, strval($order_info['order_id']), strval($totalToPay))->getData();

			$this->model_extension_payment_ifthenpay->updateMbwayIdTransaction($order_info['order_id'], $gatewayResult->idPedido);
			$this->session->data['ifthenpayPaymentReturn']['mbwayResendNotificationSent'] = true;
			$this->session->data['ifthenpayPaymentReturn']['mbwayResend_success'] = $this->language->get('mbwayResend_success');
			$this->session->data['ifthenpayPaymentReturn']['mbwayResend_error'] = '';
			$this->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow'] = true;
			$this->session->data['ifthenpayPaymentReturn']['paymentMethod'] = 'mbway';
			$this->model_extension_payment_ifthenpay->log($this->request->get['orderId'], 'Mbway Notification Resended with Success');
            $this->response->redirect($this->url->link('checkout/success', true));
        } catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($this->request->get['orderId'] . '-' . $th->getMessage(), 'Error Resending Mbway Notification');
			$this->session->data['ifthenpayPaymentReturn']['mbwayResend_success'] = '';
			$this->session->data['ifthenpayPaymentReturn']['mbwayResend_error'] = $this->language->get('mbwayResend_error');
            $this->response->redirect($this->url->link('checkout/success', true));
        }
	}

	public function cancelMbwayOrder()
	{
		try {
			if(isset($this->session->data['ifthenpayPaymentReturn']['orderId']) && $this->session->data['ifthenpayPaymentReturn']['orderId'] !== '') {
				$this->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow'] = false;
				$this->session->data['ifthenpayPaymentReturn']['mbwayResend_success'] = '';
				$this->session->data['ifthenpayPaymentReturn']['mbwayResend_error'] = '';
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
                if ($order_info['order_status_id'] === $this->config->get('payment_ifthenpay_mbway_order_status_complete_id')) {
					$this->response->addHeader('Content-Type: application/json');
					$this->response->setOutput(json_encode([
						'orderStatus' => 'paid'
					]));
                } else {
					$this->response->addHeader('Content-Type: application/json');
					$this->response->setOutput(json_encode([
						'orderStatus' => 'pending'
					]));
                }
			} else {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode([
					'error' => 'orderId is required!'
				]));
			}
        } catch (\Throwable $th) {
            $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'error' => $th->getMessage()
			]));
        }
	}

	//callback
	public function callback(){
		try {
			$this->load->model('extension/payment/ifthenpay');
			$this->ifthenpayContainer = new IfthenpayContainer();
			$this->ifthenpayContainer->getIoc()->make(CallbackStrategy::class)->execute($this->request->get, $this);
			$this->model_extension_payment_ifthenpay->log($this->request->get, 'Callback Processed with Success.');
        } catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($this->request->get . '-' . $th->getMessage(), 'Error Processing callback.');
            $this->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
            $this->session->data['ifthenpayPaymentReturn']['ccard_error'] = $th->getMessage();
        }
	}

	public function cancelMbwayOrderBackend(): void
	{
		$this->load->language('extension/payment/ifthenpay');
		$this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory(
			$this->request->get['order_id'], 
			$this->config->get('payment_ifthenpay_mbway_order_status_canceled_id'),
			$this->language->get('mbwayOrderExpiredCanceled'),
			true,
			true
		);
	}

	public function changePaymentMethodsInView(&$route, &$data, &$output) 
	{
		$paymentMethods = $data['payment_methods'];
		$this->ifthenpayContainer = new IfthenpayContainer();
		$gateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
		if (isset($paymentMethods['ifthenpay'])) {
			foreach ($paymentMethods['ifthenpay']['payments'] as $payment) {
				$paymentMethodName = str_replace('ifthenpay/method_', '', $payment['code']);
				$payment['title'] = $gateway->getPaymentLogo($paymentMethodName);
				$data['payment_methods'][$payment['code']] = $payment;
			}
			unset($data['payment_methods']['ifthenpay']);
			$this->session->data['payment_methods'] = $data['payment_methods'];
		}
	}
	
	public function paymentMethodSave(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['payment_method']['code'])) {
			$this->load->language('extension/payment/ifthenpay');
			$payment = current(explode('/', $this->session->data['payment_method']['code']));
			if ($payment == 'ifthenpay') {
				$parts = explode('_', $this->session->data['payment_method']['code']);
				$paymentMethod = end($parts);
				$this->session->data['payment_method']['title'] = $this->language->get('text_title_' . $paymentMethod);
				$json = [];
				if ($paymentMethod === 'mbway') {
					if (!isset($this->request->cookie['ifthenpayMbwayPhone'])) {
						$json['error']['warning'] = $this->language->get('error_payment_mbway_input_required');
					} else if(strlen($this->request->cookie['ifthenpayMbwayPhone']) < 9) {
						$json['error']['warning'] = $this->language->get('error_payment_mbway_input_invalid');
						$this->response->setOutput(json_encode($json));
					} else if (!preg_match('/^([9][1236])[0-9]*$/', $this->request->cookie['ifthenpayMbwayPhone'])) {
						$json['error']['warning'] = $this->language->get('error_payment_mbway_input_invalid');
						
					}
				}
				
				$this->response->setOutput(json_encode($json));
			}
		}
		
		
	}

	public function changeSuccessPage(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['ifthenpayPaymentReturn'])) {
			$ifthenpayPaymentReturn = $this->session->data['ifthenpayPaymentReturn'];
			if (!$ifthenpayPaymentReturn['orderView']) {
				$this->load->model('setting/setting');
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
				$payment = current(explode('/', $order_info['payment_code']));
				if ($payment == 'ifthenpay') {
					$this->ifthenpayContainer = new IfthenpayContainer();
					$configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
					$ifthenpayPaymentReturn = $this->ifthenpayContainer
					->getIoc()
					->make(IfthenpayOrderDetail::class)
					->setOrder($order_info)
					->setIfthenpayController($this)
					->setConfigData($configData)
					->execute()
					->getTwigVariables()
					->setStatus('ok')
					->toArray();
					$this->session->data['ifthenpayPaymentReturn'] = array_merge($this->session->data['ifthenpayPaymentReturn'], $ifthenpayPaymentReturn);
				}
			} 
			$paymentReturnPanel = $this->load->view('extension/payment/ifthenpay_payment_panel', $this->session->data['ifthenpayPaymentReturn']);
			$this->session->data['ifthenpayPaymentReturn']['paymentReturnPaymentPanel'] = $paymentReturnPanel; 
			$paymentReturn = $this->load->view('extension/payment/ifthenpay_payment_return', $this->session->data['ifthenpayPaymentReturn']);
			$data['text_message'] = $data['text_message'] . '<br>' . $paymentReturn;
			$this->session->data['ifthenpayPaymentReturn']['orderView'] = false;
			$this->session->data['ifthenpayPaymentReturn'] = array_intersect_key($this->session->data['ifthenpayPaymentReturn'], array_flip(['orderId', 'mbwayResendNotificationSent', 'mbwayCountdownShow', 'orderView']));
		}
	}

	public function changeHeaderStyles(&$route, &$data, &$output)
	{
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/checkout') {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/paymentOptions.css');
			$data['styles'] = $this->document->getStyles();
		}
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/success') {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/ifthenpayConfirmPage.css');
			$data['styles'] = $this->document->getStyles();
		}		
	}
	public function changeFooterScripts(&$route, &$data, &$output) {
		$this->document->addScript('catalog/view/javascript/ifthenpay/checkoutPaymentMethodPage.js', 'footer');
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/success' && $this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'mbway') {
			$this->document->addScript('catalog/view/javascript/ifthenpay/mbwayCountdownConfirmPage.js', 'footer');
		}
		$data['scripts'] = $this->document->getScripts('footer');
	}

	public function changeMailOrderAdd(&$route, &$data, &$output) 
	{
		$payment = current(explode('/', $this->session->data['payment_method']['code']));
		if ($payment == 'ifthenpay') {
            $parts = explode('_', $this->session->data['payment_method']['code']);
            $paymentMethod = end($parts);
			$data['payment_method'] = $paymentMethod;
			$this->session->data['ifthenpayPaymentReturn']['paymentMethodLogo'] = HTTPS_SERVER . 'admin/view/image/payment/' . $paymentMethod . '.png';
			$data['comment'] = $this->load->view('mail/ifthenpayPaymentData', $this->session->data['ifthenpayPaymentReturn']);
		}		
	}
}