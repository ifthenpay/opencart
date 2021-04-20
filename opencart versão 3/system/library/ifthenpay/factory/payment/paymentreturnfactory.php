<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Factory\Payment\StrategyFactory;
use Ifthenpay\Payments\Data\CCardPaymentReturn;
use Ifthenpay\Payments\Data\MbwayPaymentReturn;
use Ifthenpay\Payments\Data\PayshopPaymentReturn;
use Ifthenpay\Payments\Data\MultibancoPaymentReturn;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;


class PaymentReturnFactory extends StrategyFactory
{
    public function build(): PaymentReturnInterface {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                );
            case 'mbway':
                return new MbwayPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                );
            case 'payshop':
                return new PayshopPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                );
            case 'ccard':
                return new CCardPaymentReturn(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData,
                    $this->token,
                    $this->status
                );
            default:
                throw new \Exception('Unknown Payment Return Class');
        }
    }
}
