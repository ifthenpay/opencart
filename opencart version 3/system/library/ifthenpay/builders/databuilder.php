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


    // ifthenpaygateway

    public function setIthenpaygatewayKey(string $value): DataBuilderInterface
    {
        $this->data->ifthenpaygatewayKey = $value;
        return $this;
    }

    public function setLanguage(string $value): DataBuilderInterface
    {
        $this->data->language = $value;
        return $this;
    }

    public function setDeadline(string $value): DataBuilderInterface
    {
        $this->data->deadline = $value;
        return $this;
    }

    public function setAccounts(string $value): DataBuilderInterface
    {
        $this->data->accounts = $value;
        return $this;
    }

    public function setSelectedMethod(string $value): DataBuilderInterface
    {
        $this->data->selectedMethod = $value;
        return $this;
    }

    public function setBtnCloseUrl(string $value): DataBuilderInterface
    {
        $this->data->btnCloseUrl = $value;
        return $this;
    }

    public function setBtnCloseLabel(string $value): DataBuilderInterface
    {
        $this->data->btnCloseLabel = $value;
        return $this;
    }

    public function setPinCode(string $value): DataBuilderInterface
    {
        $this->data->pinCode = $value;
        return $this;
    }

    // PIX
    public function setPixFormData(array $valueArray): DataBuilderInterface
    {
        $this->data->pixFormData = $valueArray;
        return $this;
    }

    public function setMbwayDescription(string $value): DataBuilderInterface
    {
        $this->data->mbwayDescription = $value;
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
