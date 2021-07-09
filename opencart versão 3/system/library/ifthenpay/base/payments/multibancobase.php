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
        $this->gatewayBuilder->setEntidade($this->configData['payment_multibanco_entidade']);
        $this->gatewayBuilder->setSubEntidade($this->configData['payment_multibanco_subEntidade']);
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
		
		$this->ifthenpayController->model_extension_payment_multibanco->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_multibanco
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }
}
