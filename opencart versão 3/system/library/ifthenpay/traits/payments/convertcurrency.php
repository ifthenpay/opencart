<?php

declare(strict_types=1);

namespace Ifthenpay\Traits\Payments;

trait ConvertCurrency
{
    protected function convertToCurrency(array $order, $ifthenpayController): float
    {
        if ($order['currency_code'] !== 'EUR') {
            return $ifthenpayController->currency->format($order['total'], 'EUR', '', false);  
        } else {
            return $ifthenpayController->currency->format($order['total'], 
                $order['currency_code'], 
                $order['currency_value'], 
                false
            );
        }
    }
}
