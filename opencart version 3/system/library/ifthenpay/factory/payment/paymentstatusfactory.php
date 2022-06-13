<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Illuminate\Container\Container;
use Ifthenpay\Request\WebService;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Factory\Factory;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Payments\MultibancoPaymentStatus;
use Ifthenpay\Payments\MbwayPaymentStatus;
use Ifthenpay\Payments\PayshopPaymentStatus;
use Ifthenpay\Payments\CCardPaymentStatus;


class PaymentStatusFactory extends Factory
{
    private $webService;

    public function __construct(
        Container $ioc, 
        WebService $webService
    )
	{
        parent::__construct($ioc);
        $this->webService = $webService;
    }

    public function build(): PaymentStatusInterface {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoPaymentStatus(
                    $this->webService,
                );
            case Gateway::MBWAY:
                return new MbWayPaymentStatus(
                    $this->webService,
                );
            case Gateway::PAYSHOP:
                return new PayshopPaymentStatus(
                    $this->webService,
                );
            case Gateway::CCARD:
                return new CCardPaymentStatus(
                    $this->webService,
                );
            default:
                throw new \Exception('Unknown Payment Change Status Class');
        }
    }
}
