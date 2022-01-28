<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Cancel;

use Ifthenpay\Factory\Cancel\CancelIfthenpayOrderFactory;

class IfthenpayCancelOrder
{
    private $paymentMethod;
    private $ifthenpayController;

    public function __construct(
        CancelIfthenpayOrderFactory $factory
    )
    {
        $this->factory = $factory;
    }
    public function execute(): void
    {
        $this->factory
            ->setType($this->paymentMethod)
            ->setIfthenpayController($this->ifthenpayController)
            ->build()
            ->cancelOrder();
    }

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Set the value of ifthenpayController
     *
     * @return  self
     */ 
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }
}
