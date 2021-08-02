<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;

class PayshopChangePaymentStatus extends CheckPaymentStatusBase
{
    protected function setGatewayDataBuilder(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->ifthenpayController->config->get('payment_payshop_backofficeKey'));
        $this->gatewayDataBuilder->setPayshopKey($this->ifthenpayController->config->get('payment_payshop_payshopKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/payshop');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_payshop->getAllPayshopPendingOrders();
    }
    
    public function changePaymentStatus(): void
    {
        if ($this->ifthenpayController->config->get('payment_payshop_backofficeKey') && $this->ifthenpayController->config->get('payment_payshop_payshopKey')) {
            $this->setGatewayDataBuilder();
            $this->getPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $pendingOrder) {
                    $payshopPayment = $this->ifthenpayController->model_extension_payment_payshop->getPaymentByOrderId($pendingOrder['order_id'])->row;
                    if (!empty($payshopPayment)) {
                        $this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
                        if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            $this->ifthenpayController->model_extension_payment_payshop->updatePaymentStatus(
                                $payshopPayment['id_ifthenpay_payshop'], 
                                'paid'
                            );
                            $catalogChangeOrderStatusEndpoint = $this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/') . '/index.php?route=extension/payment/payshop/changeOrderStatusFromWebservice';
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
}