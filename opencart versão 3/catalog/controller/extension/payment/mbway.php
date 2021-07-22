<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\MbWayPaymentStatus;
use Ifthenpay\Payments\Data\MbwayCancelOrder;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentMbway extends Controller
{
	private $ifthenpayContainer;

	public function index()
	{
		$this->load->language('extension/payment/mbway');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->document->addScript('extension/payment/javascript/ifthenpay/checkoutMbwayPage.js');

		return $this->load->view('extension/payment/mbway', $data);
	}

	public function updateUserAccount(): void
	{
		$this->load->model('extension/payment/mbway');
		$requestUserToken = $this->request->get['user_token'];

        if (!$requestUserToken || $requestUserToken !== $this->config->get('payment_updateUserAccountToken')) {
            http_response_code(403);
            die('Not Authorized');
        }

        try {
			require('admin/model/setting/setting.php');
         
            $modelAdmin = new ModelSettingSetting( $this->registry );

            $backofficeKey = $this->config->get('payment_mbway_backofficeKey');

            if (!$backofficeKey) {
                die('Backoffice key is required!');
            }
			$this->ifthenpayContainer = new IfthenpayContainer();
            $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

            $ifthenpayGateway->authenticate($backofficeKey);
			$modelAdmin->editSettingValue(
				'payment_mbway', 
				'payment_mbway_userPaymentMethods', 
				serialize($ifthenpayGateway->getPaymentMethods())
			); 
			$modelAdmin->editSettingValue(
				'payment_mbway', 
				'payment_mbway_userAccount', 
				serialize($ifthenpayGateway->getAccount())
			);
			$modelAdmin->editSettingValue(
				'payment_mbway',
				'payment_mbway_updateUserAccountToken',
				''
			);			
            http_response_code(200);
			$this->model_extension_payment_mbway->log('', 'User Account updated with success!');
            die('User Account updated with success!');
        } catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log($th->getMessage(), 'Error Updating User Account');
            http_response_code(400);
            die($th->getMessage());
        }
		
	}

	public function confirm()
	{
		$this->load->model('extension/payment/mbway');
		$json['error'] = 'Error Processing Payment Method';
		if ($this->session->data['payment_method']['code'] == 'mbway') {
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        	$configData =  $this->model_setting_setting->getSetting('payment_mbway');
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
				$this->session->data['ifthenpayPaymentReturn']['paymentLogoUrl'] = $this->config->get('site_url') . 'image/payment/ifthenpay/mbway.svg';
				$comment = $this->load->view('extension/payment/ifthenpay_comment_payment_detail', $this->session->data['ifthenpayPaymentReturn']);
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'], 
					$configData['payment_mbway_order_status_id'],
					$comment,
					true,
					true
				);
				$this->session->data['payment_method']['comment'] = $comment;
				$redirect = $ifthenpayPaymentReturn->getRedirectUrl();
				$json['redirect'] = $redirect['redirect'] ?  $redirect['url'] : $this->url->link('checkout/success');
				$json['error'] = '';
				$this->model_extension_payment_mbway->log('', 'Payment Processed with success!');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			} catch (\Throwable $th) {
				$this->model_extension_payment_mbway->log($th->getMessage(), 'Error Processing Payment');
				$json['error'] = $th->getMessage();
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
			
	}

	public function resendMbwayNotification()
	{
		try {
			$this->load->model('extension/payment/mbway');
			$this->load->language('extension/payment/mbway');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->request->get['orderId']);
			$mbwayTelemovel = $this->request->get['mbwayTelemovel'];
			$totalToPay = $order_info['total'];
			$this->load->model('setting/setting');
			
			$this->ifthenpayContainer = new IfthenpayContainer();
			$paymentData = $this->ifthenpayContainer
				->getIoc()
				->make(GatewayDataBuilder::class)
				->setMbwayKey($this->model_setting_setting->getSettingValue('payment_mbway_mbwayKey'))
				->setTelemovel($mbwayTelemovel);

        
            $gatewayResult = $this->ifthenpayContainer
				->getIoc()
				->make(Gateway::class)
				->execute('mbway', $paymentData, strval($order_info['order_id']), strval($totalToPay))->getData();

			$this->model_extension_payment_mbway->updateMbwayIdTransaction($order_info['order_id'], $gatewayResult->idPedido);
			$this->session->data['ifthenpayPaymentReturn']['mbwayCountdownShow'] = true;
			$this->session->data['ifthenpayPaymentReturn']['paymentMethod'] = 'mbway';
			$this->model_extension_payment_mbway->log($this->request->get['orderId'], 'Mbway Notification Resended with Success');
            $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('mbwayResend_success')
			]));
        } catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log($this->request->get['orderId'] . '-' . $th->getMessage(), 'Error Resending Mbway Notification');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'error' => $this->language->get('mbwayResend_error')
			]));
        }
	}

	public function cancelMbwayOrder()
	{
		try {
			if(isset($this->request->post['orderId']) && $this->request->post['orderId'] !== '') {
				$this->load->model('checkout/order');
				$this->load->model('setting/setting');
				$this->load->model('extension/payment/mbway');
				$mbwayPayment = $this->model_extension_payment_mbway->getPaymentByOrderId($this->request->post['orderId'])->row;
				$configData =  $this->model_setting_setting->getSetting('payment_mbway');
				$order_info = $this->model_checkout_order->getOrder($this->request->post['orderId']);
				$this->ifthenpayContainer = new IfthenpayContainer();
            	$gatewayDataBuilder = $this->ifthenpayContainer->getIoc()->make(GatewayDataBuilder::class);
				$mbwayPaymentStatus = $this->ifthenpayContainer->getIoc()->make(MbWayPaymentStatus::class);
				$gatewayDataBuilder->setMbwayKey($configData['payment_mbway_mbwayKey']);
            	$gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);
                if ($order_info['order_status_id'] === $this->config->get('payment_mbway_order_status_complete_id') && $mbwayPaymentStatus->setData($gatewayDataBuilder)->getPaymentStatus()) {
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
			$this->load->model('extension/payment/mbway');
			$this->ifthenpayContainer = new IfthenpayContainer();
			$this->ifthenpayContainer->getIoc()->make(CallbackStrategy::class)->execute($this->request->get, $this);
			$this->model_extension_payment_mbway->log($this->request->get, 'Callback Processed with Success.');
        } catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log($this->request->get . '-' . $th->getMessage(), 'Error Processing callback.');
            $this->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
            $this->session->data['ifthenpayPaymentReturn']['ccard_error'] = $th->getMessage();
        }
	}

	public function cancelMbwayOrderBackend(): void
	{
		try {
			if($this->config->get('payment_mbway_activate_cancelMbwayOrder') && isset($this->request->get['tk']) && $this->request->get['tk'] === $this->config->get('payment_mbway_cronToken')) {
				$this->ifthenpayContainer = new IfthenpayContainer();
    			$this->ifthenpayContainer->getIoc()->make(MbwayCancelOrder::class)->setIfthenpayController($this)->cancelOrder();
			} else {
				throw new \Exception('Cron token is invalid');
				
			}
		} catch (\Throwable $th) {
			$this->model_extension_payment_mbway->log($this->request->get, $th->getMessage());
			throw $th;
		}
		
	}
		
	public function changeSuccessPage(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['ifthenpayPaymentReturn']) && $this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'mbway') {
			$ifthenpayPaymentReturn = $this->session->data['ifthenpayPaymentReturn'];
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
			if ($order_info['payment_code'] == 'mbway') {
				if (!$ifthenpayPaymentReturn['orderView']) {
					$this->ifthenpayContainer = new IfthenpayContainer();
					$configData =  $this->model_setting_setting->getSetting('payment_mbway');
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
				$this->session->data['ifthenpayPaymentReturn']['spinner'] = $this->load->view('extension/payment/spinner');
				$paymentReturnPanel = $this->load->view('extension/payment/ifthenpay_payment_panel', $this->session->data['ifthenpayPaymentReturn']);
				$this->session->data['ifthenpayPaymentReturn']['paymentReturnPaymentPanel'] = $paymentReturnPanel; 
				$paymentReturn = $this->load->view('extension/payment/ifthenpay_payment_return', $this->session->data['ifthenpayPaymentReturn']);
				$data['text_message'] = $data['text_message'] . '<br>' . $paymentReturn;
				unset($this->session->data['ifthenpayPaymentReturn']);
			}	
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
		$this->document->addScript('catalog/view/javascript/ifthenpay/checkoutMbwayPage.js');
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/success' && isset($this->session->data['ifthenpayPaymentReturn']) && $this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'mbway') {
			$this->document->addScript('catalog/view/javascript/ifthenpay/mbwayCountdownConfirmPage.js');
		}
		$data['scripts'] = $this->document->getScripts();
	}

	public function paymentMethodSave(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] === 'mbway') {
			$this->load->language('extension/payment/ifthenpay');
				$json = [];
				if (!isset($this->request->cookie['ifthenpayMbwayPhone'])) {
					$json['error']['warning'] = $this->language->get('error_payment_mbway_input_required');
				} else if(strlen($this->request->cookie['ifthenpayMbwayPhone']) < 9) {
					$json['error']['warning'] = $this->language->get('error_payment_mbway_input_invalid');
					$this->response->setOutput(json_encode($json));
				} else if (!preg_match('/^([9][1236])[0-9]*$/', $this->request->cookie['ifthenpayMbwayPhone'])) {
					$json['error']['warning'] = $this->language->get('error_payment_mbway_input_invalid');
					
				}
				$this->response->setOutput(json_encode($json));
		}
	}

	public function changeOrderStatusFromWebservice(): void
	{
		$this->load->language('extension/payment/mbway');
		$this->load->model('extension/payment/mbway');
		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory(
			$this->request->post['order_id'], 
			$this->config->get('payment_mbway_order_status_complete_id'),
			$this->language->get('paymentConfirmedSuccess'),
			true,
			true
		);
		$this->model_extension_payment_mbway->log($this->request->get['order_id'], 'Payshop Order Status Change to paid with success');
	}

	public function changeMailOrderAdd(&$route, &$data, &$output) 
	{
		if ($this->session->data['payment_method']['code'] == 'mbway') {
			$this->session->data['ifthenpayPaymentReturn']['paymentMethodLogo'] = $this->config->get('site_url') . 'image/payment/ifthenpay/mbway.svg';
			$data['comment'] = $this->load->view('mail/ifthenpayPaymentData', $this->session->data['ifthenpayPaymentReturn']);
		}		
	}
}