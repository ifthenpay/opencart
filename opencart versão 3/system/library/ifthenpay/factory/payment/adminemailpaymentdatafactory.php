<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Payments\Gateway;
use Illuminate\Container\Container;
use Ifthenpay\Payments\Data\EmailPaymentData;
use Ifthenpay\Payments\Data\PayshopEmailPaymentData;
use Ifthenpay\Payments\Data\MultibancoEmailPaymentData;
use Ifthenpay\Builders\TwigDataBuilder;

class AdminEmailPaymentDataFactory extends Factory
{
    private $twigDataBuilder;
    private $registry;
       
    public function __construct(Container $ioc, TwigDataBuilder $twigDataBuilder)
	{
        parent::__construct($ioc);
        $this->twigDataBuilder = $twigDataBuilder;
    }
    public function build(): EmailPaymentData  {
        switch (strtolower($this->type)) {
            case Gateway::MULTIBANCO:
                return (new MultibancoEmailPaymentData($this->twigDataBuilder))
                    ->setIfthenpayController($this->ifthenpayController)
                    ->setRegistry($this->registry);
            case Gateway::PAYSHOP:
                return (new PayshopEmailPaymentData($this->twigDataBuilder))
                    ->setIfthenpayController($this->ifthenpayController)
                    ->setRegistry($this->registry);
            default:
                throw new \Exception('Unknown Admin Email Payment Data');
        }
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
