<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\CCardBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class CCardOrderDetail extends CCardBase implements OrderDetailInterface
{
    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setIdPedido($this->paymentDataFromDb['requestId']);
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
