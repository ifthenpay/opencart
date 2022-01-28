<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;

class CancelCCardOrder extends CancelOrder
{
    use ConvertCurrency;

    protected $paymentMethod = Gateway::CCARD;
      
    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_ccard_activate_cancelCcardOrder')) {
            $this->setPendingOrders();
            if (!empty($this->pendingOrders)) {
                foreach ($this->pendingOrders as $order) {
                    $ccardPayment = $this->ifthenpayController->model_extension_payment_ccard->getPaymentByOrderId($order['order_id'])->row;
                    if (!empty($ccardPayment)) {
                        $this->setGatewayDataBuilderBackofficeKey();
                        $this->gatewayDataBuilder->setCCardKey($this->ifthenpayController->config->get('payment_ccard_ccardKey'));
                        $this->gatewayDataBuilder->setReferencia((string) $order['order_id']);                        
                        $this->gatewayDataBuilder->setTotalToPay((string)$this->convertToCurrency($order, $this->ifthenpayController));
                        if (!$this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                            $this->checkTimeChangeStatus($order);
                        }
                        $this->logCancelOrder($order['order_id']);
                    }
                }
            }
        }
    }
}


