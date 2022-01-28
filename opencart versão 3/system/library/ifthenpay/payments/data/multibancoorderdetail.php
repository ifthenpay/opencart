<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MultibancoBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;
use Ifthenpay\Traits\Payments\FormatReference;


class MultibancoOrderDetail extends MultibancoBase implements OrderDetailInterface
{
    use FormatReference;

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setEntidade($this->paymentDataFromDb['entidade']);
        $this->twigDefaultData->setReferencia($this->formatReference($this->paymentDataFromDb['referencia']));
        $this->twigDefaultData->setIfthenpayPaymentPanelEntidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelEntidade'));
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia($this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia'));
        if ($this->paymentDataFromDb['validade']) {
            $this->twigDefaultData->setValidade($this->paymentDataFromDb['validade']);
            $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
        }
        

    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
