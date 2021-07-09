<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;

class MultibancoChangePaymentStatus extends CheckPaymentStatusBase
{
    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->ifthenpayController->config->get('payment_multibanco_backofficeKey'));
        $this->gatewayDataBuilder->setEntidade($this->ifthenpayController->config->get('payment_multibanco_entidade'));
        $this->gatewayDataBuilder->setSubEntidade($this->ifthenpayController->config->get('payment_multibanco_subEntidade'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/multibanco');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_multibanco->getAllMultibancoPendingOrders();
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
                        $this->ifthenpayController->model_extension_payment_multibanco->updatePaymentStatus(
                            $multibancoPayment['id_ifthenpay_multibanco'], 
                            'paid'
                        );
                        $catalogChangeOrderStatusEndpoint = $this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/') . '/index.php?route=extension/payment/multibanco/changeOrderStatusFromWebservice';
                        $this->webservice->postRequest(
                            $catalogChangeOrderStatusEndpoint,
                            [
                            'order_id' => $pendingOrder['order_id'],
                            ],
                            false
                        );                    
                    }
                }
            }
        }
    }
}