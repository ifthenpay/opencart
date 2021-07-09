<?php

declare(strict_types=1);

namespace Ifthenpay\Base;

use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Request\WebService;


abstract class CheckPaymentStatusBase
{
    protected $gatewayDataBuilder;
    protected $ifthenpayController;
    protected $paymentStatus;
    protected $pendingOrders;
    protected $webservice;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
        Webservice $webservice,
        $ifthenpayController
    ) {
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatus = $paymentStatus;
        $this->ifthenpayController = $ifthenpayController;
        $this->webservice = $webservice;
    }

    abstract protected function setGatewayDataBuilder(): void;
    abstract protected function getPendingOrders(): void;
    abstract public function changePaymentStatus(): void;
}
