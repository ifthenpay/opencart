<?php

declare(strict_types=1);

namespace Ifthenpay\Traits\Payments;

trait GatewayDataBuilderBackofficeKey
{
    protected function setGatewayDataBuilderBackofficeKey(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_backofficeKey'));
    }
}
