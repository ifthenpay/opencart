<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;

class CancelMultibancoOrder extends CancelOrder
{
    protected $paymentMethod = Gateway::MULTIBANCO;
  
    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_multibanco_activate_cancelMultibancoOrder')) {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $multibancoPayment = $this->ifthenpayController->model_extension_payment_multibanco->getPaymentByOrderId($order['order_id'])->row;
                    if (!empty($multibancoPayment)) {
                        if ($multibancoPayment['requestId'] && $multibancoPayment['validade']) {
                            $this->setGatewayDataBuilderBackofficeKey();
                            $this->gatewayDataBuilder->setEntidade($this->ifthenpayController->config->get('payment_multibanco_entidade'));
                            $this->gatewayDataBuilder->setSubEntidade($this->ifthenpayController->config->get('payment_multibanco_subEntidade'));
                            $this->gatewayDataBuilder->setReferencia($multibancoPayment['referencia']);
                            $this->gatewayDataBuilder->setTotalToPay((string)
                                $this->ifthenpayController->currency->format($order['total'], 
                                    $order['currency_code'], 
                                    $order['currency_value'], 
                                    false
                                )
                            );
                            if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                                if ($multibancoPayment['validade']) {
                                    $this->checkTimeChangeStatus($order, null, $multibancoPayment['validade'], 'd-m-Y');
                                } else {
                                    $this->checkTimeChangeStatus($order, $this->ifthenpayController->config->get('payment_multibanco_deadline'), null, null);
                                }                                
                            }
                            $this->logCancelOrder($order['order_id']);
                        } else {
                            $this->ifthenpayController->model_extension_payment_multibanco
                                ->log($order['order_id'], 'Multibanco order was not canceled because deadline is not defined');
                        }
                    } 
                }
            }
        }
    }
}


