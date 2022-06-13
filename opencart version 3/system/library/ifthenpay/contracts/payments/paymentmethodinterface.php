<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Payments;

use Ifthenpay\Builders\DataBuilder;

interface PaymentMethodInterface
{
    public function checkValue(): void;
    public function buy(): DataBuilder;
}
