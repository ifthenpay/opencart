<?php

use Ifthenpay\Utility\Mix;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;
use Ifthenpay\Strategy\Cancel\IfthenpayCancelOrder;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentStatus;
use Ifthenpay\Strategy\Payments\IfthenpayAdminEmailPaymentData;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;


require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class IfthenpayControllerCatalog extends Controller
{
	protected $ifthenpayContainer;
	protected $paymentMethod;
	protected $dynamicModelName;
	protected $configData;

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->dynamicModelName = 'model_extension_payment_' . $this->paymentMethod;
		$this->ifthenpayContainer = new IfthenpayContainer();
		$this->load->model('extension/payment/' . $this->paymentMethod);
		$this->load->language('extension/payment/' . $this->paymentMethod);
	}

	public function index()
	{
		$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data[$this->paymentMethod . 'Script'] = 'catalog/view/javascript/ifthenpay/' . $mix->create('checkout' . ucfirst($this->paymentMethod) . 'Page.js');
		$data[$this->paymentMethod . 'Style'] = 'catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css');
		return $this->load->view('extension/payment/' . $this->paymentMethod, $data);
	}

	public function updateUserAccount(): void
	{
		$requestUserToken = $this->request->get['user_token'];

		if (!$requestUserToken || $requestUserToken !== $this->config->get('payment_' . $this->paymentMethod . '_updateUserAccountToken')) {
			http_response_code(403);
			die('Not Authorized');
		}

		try {
			require('admin/model/setting/setting.php');

			$modelAdmin = new ModelSettingSetting($this->registry);

			$backofficeKey = $this->config->get('payment_' . $this->paymentMethod . '_backofficeKey');

			if (!$backofficeKey) {
				die('Backoffice key is required!');
			}
			$ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

			$ifthenpayGateway->authenticate($backofficeKey);
			$modelAdmin->editSettingValue(
				'payment_' . $this->paymentMethod,
				'payment_' . $this->paymentMethod . '_userPaymentMethods',
				serialize($ifthenpayGateway->getPaymentMethods())
			);
			$modelAdmin->editSettingValue(
				'payment_' . $this->paymentMethod,
				'payment_' . $this->paymentMethod . '_userAccount',
				serialize($ifthenpayGateway->getAccount())
			);
			$modelAdmin->editSettingValue(
				'payment_' . $this->paymentMethod,
				'payment_' . $this->paymentMethod . '_updateUserAccountToken',
				''
			);
			http_response_code(200);
			$this->{$this->dynamicModelName}->log('', 'User Account updated with success!');
			die('User Account updated with success!');
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'requestData' => $this->request->get,
				'errorMessage' => $th->getMessage()
			], 'Error Updating User Account');
			http_response_code(400);
			die($th->getMessage());
		}
	}

	public function confirm()
	{
		$json['error'] = 'Error Processing Payment Method';
		if ($this->session->data['payment_method']['code'] == $this->paymentMethod) {
			$this->load->model('setting/setting');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
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
				$this->session->data['ifthenpayPaymentReturn']['paymentRedirectUrl'] = $ifthenpayPaymentReturn->getRedirectUrl();
				$this->setOrderCommentHistory($this->session->data['order_id'], $configData['payment_' . $this->paymentMethod . '_order_status_id']);
				$json['redirect'] = $this->url->link('checkout/success');
				$json['error'] = '';
				$this->{$this->dynamicModelName}->log('', 'Payment Processed with success!');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			} catch (\Throwable $th) {
				$this->{$this->dynamicModelName}->log([
					'orderInfo' => $order_info,
					'errorMessage' => $th->getMessage()
				], 'Error Processing Payment');
				$json['error'] = $th->getMessage();
				$this->response->addHeader('Content-Type: application/json');
				$this->response->addHeader('HTTP/1.0 400 Bad Request');
				$this->response->setOutput(json_encode($json));
			}
		}
	}

	public function callback()
	{
		try {
			$this->ifthenpayContainer->getIoc()->make(CallbackStrategy::class)->execute($this->request->get, $this);
			$this->{$this->dynamicModelName} > log($this->request->get, 'Callback Processed with Success.');
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'callbackData' => $this->request->get,
				'errorMessage' => $th->getMessage
			], 'Error Processing callback.');
			$this->session->data['ifthenpayPaymentReturn'][$this->paymentMethod . '_success'] = '';
			$this->session->data['ifthenpayPaymentReturn'][$this->paymentMethod . '_error'] = $th->getMessage();
		}
	}

	public function changeSuccessPage(&$route, &$data, &$output)
	{
		try {
			if (
				isset($this->session->data['ifthenpayPaymentReturn']) && isset($this->session->data['ifthenpayPaymentReturn']['paymentMethod']) &&
				$this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === $this->paymentMethod
			) {
				$ifthenpayPaymentReturn = $this->session->data['ifthenpayPaymentReturn'];
				$mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);
				$this->load->model('setting/setting');
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($this->session->data['ifthenpayPaymentReturn']['orderId']);
				if ($order_info['payment_code'] == $this->paymentMethod) {
					if (isset($this->session->data['ifthenpayPaymentReturn']['paymentRedirectUrl']) && $this->session->data['ifthenpayPaymentReturn']['paymentRedirectUrl']['redirect']) {
						$redirectUrl = $this->session->data['ifthenpayPaymentReturn']['paymentRedirectUrl']['url'];
						unset($this->session->data['ifthenpayPaymentReturn']);
						$this->response->redirect($redirectUrl);
					}
					if (!isset($ifthenpayPaymentReturn['orderView']) || !$ifthenpayPaymentReturn['orderView']) {
						$configData = $this->model_setting_setting->getSetting('payment_' . $this->paymentMethod);
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

					// replace # with - in telemovel for using as arg
					$pmReturnData = $this->session->data['ifthenpayPaymentReturn'];

					$pmReturnData['mobile_prefix'] = '';
					$pmReturnData['mobile_sufix'] = '';
					if (isset($pmReturnData['telemovel']) && $pmReturnData['telemovel'] !== '') {
						$parts = explode('#', $pmReturnData['telemovel']);
						if (count($parts) > 1) {
							$pmReturnData['mobile_prefix'] = $parts[0];
							$pmReturnData['mobile_sufix'] = $parts[1];
						}

					}


					$paymentReturnPanel = $this->load->view('extension/payment/ifthenpay_payment_panel', $pmReturnData);
					$this->session->data['ifthenpayPaymentReturn']['paymentReturnPaymentPanel'] = $paymentReturnPanel;
					$this->session->data['ifthenpayPaymentReturn']['confirmPageStyle'] = 'catalog/view/theme/default/stylesheet/ifthenpay/' .
						$mix->create('ifthenpayConfirmPage.css');
					$this->session->data['ifthenpayPaymentReturn']['confirmPageScript'] = 'catalog/view/javascript/ifthenpay/' . $mix->create('ifthenpaySuccessPage.js');
					$paymentReturn = $this->load->view('extension/payment/ifthenpay_payment_return', $this->session->data['ifthenpayPaymentReturn']);
					$data['text_message'] = $data['text_message'] . '<br>' . $paymentReturn;
					$this->{$this->dynamicModelName}->log($this->session->data['ifthenpayPaymentReturn'], 'Loading order details in success page.');
					unset($this->session->data['ifthenpayPaymentReturn']);

				}
			}
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'sessionData' => $this->session->data,
				'errorMessage' => $th->getMessage()
			], 'Error Loading order details in success page.');
		}
	}

	public function changeMailOrderAdd(&$route, &$data, &$output)
	{
		if (
			isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] === $this->paymentMethod &&
			(isset($this->session->data['ifthenpayPaymentReturn']) && !empty($this->session->data['ifthenpayPaymentReturn']))
		) {
			$paymentMethodLogo = $this->config->get('site_url') . 'image/payment/ifthenpay/' . $this->paymentMethod . '.png';
			$data['payment_method'] = $this->language->get('text_title_' . $this->paymentMethod);
			$this->session->data['ifthenpayPaymentReturn']['paymentMethodLogo'] = $paymentMethodLogo;
			$data['comment'] = $this->load->view('mail/ifthenpayPaymentData', $this->session->data['ifthenpayPaymentReturn']);
		}
	}

	public function cancelOrderCron(): void
	{
		try {
			$this->{$this->dynamicModelName}->log('', 'Cancel ' . $this->paymentMethod . ' order cron started');
			$this->ifthenpayContainer->getIoc()->make(IfthenpayCancelOrder::class)
				->setPaymentMethod($this->paymentMethod)
				->setIfthenpayController($this)
				->execute();
			$this->{$this->dynamicModelName}->log('', 'Cancel ' . $this->paymentMethod . ' order cron finish');
			header("HTTP/1.1 200 OK");
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'errorMessage' => $th->getMessage()
			], 'Error Cancel ' . $this->PaymentMethod . ' order cron');
			header("HTTP/1.1 400 Bad Request");
		}
	}

	public function checkPaymentStatusCron(): void
	{
		try {
			$this->{$this->dynamicModelName}->log('', 'Check ' . $this->paymentMethod . ' payment status cron started');
			if (in_array($this->paymentMethod, $this->ifthenpayContainer->getIoc()->make(Gateway::class)->getPaymentMethodsCanCancel())) {
				$this->ifthenpayContainer->getIoc()->make(IfthenpayPaymentStatus::class)
					->setPaymentMethod($this->paymentMethod)
					->setIfthenpayController($this)
					->execute();
			}
			$this->{$this->dynamicModelName}->log('', 'Check ' . $this->paymentMethod . ' payment status cron finish');
			header("HTTP/1.1 200 OK");
		} catch (\Throwable $th) {
			$this->{$this->dynamicModelName}->log([
				'errorMessage' => $th->getMessage()
			], 'Error Check ' . $this->PaymentMethod . ' payment status cron');
			header("HTTP/1.1 400 Bad Request");
		}
	}

	private function backofficePaymentProcessing(string $orderId): void
	{
		if ($orderId) {
			$this->{$this->dynamicModelName}->log($orderId, 'Start processing payment for order edit in backofice');
			$json['error'] = 'Error Processing Payment Method';
			if ($this->session->data['payment_method']['code'] == $this->paymentMethod) {
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
					unset($this->session->data['ifthenpayPaymentReturn']);
					$json['error'] = '';
					$this->{$this->dynamicModelName}->log($orderId, 'Order edit in backoffice Payment Processed with success!');
					$json['success'] = $this->language->get('text_success');

				} catch (\Throwable $th) {
					unset($this->session->data['ifthenpayPaymentReturn']);
					$this->{$this->dynamicModelName}->log([
						'orderInfo' => $order_info,
						'configData' => $configData,
						'errorMessage' => $th->getMessage()
					], 'Error Processing Payment');
					$json['error'] = $th->getMessage();
				}
			}
		}
	}

	public function backofficeOrderAdd(&$route, &$data, &$output)
	{
		$output = json_decode($this->response->getOutput());
		if (!isset($output->error) && isset($output->order_id))
			$this->backofficePaymentProcessing((string) $output->order_id);
	}

	public function backofficeOrderEdit(&$route, &$data, &$output)
	{
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);
		if ($order_info['payment_code'] !== $this->paymentMethod) {
			$this->{$this->dynamicModelName}->deletePaymentByOrderId($this->request->get['order_id']);
		}
		$this->backofficePaymentProcessing($this->request->get['order_id']);
	}

	protected function setOrderCommentHistory(string $orderId, string $orderStatusId): void
	{
		$viewData = $this->session->data['ifthenpayPaymentReturn'];
		$viewData['mobile_prefix'] = '';
		$viewData['mobile_sufix'] = '';

		if (
			$this->session->data['ifthenpayPaymentReturn']['paymentMethod'] === 'mbway' &&
			isset($this->session->data['ifthenpayPaymentReturn']['telemovel']) &&
			$this->session->data['ifthenpayPaymentReturn']['telemovel'] != ''
		) {
			$parts = explode('#', $this->session->data['ifthenpayPaymentReturn']['telemovel']);
			if (count($parts) > 1) {
				$viewData['mobile_prefix'] = $parts[0];
				$viewData['mobile_sufix'] = $parts[1];
			}
		}



		$comment = $this->load->view('extension/payment/ifthenpay_comment_payment_detail', $viewData);
		$this->load->model('checkout/order');
		$this->model_checkout_order->addOrderHistory(
			$orderId,
			$orderStatusId,
			$comment,
			true,
			true
		);
		$this->session->data['payment_method']['comment'] = $comment;
	}

	public function resendPaymentData()
	{
		try {
			$this->ifthenpayContainer->getIoc()->make(IfthenpayAdminEmailPaymentData::class)
				->setIfthenpayController($this)
				->setRegistry($this->registry)
				->setPaymentMethod($this->paymentMethod)
				->execute();
			$this->{$this->dynamicModelName}->log($this->request->get['orderId'], 'Email payment data sent with Success');
			unset($this->session->data['payment_method']['code']);
			unset($this->session->data['ifthenpayPaymentReturn']);
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('paymentDataResend_success')
			]));
		} catch (\Throwable $th) {
			unset($this->session->data['payment_method']['code']);
			unset($this->session->data['ifthenpayPaymentReturn']);
			$this->{$this->dynamicModelName}->log([
				'requestData' => $this->request->get,
				'errorMessage' => $th->getMessage()
			], 'Error Resending Email Payment Data');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->addHeader('HTTP/1.0 400 Bad Request');
			$this->response->setOutput(json_encode([
				'success' => $this->language->get('paymentDataResend_error')
			]));
		}
	}
}