<?php

declare(strict_types=1);

namespace Ifthenpay\Builders;

use Ifthenpay\Contracts\Builders\DataBuilderInterface;

class DataBuilder implements DataBuilderInterface
{
	protected $data;

	public function __construct()
	{
		$this->data = new \stdClass;
	}

	public function setOrder($value): DataBuilderInterface
	{
		$this->data->order = $value;
		return $this;
	}

	public function setTotalToPay(string $value): DataBuilderInterface
	{
		$this->data->totalToPay = $value;
		return $this;
	}

	public function setPaymentMethod(string $value): DataBuilderInterface
	{
		$this->data->paymentMethod = $value;
		return $this;
	}

	public function setEntidade(string $value): DataBuilderInterface
	{
		$this->data->entidade = $value;
		return $this;
	}

	public function setReferencia(string $value): DataBuilderInterface
	{
		$this->data->referencia = $value;
		return $this;
	}

	public function setTelemovel(string $value = null): DataBuilderInterface
	{
		$this->data->telemovel = $value;
		return $this;
	}

	public function setValidade(string $value): DataBuilderInterface
	{
		$this->data->validade = $value;
		return $this;
	}

	public function setIdPedido(string $value = null): DataBuilderInterface
	{
		$this->data->idPedido = $value;
		return $this;
	}

	public function setBackofficeKey(string $value): DataBuilderInterface
	{
		$this->data->backofficeKey = $value;
		return $this;
	}

	public function setSuccessUrl(string $value): DataBuilderInterface
	{
		$this->data->successUrl = $value;
		return $this;
	}

	public function setErrorUrl(string $value): DataBuilderInterface
	{
		$this->data->errorUrl = $value;
		return $this;
	}

	public function setCancelUrl(string $value): DataBuilderInterface
	{
		$this->data->cancelUrl = $value;
		return $this;
	}

	public function setReturnUrl(string $value): DataBuilderInterface
	{
		$this->data->returnUrl = $value;
		return $this;
	}

	public function setCustomerData(array $value): DataBuilderInterface
	{
		$this->data->customerData = $value;
		return $this;
	}

	public function setHash(string $value): DataBuilderInterface
	{
		$this->data->hash = $value;
		return $this;
	}

	public function setPaymentMessage(string $value): DataBuilderInterface
	{
		$this->data->message = $value;
		return $this;
	}

	public function setPaymentUrl(string $value): DataBuilderInterface
	{
		$this->data->paymentUrl = $value;
		return $this;
	}

	public function setPaymentStatus(string $value): DataBuilderInterface
	{
		$this->data->status = $value;
		return $this;
	}

	public function setBaseUrl(string $value): DataBuilderInterface
	{
		$this->data->baseUrl = $value;
		return $this;
	}


	public function toArray(): array
	{
		return json_decode(json_encode($this->data), true);
	}

	public function getData(): \stdClass
	{
		return $this->data;
	}
}
