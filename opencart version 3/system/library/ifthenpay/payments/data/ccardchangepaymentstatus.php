<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;

class CCardChangePaymentStatus extends CheckPaymentStatusBase
{
    use ConvertCurrency;

    protected $paymentMethod = Gateway::CCARD;

    protected function setGatewayDataBuilder(): void
    {
        $this->setGatewayDataBuilderBackofficeKey();
        $this->gatewayDataBuilder->setCCardKey($this->ifthenpayController->config->get('payment_ccard_ccardKey'));
    }

    protected function getPendingOrders(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ccard');
        $this->pendingOrders = $this->ifthenpayController->model_extension_payment_ccard->getAllPendingOrders();
    }
    
    public function changePaymentStatus(): void
    {
        $this->setGatewayDataBuilder();
        $this->getPendingOrders();
        if (!empty($this->pendingOrders)) {
            foreach ($this->pendingOrders as $pendingOrder) {
                $ccardPayment = $this->ifthenpayController->model_extension_payment_ccard->getPaymentByOrderId($pendingOrder['order_id'])->row;
                if (!empty($ccardPayment)) {
                    $this->gatewayDataBuilder->setReferencia((string) $pendingOrder['order_id']);
                    $this->gatewayDataBuilder->setTotalToPay((string)$this->convertToCurrency($pendingOrder, $this->ifthenpayController));
                    if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $this->savePaymentStatus($ccardPayment);
                    }
                }
                
            }
        }
    }
}