<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Factory\Factory;
use Illuminate\Container\Container;
use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\MbWayPaymentStatus;
use Ifthenpay\Payments\PayshopPaymentStatus;
use Ifthenpay\Payments\MultibancoPaymentStatus;
use Ifthenpay\Payments\Data\MbwayChangePaymentStatus;
use Ifthenpay\Payments\Data\PayshopChangePaymentStatus;
use Ifthenpay\Payments\Data\MultibancoChangePaymentStatus;
use Ifthenpay\Request\WebService;

class PaymentStatusFactory extends Factory
{
    private $gatewayDataBuilder;

    public function __construct(Container $ioc, GatewayDataBuilder $gatewayDataBuilder)
	{
        parent::__construct($ioc);
        $this->gatewayDataBuilder = $gatewayDataBuilder;
    }

    public function build(): CheckPaymentStatusBase {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoChangePaymentStatus($this->gatewayDataBuilder, $this->ioc->make(MultibancoPaymentStatus::class), $this->ioc->make(WebService::class), $this->ifthenpayController);
            case 'mbway':
                return new MbwayChangePaymentStatus($this->gatewayDataBuilder, $this->ioc->make(MbWayPaymentStatus::class), $this->ioc->make(WebService::class),$this->ifthenpayController);
            case 'payshop':
                return new PayshopChangePaymentStatus($this->gatewayDataBuilder, $this->ioc->make(PayshopPaymentStatus::class), $this->ioc->make(WebService::class),$this->ifthenpayController);
            default:
                throw new \Exception('Unknown Payment Change Status Class');
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
