<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentCcard extends Controller
{
	private $ifthenpayContainer;

	public function index()
	{
		$this->ifthenpayContainer = new IfthenpayContainer();
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$this->load->language('extension/payment/ccard');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$scriptVersion = $mix->create('checkoutCcardPage.js');
		$this->document->addScript('extension/payment/javascript/ifthenpay/' . $scriptVersion);
		$data['ccardScript'] = 'catalog/view/javascript/ifthenpay/' . $scriptVersion;
		return $this->load->view('extension/payment/ccard', $data);
	}

	public function updateUserAccount(): void
	{
		$this->load->model('extension/payment/ccard');
		$requestUserToken = $this->request->get['user_token'];

        if (!$requestUserToken || $requestUserToken !== $this->config->get('payment_ccard_updateUserAccountToken')) {
            http_response_code(403);
            die('Not Authorized');
        }

        try {
			require('admin/model/setting/setting.php');
         
            $modelAdmin = new ModelSettingSetting( $this->registry );

            $backofficeKey = $this->config->get('payment_ccard_backofficeKey');

            if (!$backofficeKey) {
                die('Backoffice key is required!');
            }
			$this->ifthenpayContainer = new IfthenpayContainer();
            $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

            $ifthenpayGateway->authenticate($backofficeKey);
			$modelAdmin->editSettingValue(
				'payment_ccard', 
				'payment_ccard_userPaymentMethods', 
				serialize($ifthenpayGateway->getPaymentMethods())
			); 
			$modelAdmin->editSettingValue(
				'payment_ccard', 
				'payment_ccard_userAccount', 
				serialize($ifthenpayGateway->getAccount())
			);
			$modelAdmin->editSettingValue(
				'payment_ccard',
				'payment_ccard_updateUserAccountToken',
				''
			);			
            http_response_code(200);
			$this->model_extension_payment_ccard->log('', 'User Account updated with success!');
            die('User Account updated with success!');
        } catch (\Throwable $th) {
			$this->model_extension_payment_ccard->log($th->getMessage(), 'Error Updating User Account');
            http_response_code(400);
            die($th->getMessage());
        }
	}

	public function confirm()
	{
		$this->load->model('extension/payment/ccard');
		$json['error'] = 'Error Processing Payment Method';
		if ($this->session->data['payment_method']['code'] == 'ccard') {
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        	$configData =  $this->model_setting_setting->getSetting('payment_ccard');
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
				$this->session->data['ifthenpayPaymentReturn']['paymentLogoUrl'] = $this->config->get('site_url') . 'image/payment/ifthenpay/ccard.svg';
				$comment = $this->load->view('extension/payment/ifthenpay_comment_payment_detail', $this->session->data['ifthenpayPaymentReturn']);
				$this->load->model('checkout/order');
				$this->model_checkout_order->addOrderHistory(
					$this->session->data['order_id'], 
					$configData['payment_ccard_order_status_id'],
					$comment,
					true,
					true
				);
				$this->session->data['payment_method']['comment'] = $comment;
				$redirect = $ifthenpayPaymentReturn->getRedirectUrl();
				$json['redirect'] = $redirect['redirect'] ?  $redirect['url'] : $this->url->link('checkout/success');
				$json['error'] = '';
				$this->model_extension_payment_ccard->log('', 'Payment Processed with success!');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			} catch (\Throwable $th) {
				$this->model_extension_payment_ccard->log($th->getMessage(), 'Error Processing Payment');
				$json['error'] = $th->getMessage();
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
			
	}

	//callback
	public function callback(){
		try {
			$this->load->model('extension/payment/ccard');
			$this->ifthenpayContainer = new IfthenpayContainer();
			$this->ifthenpayContainer->getIoc()->make(CallbackStrategy::class)->execute($this->request->get, $this);
			$this->model_extension_payment_ccard->log($this->request->get, 'Callback Processed with Success.');
        } catch (\Throwable $th) {
			$this->model_extension_payment_ccard->log($this->request->get . '-' . $th->getMessage(), 'Error Processing callback.');
            $this->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
            $this->session->data['ifthenpayPaymentReturn']['ccard_error'] = $th->getMessage();
        }
	}
		
	public function changeSuccessPage(&$route, &$data, &$output) 
	{
		if (isset($this->session->data['ifthenpayPaymentReturn']) && $this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'ccard') {
			$ifthenpayPaymentReturn = $this->session->data['ifthenpayPaymentReturn'];
			$this->load->model('setting/setting');
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
				if ($order_info['payment_code'] == 'ccard') {
					if (!$ifthenpayPaymentReturn['orderView']) {
							$this->ifthenpayContainer = new IfthenpayContainer();
							$configData =  $this->model_setting_setting->getSetting('payment_ccard');
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
		if ((isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/checkout') || (isset($_REQUEST['_route_']) && $_REQUEST['_route_'] === 'checkout/checkout')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css'));
			$data['styles'] = $this->document->getStyles();
		}
		if ((isset($_REQUEST['route']) && $_REQUEST['route'] === 'checkout/success') || (isset($_REQUEST['_route_']) && $_REQUEST['_route_'] === 'checkout/success')) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('ifthenpayConfirmPage.css'));
			$data['styles'] = $this->document->getStyles();
		}		
	}
	public function changeFooterScripts(&$route, &$data, &$output) {
		$this->ifthenpayContainer = new IfthenpayContainer();
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$this->document->addScript('catalog/view/javascript/ifthenpay/' . $mix->create('checkoutCcardPage.js'));
		$data['scripts'] = $this->document->getScripts();
	}

	public function changeMailOrderAdd(&$route, &$data, &$output) 
	{
		if ($this->session->data['payment_method']['code'] == 'ccard') {
			$this->load->language('extension/payment/ccard');
			$paymentMethodLogo = $this->config->get('site_url') . 'image/payment/ifthenpay/ccard.png';
			$data['payment_method'] = $this->language->get('text_title_ccard');
			$this->session->data['ifthenpayPaymentReturn']['paymentMethodLogo'] = $paymentMethodLogo;
			$data['comment'] = $this->load->view('mail/ifthenpayPaymentData', $this->session->data['ifthenpayPaymentReturn']);
		}		
	}

}