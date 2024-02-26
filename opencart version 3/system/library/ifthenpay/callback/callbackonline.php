<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackProcessInterface;
use Ifthenpay\Traits\Payments\ConvertCurrency;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Request\WebService;


class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
{
	use ConvertCurrency;

	public function process(): void
	{

		switch ($this->paymentMethod) {
			case Gateway::CCARD:
				$this->executeCcardCallback();
				break;
			case Gateway::COFIDIS:
				$this->executeCofidisCallback();
				break;

			default:
				$this->executePaymentMethodNotFound();
				break;
		}
	}


	/**
	 * makes request to get the cofidis transaction status
	 * @return string
	 */
	public function getCofidisTransactionStatusArray($cofidisKey, $transactionId): array
	{
		$payload = [
			'cofidisKey' => $cofidisKey,
			'requestId' => $transactionId,
		];

		$ifthenpayContainer = new IfthenpayContainer();
		$webService = $ifthenpayContainer->getIoc()->make(WebService::class);

		$statusArray = $webService->postRequest('https://ifthenpay.com/api/cofidis/status', $payload, true)->getResponseJson();

		return $statusArray;
	}

	private function executeCofidisCallback()
	{
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['paymentMethod'] = $this->paymentMethod;
		$this->setPaymentData();

		$checkoutLink = 'checkout/success';

		if (empty($this->paymentData)) {
			$this->executePaymentNotFound();
		} else {

			try {
				$this->setOrder();

				$paymentStatus = $this->request['Success'];

				if (
					strtolower($this->paymentData['status']) !== 'pending' && $this->order['order_status'] !==
					$this->ifthenpayController->config->get('payment_cofidis_order_status_id')
				) {
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('orderIsPaid');
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
					$this->ifthenpayController->model_extension_payment_cofidis->log([
						'paymentData' => $this->paymentData,
					], 'order already paid');
					$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));
				} else {


					$cofidisKey = $this->ifthenpayController->config->get('payment_cofidis_cofidisKey');
					$transactionId = $this->paymentData['requestId'];

					if ($paymentStatus === 'True') {

						$transactionStatusArray = $this->getCofidisTransactionStatusArray($cofidisKey, $transactionId);
						$status = $transactionStatusArray[0]['statusCode'];

						if ($status === 'INITIATED' || $status === 'PENDING_INVOICE') {
							$this->ifthenpayController->load->model('setting/setting');

							$this->changeIfthenpayPaymentStatus('pending');

							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = $this->ifthenpayController->language->get('paymentConfirmedSuccess');
							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = '';
						} else {

							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('cofidis_error_canceled');
							$checkoutLink = 'checkout/failure';

							$this->ifthenpayController->model_extension_payment_cofidis->log([
								'paymentData' => $this->paymentData
							], 'Payment by credit card canceled by the client or resulted in error.');
						}


					} else if ($paymentStatus !== 'True') {

						$transactionStatusArray = [];

						if ($this->request['Success'] !== 'True') {
							// sleep 5 seconds because error, cancel, not approved may not be present right after returning with error from cofidis
							for ($i = 0; $i < 2; $i++) {

								sleep(5);
								$transactionStatusArray = $this->getCofidisTransactionStatusArray($cofidisKey, $transactionId);
								if (count($transactionStatusArray) > 1) {
									break;
								}
							}
						}

						$checkoutLink = 'checkout/failure';

						if ($transactionStatusArray[0]['statusCode'] === 'CANCELED') {
							$hasTechnicalError = false;

							// check if it was canceled due to technical error
							foreach ($transactionStatusArray as $transactionStatus) {
								if ($transactionStatus['statusCode'] === 'TECHNICAL_ERROR') {
									$hasTechnicalError = true;
								}
							}

							if ($hasTechnicalError) {
								$this->handleCofidisTechnicalError();
							} else {
								$this->handleCofidisCancel();
							}


						} else if ($transactionStatusArray[0]['statusCode'] === 'NOT_APPROVED') {
							$this->handleCofidisNotApproved();
						} else if ($transactionStatusArray[0]['statusCode'] === 'TECHNICAL_ERROR') {
							// fallback for technical error status without canceled status, that can occur if cancel status is not registered immediately after technical error
							$this->handleCofidisTechnicalError();
						}


					} else {

						$this->handleCofidisOtherError();
					}

					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
					$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));

				}

			} catch (\Throwable $th) {
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderView'] = false;
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $th->getMessage();
				$checkoutLink = 'checkout/failure';

				$this->ifthenpayController->model_extension_payment_cofidis->log([
					'error' => $th->getMessage(),
					'paymentData' => $this->paymentData
				], 'Error processing cofidis payment - internal error');

				$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));

			}


		}
	}

	private function handleCofidisOtherError()
	{

		$this->changeIfthenpayPaymentStatus('error');
		$this->ifthenpayController->model_checkout_order->addOrderHistory(
			$this->paymentData['order_id'],
			$this->ifthenpayController->config->get('payment_cofidis_order_status_failed_id'),
			$this->ifthenpayController->language->get('cofidis_error_failed'),
			true,
			true
		);

		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('cofidis_error_failed');

		$errorMsg = [];
		if (isset($this->request['error'])) {
			$errorMsg = $this->request['error'];
		}

		$this->ifthenpayController->model_extension_payment_cofidis->log([
			'error' => $errorMsg,
			'paymentData' => $this->paymentData
		], 'Error processing credit card payment');
	}

	private function handleCofidisNotApproved()
	{
		$this->changeIfthenpayPaymentStatus('cancel');
		$this->ifthenpayController->model_checkout_order->addOrderHistory(
			$this->paymentData['order_id'],
			$this->ifthenpayController->config->get('payment_cofidis_order_status_not_approved_id'),
			$this->ifthenpayController->language->get('cofidis_error_not_approved'),
			true,
			true
		);

		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('cofidis_error_not_approved');

		$this->ifthenpayController->model_extension_payment_cofidis->log([
			'paymentData' => $this->paymentData
		], 'Payment by Cofidis Pay Not Approved.');
	}

	private function handleCofidisTechnicalError()
	{
		$this->changeIfthenpayPaymentStatus('error');
		$this->ifthenpayController->model_checkout_order->addOrderHistory(
			$this->paymentData['order_id'],
			$this->ifthenpayController->config->get('payment_cofidis_order_status_failed_id'),
			$this->ifthenpayController->language->get('cofidis_error_failed'),
			true,
			true
		);

		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('cofidis_error_failed');

		$this->ifthenpayController->model_extension_payment_cofidis->log([
			'paymentData' => $this->paymentData
		], 'Payment by Cofidis Pay resulted in error.');
	}



	private function handleCofidisCancel()
	{
		$this->changeIfthenpayPaymentStatus('cancel');
		$this->ifthenpayController->model_checkout_order->addOrderHistory(
			$this->paymentData['order_id'],
			$this->ifthenpayController->config->get('payment_cofidis_order_status_canceled_id'),
			$this->ifthenpayController->language->get('cofidis_error_canceled'),
			true,
			true
		);

		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_success'] = '';
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['cofidis_error'] = $this->ifthenpayController->language->get('cofidis_error_canceled');

		$this->ifthenpayController->model_extension_payment_cofidis->log([
			'paymentData' => $this->paymentData
		], 'Payment by credit card canceled by the client.');
	}

	private function executeCcardCallback()
	{
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['paymentMethod'] = $this->paymentMethod;
		$this->setPaymentData();

		$checkoutLink = 'checkout/success';

		if (empty($this->paymentData)) {
			$this->executePaymentNotFound();
		} else {
			try {
				$paymentStatus = $this->status->getTokenStatus(
					$this->token->decrypt($this->request['qn'])
				);
				$this->setOrder();
				if (
					strtolower($this->paymentData['status']) !== 'pending' && $this->order['order_status'] !==
					$this->ifthenpayController->config->get('payment_ccard_order_status_id')
				) {
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('orderIsPaid');
					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
					$this->ifthenpayController->model_extension_payment_ccard->log([
						'paymentData' => $this->paymentData,
					], 'order already paid');
					$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));
				} else {

					if ($paymentStatus === 'success') {
						$this->ifthenpayController->load->model('setting/setting');
						$configData = $this->ifthenpayController->model_setting_setting->getSetting('payment_ccard');
						if (
							$this->request['sk'] !== $this->tokenExtra->encript(
								$this->request['id'] . $this->request['amount'] . $this->request['requestId'],
								$configData['payment_ccard_ccardKey']
							)
						) {
							throw new \Exception($this->ifthenpayController->language->get('paymentSecurityToken'));
						}
						if ($this->order['currency_code'] !== 'EUR') {
							$orderTotal = $this->ifthenpayController->currency->format($this->order['total'], 'EUR', '', false);
						} else {
							$orderTotal = floatval($this->order['total']);
						}
						$requestValue = floatval($this->request['amount']);
						if (round($orderTotal, 2) !== round($requestValue, 2)) {
							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
							$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_message');

							$this->ifthenpayController->model_extension_payment_ccard->log([
								'orderTotal' => $orderTotal,
								'requestValue' => $requestValue,
								'paymentData' => $this->paymentData
							], 'Payment value by credit card not valid');
						}
						$this->changeIfthenpayPaymentStatus('paid');
						$this->ifthenpayController->model_checkout_order->addOrderHistory(
							$this->paymentData['order_id'],
							$this->ifthenpayController->config->get('payment_ccard_order_status_complete_id'),
							$this->ifthenpayController->language->get('paymentConfirmedSuccess'),
							true,
							true
						);

						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = $this->ifthenpayController->language->get('paymentConfirmedSuccess');
						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = '';
					} else if ($paymentStatus === 'cancel') {
						$this->changeIfthenpayPaymentStatus('cancel');
						$this->ifthenpayController->model_checkout_order->addOrderHistory(
							$this->paymentData['order_id'],
							$this->ifthenpayController->config->get('payment_ccard_order_status_canceled_id'),
							$this->ifthenpayController->language->get('ccard_error_canceled'),
							true,
							true
						);

						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_canceled');
						$checkoutLink = 'checkout/failure';

						$this->ifthenpayController->model_extension_payment_ccard->log([
							'paymentData' => $this->paymentData
						], 'Payment by credit card canceled by the client');
					} else {
						$this->changeIfthenpayPaymentStatus('error');
						$this->ifthenpayController->model_checkout_order->addOrderHistory(
							$this->paymentData['order_id'],
							$this->ifthenpayController->config->get('payment_ccard_order_status_failed_id'),
							$this->ifthenpayController->language->get('ccard_error_failed'),
							true,
							true
						);

						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
						$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_failed');
						$checkoutLink = 'checkout/failure';

						$errorMsg = [];
						if (isset($this->request['error'])) {
							$errorMsg = $this->request['error'];
						}

						$this->ifthenpayController->model_extension_payment_ccard->log([
							'error' => $errorMsg,
							'paymentData' => $this->paymentData
						], 'Error processing credit card payment');
					}

					$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
					$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));
				}
			} catch (\Throwable $th) {
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderView'] = false;
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
				$this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $th->getMessage();
				$checkoutLink = 'checkout/failure';

				$this->ifthenpayController->model_extension_payment_ccard->log([
					'error' => $th->getMessage(),
					'paymentData' => $this->paymentData
				], 'Error processing credit card payment - internal error');

				$this->ifthenpayController->response->redirect($this->ifthenpayController->url->link($checkoutLink, true));
			}
		}
	}
}
