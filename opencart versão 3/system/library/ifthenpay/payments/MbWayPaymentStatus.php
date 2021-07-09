<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\Webservice;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;

class MbWayPaymentStatus implements PaymentStatusInterface
{
    private $data;
    private $mbwayPedido;
    private $webservice;

    public function __construct(Webservice $webservice)
    {
        $this->webservice = $webservice;
    }

    private function checkEstado(): bool
    {
        if ($this->mbwayPedido['EstadoPedidos'][0]['Estado'] === '000') {
            return true;
        }
        return false;
    }

    private function getMbwayEstado(): void
    {
        $this->mbwayPedido = $this->webservice->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/EstadoPedidosJSON',
            [
                    'MbWayKey' => $this->data->getData()->mbwayKey,
                    'canal' => '03',
                    'idspagamento' => $this->data->getData()->idPedido
                ]
        )->getResponseJson();
    }

    public function getPaymentStatus(): bool
    {
        $this->getMbwayEstado();
        return $this->checkEstado();
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData(GatewayDataBuilder $data): PaymentStatusInterface
    {
        $this->data = $data;

        return $this;
    }
}