<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;

class CancelPayshopOrder extends CancelOrder
{
    protected $paymentMethod = Gateway::PAYSHOP;
  
    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_payshop_activate_cancelPayshopOrder')) {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $payshopPayment = $this->ifthenpayController->model_extension_payment_payshop->getPaymentByOrderId($order['order_id'])->row;
                    if (!empty($payshopPayment)) {
                        if ($payshopPayment['validade']) {
                            $this->setGatewayDataBuilderBackofficeKey();
                            $this->gatewayDataBuilder->setPayshopKey($this->ifthenpayController->config->get('payment_payshop_payshopKey'));
                            $this->gatewayDataBuilder->setReferencia($payshopPayment['referencia']);
                            $this->gatewayDataBuilder->setTotalToPay(
                                (string)$this->ifthenpayController->currency->format($order['total'], 
                                    $order['currency_code'], 
                                    $order['currency_value'], 
                                    false
                                )
                            );
                            if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                                if ($payshopPayment['validade']) {
                                    $this->checkTimeChangeStatus($order, null, $payshopPayment['validade'], 'Ymd');
                                } else {
                                    $this->checkTimeChangeStatus($order, $this->ifthenpayController->config->get('payment_payshop_validade'), null, null);
                                }  
                            }
                            $this->logCancelOrder($order['order_id']);
                        } else {
                            $this->ifthenpayController->model_extension_payment_payshop
                                ->log($order['order_id'], 'Payshop order was not canceled because deadline is not defined');
                        }
                    }
                }
            }
        }
    }
}


