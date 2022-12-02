<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;

class MbWayPaymentStatus implements PaymentStatusInterface
{
    private $webService;

    private static $statusRefusedByUser = '020';
    private static $statusPaid = '000';
    private static $statusPending = '123';


    public function __construct(WebService $webService)
    {
        $this->webService = $webService;
    }

    private function checkEstado(): bool
    {
        if (isset($this->mbwayPedido['EstadoPedidos'][0]['Estado']) && $this->mbwayPedido['EstadoPedidos'][0]['Estado'] === '000') {
            return true;
        }
        return false;
    }

    private function getMbwayEstado(): void
    {
        $this->mbwayPedido = $this->webService->postRequest(
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

    public function getPaymentStatusWithArgs($mbwayKey, $requestId)
    {
        $response = $this->webService->postRequest(
            'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/EstadoPedidosJSON',
            [
                'MbWayKey' => $mbwayKey,
                'canal' => '03',
                'idspagamento' => $requestId
            ]
        )->getResponseJson();

        try {
            $status = $response['EstadoPedidos'][0]['Estado'];


            if ($status === self::$statusPending) {
                return
                    ['orderStatus' => 'pending'];
            }
            if ($status === self::$statusRefusedByUser) {
                return 
                    ['orderStatus' => 'refused']
                ;
            }
            if ($status === self::$statusPaid) {
                return 
                    ['orderStatus' => 'paid']
                ;
            }

            return 
                ['orderStatus' => 'error']
            ;
        } catch (\Throwable $th) {
            return 
                ['orderStatus' => 'error']
            ;
        }
    }
}
