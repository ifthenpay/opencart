<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Payments\Payment;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;
use Ifthenpay\Request\WebService;


class Ifthenpaygateway extends Payment implements PaymentMethodInterface
{
	private $ifthenpaygatewayKey;
	private $telemovel;
	private $ifthenpaygatewayPedido;

	public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService)
	{
		parent::__construct($orderId, $valor, $data, $webService);
		$this->ifthenpaygatewayKey = $data->getData()->ifthenpaygatewayKey;
	}

	public function checkValue(): void
	{
		// not required to check
	}

	private function checkEstado(): void
	{
		if (!isset($this->ifthenpaygatewayPedido['PinCode']) || !isset($this->ifthenpaygatewayPedido['RedirectUrl'])) {
			throw new \Exception('Error requesting payment');
		}
	}


	private function setReferencia(): void
	{
		$payload = [
			'id' => $this->orderId,
			"amount" => $this->valor,
			"description" => 'Opencart order #' . $this->orderId,
			"lang" => $this->dataBuilder->getData()->language,
			"expiredate" => $this->dataBuilder->getData()->deadline,
			"accounts" => $this->dataBuilder->getData()->accounts,
			"selected_method" => $this->dataBuilder->getData()->selectedMethod,
			"btnCloseUrl" => $this->dataBuilder->getData()->btnCloseUrl,
			"btnCloseLabel" => $this->dataBuilder->getData()->btnCloseLabel,
			"success_url" => $this->dataBuilder->getData()->successUrl,
			"cancel_url" => $this->dataBuilder->getData()->cancelUrl,
			"error_url" => $this->dataBuilder->getData()->errorUrl,
		];

		$this->ifthenpaygatewayPedido = $this->webService->postRequest(
			'https://api.ifthenpay.com/gateway/pinpay/' . $this->ifthenpaygatewayKey,
			$payload,
			true
		)->getResponseJson();
	}

	private function getReferencia(): DataBuilder
	{
		$this->setReferencia();
		$this->checkEstado();
		$this->dataBuilder->setPinCode($this->ifthenpaygatewayPedido['PinCode']);
		$this->dataBuilder->setPaymentUrl($this->ifthenpaygatewayPedido['RedirectUrl']);
		$this->dataBuilder->setTotalToPay((string)$this->valor);
		return $this->dataBuilder;
	}

	public function buy(): DataBuilder
	{
		return $this->getReferencia();
	}
}
