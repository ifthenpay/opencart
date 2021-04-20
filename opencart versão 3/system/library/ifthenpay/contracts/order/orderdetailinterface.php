<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Order;


interface OrderDetailInterface
{
    public function setTwigVariables(): void;
    public function getOrderDetail(): OrderDetailInterface;
}
