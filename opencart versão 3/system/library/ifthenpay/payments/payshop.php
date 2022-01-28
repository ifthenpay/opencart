<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;


use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;


class Payshop extends Payment implements PaymentMethodInterface
{
    private $payshopKey;
    protected $validade;
    private $payshopPedido;

    public function __construct(GatewayDataBuilder $data, string $orderId, string $valor, WebService $webService)
    {
        parent::__construct($orderId, $valor, $data, $webService);
        $this->payshopKey = $data->getData()->payshopKey;
        $this->validade = $this->makeValidade($data->getData()->validade);
    }

    private function makeValidade(string $validade): string
    {

        if ($validade === '0' || $validade === '') {
            return '';
        }
        return (new \DateTime(date("Ymd")))->modify('+' . $validade . 'day')
            ->format('Ymd');
    }

    public function checkValue(): void
    {
        if ($this->valor < 0) {
            throw new \Exception('Payshop does not allow payments of 0€');
        }
    }

    private function checkEstado(): void
    {
        if ($this->payshopPedido['Code'] !== '0') {
            throw new \Exception($this->payshopPedido['Message']);
        }
    }

    private function setReferencia(): void
    {
        $this->payshopPedido = $this->webService->postRequest(
            'https://ifthenpay.com/api/payshop/reference/',
            [
                    'payshopkey' => $this->payshopKey,
                    'id' => $this->orderId,
                    'valor' => $this->valor,
                    'validade' => $this->validade,
                ],
            true
        )->getResponseJson();
    }

    private function getReferencia(): DataBuilder
    {
        $this->setReferencia();
        $this->checkEstado();

        $this->dataBuilder->setIdPedido($this->payshopPedido['RequestId']);
        $this->dataBuilder->setReferencia($this->payshopPedido['Reference']);
        $this->dataBuilder->setTotalToPay((string)$this->valor);
        $this->dataBuilder->setValidade($this->validade);
        return $this->dataBuilder;
    }

    public function buy(): DataBuilder
    {
        $this->checkValue();
        return $this->getReferencia();
    }
}
