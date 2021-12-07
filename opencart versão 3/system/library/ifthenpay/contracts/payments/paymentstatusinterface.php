<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Payments;

use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface as PaymentStatusContract;

interface PaymentStatusInterface
{
    public function getPaymentStatus(): bool;
    public function setData(GatewayDataBuilder $data): PaymentStatusContract;
}
