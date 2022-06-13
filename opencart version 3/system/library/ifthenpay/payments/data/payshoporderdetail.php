<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\PayshopBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;
use Ifthenpay\Traits\Payments\FormatReference;

class PayshopOrderDetail extends PayshopBase implements OrderDetailInterface
{
    use FormatReference;

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setReferencia($this->formatReference($this->paymentDataFromDb['referencia']));
        $this->twigDefaultData->setValidade(!empty($this->paymentDataFromDb) ? 
            (new \DateTime($this->paymentDataFromDb['validade']))->format('d-m-Y') : ''
        );
        $this->twigDefaultData->setIdPedido($this->paymentDataFromDb['id_transacao']);
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia($this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia'));
        $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
