<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;

class Pix extends Payment implements PaymentMethodInterface
{
	private $pixKey;
	private $pixPedido;
	private $returnUrl;

	public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService)
	{
		parent::__construct($orderId, $valor, $data, $webService);
		$this->pixKey = $data->getData()->pixKey;
		$this->returnUrl = $data->getData()->returnUrl;
	}

	public function checkValue(): void
	{
		//void
	}

	private function checkEstado(): void
	{
		if ($this->pixPedido['status'] !== '0') {
			throw new \Exception('Error: Unable to generate Pix Payment.');
		}
	}

	private function setReferencia(): void
	{
		$payload = $this->dataBuilder->getData()->pixFormData;

		$payload["orderId"] = $this->orderId;
		$payload["amount"] = (string)$this->valor;
		$payload["redirectUrl"] = $this->returnUrl;
		$payload["description"] = "Order {$this->orderId}";


		$this->pixPedido = $this->webService->postRequest(
			'https://api.ifthenpay.com/pix/init/' . $this->pixKey,
			$payload,
			true
		)->getResponseJson();
	}

	private function getReferencia(): DataBuilder
	{
		$this->setReferencia();
		$this->checkEstado();

		$this->dataBuilder->setPaymentMessage($this->pixPedido['message']);
		$this->dataBuilder->setPaymentUrl($this->pixPedido['paymentUrl']);
		$this->dataBuilder->setIdPedido($this->pixPedido['requestId']);
		$this->dataBuilder->setPaymentStatus($this->pixPedido['status']);

		return $this->dataBuilder;
	}

	public function buy(): DataBuilder
	{
		return $this->getReferencia();
	}
}
