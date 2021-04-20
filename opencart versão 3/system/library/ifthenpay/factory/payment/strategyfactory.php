<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Status;
use Ifthenpay\Factory\Factory;
use Ifthenpay\Payments\Gateway;
use Illuminate\Container\Container;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;


abstract class StrategyFactory extends Factory
{
    protected $paymentDefaultData;
    protected $twigDefaultData;
    protected $gatewayBuilder;
    protected $ifthenpayGateway;
    protected $configData;
    protected $utility;
    protected $ifthenpayController;
    
    public function __construct(
        Container $ioc,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        Token $token = null,
        Status $status = null
    )
	{
        parent::__construct($ioc);
        $this->gatewayBuilder = $gatewayBuilder;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->token = $token;
        $this->status = $status;
    }

    abstract public function build();

    /**
     * Set the value of paymentDefaultData
     *
     * @return  self
     */ 
    public function setPaymentDefaultData($paymentDefaultData)
    {
        $this->paymentDefaultData = $paymentDefaultData;

        return $this;
    }

    /**
     * Set the value of smartyDefaultData
     *
     * @return  self
     */ 
    public function setTwigDefaultData(TwigDataBuilder $twigDefaultData)
    {
        $this->twigDefaultData = $twigDefaultData;

        return $this;
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
