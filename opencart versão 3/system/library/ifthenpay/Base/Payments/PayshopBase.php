<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Base\PaymentBase;

class PayshopBase extends PaymentBase
{
    protected $paymentMethod = 'payshop';
    protected $paymentMethodAlias = 'Payshop';


    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setPayshopKey($this->configData['payment_ifthenpay_payshop_payshopKey']);
        $this->gatewayBuilder->setValidade($this->configData['payment_ifthenpay_payshop_validade']);
    }
}
