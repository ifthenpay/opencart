<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Base\PaymentBase;

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = 'multibanco';
    protected $paymentMethodAlias = 'Multibanco';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->configData['payment_ifthenpay_multibanco_entidade']);
        $this->gatewayBuilder->setSubEntidade($this->configData['payment_ifthenpay_multibanco_subEntidade']);
    }
}
