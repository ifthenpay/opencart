<?php
declare(strict_types=1);

namespace Ifthenpay\Contracts\Payments;

interface PaymentReturnInterface
{
    public function setTwigVariables(): void;
    public function getPaymentReturn();
}
