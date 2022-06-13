<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;

use Ifthenpay\Factory\Payment\AdminEmailPaymentDataFactory;

class IfthenpayAdminEmailPaymentData
{
    private $paymentMethod;
    private $registry;

    public function __construct(
        AdminEmailPaymentDataFactory $factory
    )
    {
        $this->factory = $factory;
    }
    public function execute(): void
    {
        $this->factory
            ->setType($this->paymentMethod)
            ->setIfthenpayController($this->ifthenpayController)
            ->setRegistry($this->registry)
            ->build()
            ->sendEmail();
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

    /**
     * Set the value of registry
     *
     * @return  self
     */ 
    public function setRegistry($registry)
    {
        $this->registry = $registry;

        return $this;
    }
}
