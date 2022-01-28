<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Cancel;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Factory\Payment\PaymentStatusFactory;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\Cancel\CancelOrder;
use Ifthenpay\Payments\Cancel\CancelMultibancoOrder;
use Ifthenpay\Payments\Cancel\CancelMbwayOrder;
use Ifthenpay\Payments\Cancel\CancelPayshopOrder;
use Ifthenpay\Payments\Cancel\CancelCCardOrder;
use Ifthenpay\Payments\Gateway;


class CancelIfthenpayOrderFactory extends Factory
{
    private $gatewayDataBuilder; 
    private $paymentStatusFactory;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder, 
        PaymentStatusFactory $paymentStatusFactory
    )
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder; 
        $this->paymentStatusFactory = $paymentStatusFactory;
	}

    public function build(): CancelOrder {
            switch ($this->type) {
                case Gateway::MULTIBANCO:
                    return (new CancelMultibancoOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory->setType($this->type)->build()
                ))->setIfthenpayController($this->ifthenpayController);
                case Gateway::MBWAY:
                    return (new CancelMbwayOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory->setType($this->type)->build()
                    ))->setIfthenpayController($this->ifthenpayController);
                case Gateway::PAYSHOP:
                    return (new CancelPayshopOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory->setType($this->type)->build()
                    ))->setIfthenpayController($this->ifthenpayController);
                case Gateway::CCARD:
                    return (new CancelCCardOrder(
                        $this->gatewayDataBuilder, 
                        $this->paymentStatusFactory->setType($this->type)->build()
                    ))->setIfthenpayController($this->ifthenpayController);
                default:
                    throw new \Exception('Unknown Cancel Order Class');
            }
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
