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

                if(isset($this->paymentData['status']) && $this->paymentData['status'] === 'paid' && (isset($_GET['test']) && $_GET['test'] === 'true')) {
                    throw new \Exception('Pagamento já efetuado');
                }

                $this->setOrder();
                $this->callbackValidate->setHttpRequest($this->request)
                ->setOrder($this->order)
                ->setConfigPaidStatus($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_id'))
                ->setConfigurationChaveAntiPhishing($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_chaveAntiPhishing'))
                ->setPaymentDataFromDb($this->paymentData)
                ->validate();
                $this->changeIfthenpayPaymentStatus('paid');
                $this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);
                $this->ifthenpayController->model_checkout_order->addOrderHistory(
                    $this->paymentData['order_id'], 
                    $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_complete_id'),
                    $this->ifthenpayController->language->get('paymentConfirmedSuccess'),
                    true,
                    true
                );

                if (isset($_GET['test']) && $_GET['test'] === 'true') {
                    http_response_code(200);

                    $response = [
                        'status' => 'success',
                        'message' => 'Callback received and validated with success for payment method ', pathinfo(__FILE__)['filename'] . $this->paymentMethod
                    ];


                    die(json_encode($response));
                }

                http_response_code(200);
                die('ok');           
            } catch (\Throwable $th) {

                if (isset($_GET['test']) && $_GET['test'] === 'true') {
                    http_response_code(200);

                    $response = [
                        'status' => 'warning',
                        'message' => $th->getMessage(),
                    ];


                    die(json_encode($response));
                }

                http_response_code(400);
                die($th->getMessage());
            }
        }
    }
}
