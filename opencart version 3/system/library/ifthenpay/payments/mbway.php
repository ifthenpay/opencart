<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Payments\Payment;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;
use Ifthenpay\Request\WebService;


class MbWay extends Payment implements PaymentMethodInterface
{
    private $mbwayKey;
    private $telemovel;
    private $mbwayPedido;
    private $mbwayDescription;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService)
    {
        parent::__construct($orderId, $valor, $data, $webService);
        $this->mbwayKey = $data->getData()->mbwayKey;
        $this->telemovel = $data->getData()->telemovel;
        $this->mbwayDescription = $data->getData()->mbwayDescription;
    }

    public function checkValue(): void
    {
        if ($this->valor < 0.10) {
            throw new \Exception('Mbway does not allow payments under 0.10€');
        }
    }

    private function checkEstado(): void
    {
        if ($this->mbwayPedido['Status'] !== '000') {
            throw new \Exception($this->mbwayPedido['Message']);
        }
    }

    private function setReferencia(): void
    {
        $payload = [
            'mbWayKey' => $this->mbwayKey,
            'orderId' => $this->orderId,
            'amount' => $this->valor,
            'mobileNumber' => $this->telemovel,
            'email' => '',
            'descricao' => str_replace('{{order_id}}', $this->orderId, $this->mbwayDescription ?? '')
        ];


        $this->mbwayPedido = $this->webService->postRequest(
            'https://api.ifthenpay.com/spg/payment/mbway',
            [
                'mbWayKey' => $this->mbwayKey,
                'orderId' => $this->orderId,
                'amount' => $this->valor,
                'mobileNumber' => $this->telemovel,
                'email' => '',
                'descricao' => str_replace('{{order_id}}', $this->orderId, $this->mbwayDescription ?? '')
            ],
            true
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();
        $this->dataBuilder->setIdPedido($this->mbwayPedido['RequestId']);
        $this->dataBuilder->setTelemovel($this->telemovel);
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue();
        return $this->getReferencia();
    }
}
