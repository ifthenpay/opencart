<?php

declare(strict_types=1);

namespace Ifthenpay\Traits\Payments;

trait FormatPaymentValue
{
    private function setPaymentValueFormated()
    {
        $this->paymentValueFormated = $this->ifthenpayController->currency->format(
            $this->order['total'], 
            $this->order['currency_code'], ''
        );
    }
}





