<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Payments\Gateway;

class CCardPaymentStatus implements PaymentStatusInterface
{
    private $data;
    private $ccardPedido;
    private $webService;

    public function __construct(WebService $webService)
    {
        $this->webService = $webService;
    }

    private function checkEstado(): bool
    {
        if (isset($this->ccardPedido['CodigoErro']) && $this->ccardPedido['CodigoErro'] === '0') {
            return true;
        }
        return false;
    }

    private function getCCardEstado(): void
    {
        $this->ccardPedido = $this->webService->getRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => strtoupper(Gateway::CCARD),
                    'Subentidade' => $this->data->getData()->ccardKey,
                    'dtHrInicio' => '',
                    'dtHrFim' => '',
                    'Referencia' => $this->data->getData()->referencia,
                    'Valor' => $this->data->getData()->totalToPay,
                    'Sandbox' => 0
                ]
        )->getXmlConvertedResponseToArray();
    }

    public function getPaymentStatus(): bool
    {
        $this->getCCardEstado();
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