<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;

class MultibancoBase extends PaymentBase
{
    protected $paymentMethod = Gateway::MULTIBANCO;
    protected $paymentMethodAlias = 'Multibanco';

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setEntidade($this->configData['payment_multibanco_entidade']);
        $this->gatewayBuilder->setSubEntidade($this->configData['payment_multibanco_subEntidade']);
        if (isset($this->configData['payment_multibanco_deadline']) && $this->configData['payment_multibanco_deadline'] != '') {
            $this->gatewayBuilder->setValidade($this->configData['payment_multibanco_deadline']);
        }
        
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
		$multibancoPayment = $this->ifthenpayController
            ->model_extension_payment_multibanco->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])->row;
        if (empty($multibancoPayment)) {
            $this->ifthenpayController->model_extension_payment_multibanco
                ->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
        } else {
            $this->ifthenpayController->model_extension_payment_multibanco
                ->updatePendingMultibanco($multibancoPayment['id_ifthenpay_multibanco'], $this->paymentDefaultData, $this->paymentGatewayResultData);
        }
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_multibanco
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }
}
