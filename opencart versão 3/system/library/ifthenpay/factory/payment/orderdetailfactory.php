<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Payments\Data\CCardOrderDetail;
use Ifthenpay\Payments\Data\MbwayOrderDetail;
use Ifthenpay\Payments\Data\PayshopOrderDetail;
use Ifthenpay\Payments\Data\MultibancoOrderDetail;
use Ifthenpay\Contracts\Order\OrderDetailInterface;


class OrderDetailFactory extends StrategyFactory
{    
    public function build(): OrderDetailInterface {
        switch (strtolower($this->type)) {
            case 'multibanco':
                return new MultibancoOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                    
            );
            case 'mbway':
                return new MbwayOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                );
            case 'payshop':
                return new PayshopOrderDetail(
                    $this->paymentDefaultData, 
                    $this->gatewayBuilder, 
                    $this->ifthenpayGateway,
                    $this->configData,
                    $this->ifthenpayController,
                    $this->twigDefaultData
                );
            case 'ccard':
                return new CCardOrderDetail(
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
                throw new \Exception('Unknown Order Detail Class');
        }
    }
}
