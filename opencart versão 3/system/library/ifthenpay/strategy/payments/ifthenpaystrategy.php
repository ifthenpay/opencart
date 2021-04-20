<?php


declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;


use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Factory\Payment\StrategyFactory;

class IfthenpayStrategy
{
    protected $paymentDefaultData;
    protected $twigDefaultData;
    protected $emailDefaultData;
    protected $order;
    protected $paymentValueFormated;
    protected $ifthenpayController;
    protected $factory;
    protected $configData;



    public function __construct(
        DataBuilder $paymentDataBuilder,
        TwigDataBuilder $twigDataBuilder, 
        StrategyFactory $factory
    )
    {
        $this->paymentDefaultData = $paymentDataBuilder;
        $this->twigDefaultData = $twigDataBuilder;
        $this->emailDefaultData = [];
        $this->factory = $factory;
    }

    protected function getPaymentMethodName(string $paymentCode): string 
    {
        $parts = explode('_', $paymentCode);
        return end($parts);
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrder($this->order);
        $this->paymentDefaultData->setPaymentMethod($this->getPaymentMethodName($this->order['payment_code']));
    }

    /**
     * Set the value of configData
     *
     * @return  self
     */ 
    public function setConfigData($configData)
    {
        $this->configData = $configData;

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder(array $order)
    {
        $this->order = $order;

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
        $this->setPaymentValueFormated();        
        return $this;
    }

    /**
     * Set the value of paymentValueFormated
     *
     * @return  self
     */ 
    private function setPaymentValueFormated()
    {
        $this->paymentValueFormated = $this->ifthenpayController->currency->format(
            $this->order['total'], 
            $this->order['currency_code'], ''
        );
    }
}
