<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Callback\CallbackProcess;
use Ifthenpay\Callback\CallbackVars;
use Ifthenpay\Contracts\Callback\CallbackProcessInterface;
use Ifthenpay\Payments\Gateway;

class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
	public function process(): void
	{

		// get the callback payment method to use the correct phish key
		$originalPaymentMethod = $this->paymentMethod;

		// will set the paymentdata by the payment method in the callback
		$this->setPaymentData();

		// if no payment data is set, search other payment methods
		if (empty($this->paymentData)) {
			if ($this->paymentMethod === Gateway::IFTHENPAYGATEWAY) {

				// search every active pament method tables
				foreach (Gateway::METHODS_WITH_CALLBACK as $paymentMethod) {
					$isActive = $this->ifthenpayController->config->get('payment_' . $paymentMethod . '_status') ?? '';

					if ($isActive == '1') {
						$this->setPaymentData($paymentMethod);
						if (!empty($this->paymentData)) {
							// set the correct payment method
							$this->paymentMethod = $paymentMethod;
							$this->request[CallbackVars::PAYMENT] = $paymentMethod;
							break;
						}
					}
				}
			} else {

				$isActive = $this->ifthenpayController->config->get('payment_' . Gateway::IFTHENPAYGATEWAY . '_status') ?? '';

				if ($isActive == '1') {
					// search the ifthenpaygateway table
					$this->setPaymentData(Gateway::IFTHENPAYGATEWAY);
					if (!empty($this->paymentData)) {
						// set the correct payment method
						$this->paymentMethod = Gateway::IFTHENPAYGATEWAY;
						$this->request[CallbackVars::PAYMENT] = Gateway::IFTHENPAYGATEWAY;
					}
				}
			}
		}





		if (empty($this->paymentData)) {
			$this->executePaymentNotFound();
		} else {
			try {

				if (isset($this->paymentData['status']) && $this->paymentData['status'] === 'paid' && (isset($_GET['test']) && $_GET['test'] === 'true')) {
					throw new \Exception('Pagamento já efetuado');
				}

				$this->setOrder();
				$this->callbackValidate->setHttpRequest($this->request)
					->setOrder($this->order)
					->setConfigPaidStatus($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_id'))
					->setConfigurationChaveAntiPhishing($this->ifthenpayController->config->get('payment_' . $originalPaymentMethod . '_chaveAntiPhishing'))
					->setPaymentDataFromDb($this->paymentData)
					->validate();
				$this->changeIfthenpayPaymentStatus('paid');
				$this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);



				$historyMessage = $this->ifthenpayController->language->get('paymentConfirmedSuccess');
				if ($this->paymentMethod == Gateway::IFTHENPAYGATEWAY) {

					// get the payment method key
					$paymentMethodKey = $this->request[CallbackVars::PM] ?? 'none';

					// get the payment method name (used to display to user)
					$methods = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_methods');
					$paymentMethodName = 'Ifthenpay Gateway';
					if (isset($methods[$paymentMethodKey]) && isset($methods[$paymentMethodKey]['method_name'])) {
						$paymentMethodName = $methods[$paymentMethodKey]['method_name'];
					}

					$historyMessage = $this->ifthenpayController->language->get('paymentConfirmedSuccess') . $paymentMethodName . '.';
				}


				$this->ifthenpayController->model_checkout_order->addOrderHistory(
					$this->paymentData['order_id'],
					$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_complete_id'),
					$historyMessage,
					true,
					true
				);

				if (isset($_GET['test']) && $_GET['test'] === 'true') {
					http_response_code(200);

					$response = [
						'status' => 'success',
						'message' => 'Callback received and validated with success for payment method ',
						pathinfo(__FILE__)['filename'] . $this->paymentMethod
					];


					die(json_encode($response));
				}

				http_response_code(200);
				die('ok');
			} catch (\Throwable $th) {

				if (isset($_GET['test']) && $_GET['test'] === 'true') {
					http_response_code(200);

					$response = [
						'status' => 'warning',
						'message' => $th->getMessage(),
					];


					die(json_encode($response));
				}

				http_response_code(400);
				die($th->getMessage());
			}
		}
	}
}
