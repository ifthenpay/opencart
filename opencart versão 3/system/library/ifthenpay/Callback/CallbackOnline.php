<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackProcessInterface;

class CallbackOnline extends CallbackProcess implements CallbackProcessInterface
{
        
    public function process(): void
    {
        $this->setPaymentData();

        if (empty($this->paymentData)) {
            $this->executePaymentNotFound();
        } else {
            try {
                $this->ifthenpayController->load->language('extension/payment/ifthenpay');
                $paymentStatus = $this->status->getTokenStatus(
                    $this->token->decrypt($this->request['qn'])
                );
                $this->setOrder();
                
                if ($paymentStatus === 'success') {
                    $orderTotal = floatval($this->order['total']);
                    $requestValor = floatval($this->request['amount']);
                    if (round($orderTotal, 2) !== round($requestValor, 2)) {
                        $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
			            $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_message');
                        
                    }
                    $this->changeIfthenpayPaymentStatus('paid');
                    $this->ifthenpayController->model_checkout_order->addOrderHistory(
                        $this->paymentData['order_id'], 
                        $this->ifthenpayController->config->get('payment_ifthenpay_ccard_order_status_complete_id'),
                        $this->ifthenpayController->language->get('paymentConfirmedSuccess'),
                        true,
                        true
                    );
                    
                    $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
			        $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = '';
   
                } else if($paymentStatus === 'cancel') {
                    $this->changeIfthenpayPaymentStatus('cancel');
                    $this->ifthenpayController->model_checkout_order->addOrderHistory(
                        $this->paymentData['order_id'], 
                        $this->ifthenpayController->config->get('payment_ifthenpay_ccard_order_status_canceled_id'),
                        $this->ifthenpayController->language->get('ccard_error_canceled'),
                        true,
                        true
                    );

                    $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
                    $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_canceled');
                } else {
                    $this->changeIfthenpayPaymentStatus('error');
                    $this->ifthenpayController->model_checkout_order->addOrderHistory(
                        $this->paymentData['order_id'], 
                        $this->ifthenpayController->config->get('payment_ifthenpay_ccard_order_status_failed_id'),
                        $this->ifthenpayController->language->get('ccard_error_failed'),
                        true,
                        true
                    );

                        $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
                        $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $this->ifthenpayController->language->get('ccard_error_failed');
                    
                }
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
                $this->ifthenpayController->response->redirect($this->ifthenpayController->url->link('checkout/success', true));
            } catch (\Throwable $th) {
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['orderId'] = $this->paymentData['order_id'];
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_success'] = '';
                $this->ifthenpayController->session->data['ifthenpayPaymentReturn']['ccard_error'] = $th->getMessage();
                $this->ifthenpayController->response->redirect($this->ifthenpayController->url->link('checkout/success', true));
            }
        }
    }
}
