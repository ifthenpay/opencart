<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\MultibancoBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;


class MultibancoOrderDetail extends MultibancoBase implements OrderDetailInterface
{
    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setEntidade($this->paymentDataFromDb['entidade']);
        $this->twigDefaultData->setReferencia($this->paymentDataFromDb['referencia']);
        $this->twigDefaultData->setIfthenpayPaymentPanelEntidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelEntidade'));
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia($this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia'));
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
