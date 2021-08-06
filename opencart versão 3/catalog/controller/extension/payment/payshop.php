<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentPayshop extends Controller
{
	private $ifthenpayContainer;

	public function index()
	{
		$this->ifthenpayContainer = new IfthenpayContainer();
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$this->load->language('extension/payment/payshop');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$scriptVersion = $mix->create('checkoutPayshopPage.js');
		$this->document->addScript('extension/payment/javascript/ifthenpay/' . $scriptVersion);
		$data['payshopScript'] = 'catalog/view/javascript/ifthenpay/' . $scriptVersion;
		return $this->load->view('extension/payment/payshop', $data);
	}

	public function updateUserAccount(): void
	{
		$this->load->model('extension/payment/payshop');
		$requestUserToken = $this->request->get['user_token'];

        if (!$requestUserToken || $requestUserToken !== $this->config->get('payment_payshop_updateUserAccountToken')) {
            http_response_code(403);
            die('Not Authorized');
        }

        try {
			require('admin/model/setting/setting.php');
         
            $modelAdmin = new ModelSettingSetting( $this->registry );

            $backofficeKey = $this->config->get('payment_payshop_backofficeKey');

            if (!$backofficeKey) {
                die('Backoffice key is required!');
            }
			$this->ifthenpayContainer = new IfthenpayContainer();
            $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

            $ifthenpayGateway->authenticate($backofficeKey);
			$modelAdmin->editSettingValue(
				'payment_payshop', 
				'payment_payshop_userPaymentMethods', 
				serialize($ifthenpayGateway->getPaymentMethods())
			); 
			$modelAdmin->editSettingValue(
				'payment_payshop', 
				'payment_payshop_userAccount', 
				serialize($ifthenpayGateway->getAccount())
			);
			$modelAdmin->editSettingValue(
				'payment_payshop',
				'payment_payshop_updateUserAccountToken',
				''
			);			
            http_response_code(200);
			$this->model_extension_payment_payshop->log('', 'User Account updated with success!');
            die('User Account updated with success!');
        } catch (\Throwable $th) {
			$this->model_extension_payment_payshop->log($th->getMessage(), 'Error Updating User Account');
            http_response_code(400);
            die($th->getMessage());
        }
	}

	public function confirm()
	{
		$this->load->model('extension/payment/payshop');
		$json['error'] = 'Error Processing Payment Method';
		if ($this->session->data['payment_method']['code'] == 'payshop') {
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        	$configData =  $this->model_setting_setting->getSetting('payment_payshop');
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
				$this->session->data['ifthenpayPaymentReturn']['paymentLogoUrl'] = $this->config->get('site_url') . 'image/payment/ifthenpay/payshop.svg';
				$comment = $this->load->view('extension/payment/ifthenpay_comment_payment_detail', $this->session->data['ifthenpayPaymentReturn']);
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'], 
					$configData['payment_payshop_order_status_id'],
					$comment,
					true,
					true
				);
				$this->session->data['payment_method']['comment'] = $comment;
				$redirect = $ifthenpayPaymentReturn->getRedirectUrl();
				$json['redirect'] = $redirect['redirect'] ?  $redirect['url'] : $this->url->link('checkout/success');
				$json['error'] = '';
				$this->model_extension_payment_payshop->log('', 'Payment Processed with success!');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			} catch (\Throwable $th) {
				$this->model_extension_payment_payshop->log($th->getMessage(), 'Error Processing Payment');
				$json['error'] = $th->getMessage();
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
			
	}

	//callback
	public function callback(){
		try {
			$this->load->model('extension/payment/payshop');
			$this->ifthenpayContainer = new IfthenpayContainer();
			$this->ifthenpayContainer->getIoc()->make(CallbackStrategy::class)->execute($this->request->get, $this);
			$this->model_extension_payment_payshop->log($this->request->get, 'Callback Processed with Success.');
        } catch (\Throwable $th) {
			$this->model_extension_payment_payshop->log($this->request->get . '-' . $th->getMessage(), 'Error Processing callback.');
        }
	}
		
	public function changeSuccessPage(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['ifthenpayPaymentReturn']) && $this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'payshop') {
			$ifthenpayPaymentReturn = $this->session->data['ifthenpayPaymentReturn'];
			$this->load->model('setting/setting');
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
			if ($order_info['payment_code'] === 'payshop') {
				if (!$ifthenpayPaymentReturn['orderView']) {
					$this->ifthenpayContainer = new IfthenpayContainer();
					$configData =  $this->model_setting_setting->getSetting('payment_payshop');
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
		$this->ifthenpayContainer = new IfthenpayContainer();
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/checkout') {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css'));
			$data['styles'] = $this->document->getStyles();
		}
		if (isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/success') {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('ifthenpayConfirmPage.css'));
			$data['styles'] = $this->document->getStyles();
		}		
	}
	public function changeFooterScripts(&$route, &$data, &$output) 
	{
		$this->ifthenpayContainer = new IfthenpayContainer();
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$this->document->addScript('catalog/view/javascript/ifthenpay/' . $mix->create('checkoutPayshopPage.js'));
		$data['scripts'] = $this->document->getScripts();
	}

	public function changeOrderStatusFromWebservice(): void
	{
		$this->load->language('extension/payment/payshop');
		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory(
			$this->request->post['order_id'], 
			$this->config->get('payment_payshop_order_status_complete_id'),
			$this->language->get('paymentConfirmedSuccess'),
			true,
			true
		);
		$this->ifthenpayController->model_extension_payment_payshop->log($this->request->get['order_id'], 'Payshop Order Status Change to paid with success');
	}

	public function changeMailOrderAdd(&$route, &$data, &$output) 
	{
		if ($this->session->data['payment_method']['code'] == 'payshop') {
			$this->session->data['ifthenpayPaymentReturn']['paymentMethodLogo'] = $this->config->get('site_url') . 'image/payment/payshop.svg';
			$data['comment'] = $this->load->view('mail/ifthenpayPaymentData', $this->session->data['ifthenpayPaymentReturn']);
		}		
	}
}