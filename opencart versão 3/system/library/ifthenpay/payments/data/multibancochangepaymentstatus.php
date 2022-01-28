<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;

class MultibancoChangePaymentStatus extends CheckPaymentStatusBase
{
    protected $paymentMethod = Gateway::MULTIBANCO;
    
    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->ifthenpayController->config->get('payment_multibanco_backofficeKey'));
        $this->gatewayDataBuilder->setEntidade($this->ifthenpayController->config->get('payment_multibanco_entidade'));
        $this->gatewayDataBuilder->setSubEntidade($this->ifthenpayController->config->get('payment_multibanco_subEntidade'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_multibanco->getAllPendingOrders();
    }
    
    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingOrders)) {
            foreach ($this->pendingOrders as $pendingOrder) {
                $multibancoPayment = $this->ifthenpayController->model_extension_payment_multibanco->getPaymentByOrderId($pendingOrder['order_id'])->row;
                if (!empty($multibancoPayment)) {
                    $this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->savePaymentStatus($multibancoPayment);               
                    }
                }
            }
        }
    }
}
