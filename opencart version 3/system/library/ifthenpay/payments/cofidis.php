<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;

class Cofidis extends Payment implements PaymentMethodInterface
{
	private $cofidisKey;
	private $cofidisPedido;
	private $returnUrl;

	public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService)
	{
		parent::__construct($orderId, $valor, $data, $webService);
		$this->cofidisKey = $data->getData()->cofidisKey;
		$this->returnUrl = $data->getData()->returnUrl;
	}

	public function checkValue(): void
	{
		//void
	}

	private function checkEstado(): void
	{
		if ($this->cofidisPedido['status'] !== '0') {
			throw new \Exception($this->cofidisPedido['message']);
		}
	}

	private function setReferencia(): void
	{
		$payload = $this->dataBuilder->getData()->customerData;
		$payload['returnUrl'] = $this->returnUrl;
		$payload['orderId'] = $this->orderId;
		$payload['amount'] = $this->valor;

		$this->cofidisPedido = $this->webService->postRequest(
			'http://ifthenpay.com/api/cofidis/init/' . $this->cofidisKey,
			$payload,
			true
		)->getResponseJson();
	}

	private function getReferencia(): DataBuilder
	{
		$this->setReferencia();
		$this->checkEstado();

		$this->dataBuilder->setPaymentMessage($this->cofidisPedido['message']);
		$this->dataBuilder->setPaymentUrl($this->cofidisPedido['paymentUrl']);
		$this->dataBuilder->setIdPedido($this->cofidisPedido['requestId']);
		$this->dataBuilder->setPaymentStatus($this->cofidisPedido['status']);

		return $this->dataBuilder;
	}

	public function buy(): DataBuilder
	{
		return $this->getReferencia();
	}
}
