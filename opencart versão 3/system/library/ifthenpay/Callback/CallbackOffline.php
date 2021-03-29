<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Callback\CallbackProcess;
use Ifthenpay\Contracts\Callback\CallbackProcessInterface;


class CallbackOffline extends CallbackProcess implements CallbackProcessInterface
{
    public function process(): void
    {       
        $this->setPaymentData();
        
        if (empty($this->paymentData)) {
            $this->executePaymentNotFound();
        } else {
            try {
                $this->setOrder();
                $this->callbackValidate->setHttpRequest($this->request)
                ->setOrder($this->order)
                ->setConfigurationChaveAntiPhishing($this->ifthenpayController->config->get('payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'))
                ->setPaymentDataFromDb($this->paymentData)
                ->validate();
                $this->changeIfthenpayPaymentStatus('paid');
                $this->ifthenpayController->model_checkout_order->addOrderHistory(
                    $this->paymentData['order_id'], 
                    $this->ifthenpayController->config->get('payment_ifthenpay_' . $this->paymentMethod . '_order_status_complete_id'),
                    $this->ifthenpayController->language->get('paymentConfirmedSuccess'),
                    true,
                    true
                );
                http_response_code(200);
                die('ok');           
            } catch (\Throwable $th) {
                http_response_code(400);
                die($th->getMessage());
            }
        }
    }
}
