<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;

class PayshopBase extends PaymentBase
{
    protected $paymentMethod = Gateway::PAYSHOP;
    protected $paymentMethodAlias = 'Payshop';


    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setPayshopKey($this->configData['payment_payshop_payshopKey']);
        $this->gatewayBuilder->setValidade($this->configData['payment_payshop_validade']);
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/payshop');

        $payshopPayment = $this->ifthenpayController
            ->model_extension_payment_payshop->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])->row;
        if (empty($payshopPayment)) {
            $this->ifthenpayController->model_extension_payment_payshop
                ->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
        } else {
            $this->ifthenpayController->model_extension_payment_payshop
                ->updatePendingPayshop($payshopPayment['id_ifthenpay_payshop'], $this->paymentDefaultData, $this->paymentGatewayResultData);
        }
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/payshop');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_payshop
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }
}
