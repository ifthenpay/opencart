<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Status;
use Ifthenpay\Callback\CallbackValidate;
use Ifthenpay\Factory\Callback\CallbackDataFactory;
use Ifthenpay\Utility\TokenExtra;
use Ifthenpay\Payments\Gateway;

class CallbackProcess
{
    protected $paymentMethod;
    protected $paymentData;
    protected $order;
    protected $request;
    protected $ifthenpayController;
    protected $tokenExtra;

    public function __construct(
        CallbackDataFactory $callbackDataFactory, 
        CallbackValidate $callbackValidate, 
        Status $status = null,
        Token $token = null,
        TokenExtra $tokenExtra = null
    )
	{
        $this->callbackDataFactory = $callbackDataFactory;
        $this->callbackValidate = $callbackValidate;
        $this->status = $status;
        $this->token = $token;
        $this->tokenExtra = $tokenExtra;
	}
	    
    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->ifthenpayController->load->model('extension/payment/' . $this->paymentMethod);
        return $this;
    }

    /**
     * Set the value of paymentData
     *
     * @return  self
     */ 
    protected function setPaymentData(): void
    {
        $this->paymentData = $this->callbackDataFactory->setType($this->request['payment'])
            ->build()
            ->getData($this->request, $this->ifthenpayController);

    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    protected function setOrder(): void
    {
        $this->order = $this->ifthenpayController->model_checkout_order->getOrder($this->paymentData['order_id']);
    }

    protected function executePaymentNotFound(): void
    {
        if (isset($this->request['test']) && $this->request['test'] === 'true') {

            http_response_code(200);
            $response = [
                'status' => 'warning',
                'message' => 'Payment not found for payment method ' . $this->paymentMethod
            ];
            die(json_encode($response));
        }

        http_response_code(404);
        die('Pagamento não encontrado');
    }

    protected function changeIfthenpayPaymentStatus(string $status): void
    {
        switch ($this->request['payment']) {
            case Gateway::MULTIBANCO:
                $this->ifthenpayController->load->model('extension/payment/multibanco');		
                $this->ifthenpayController->model_extension_payment_multibanco->updatePaymentStatus(
                    $this->paymentData['id_ifthenpay_multibanco'], 
                    $status
                );
                break;
            case Gateway::MBWAY:
                $this->ifthenpayController->load->model('extension/payment/mbway');		
                $this->ifthenpayController->model_extension_payment_mbway->updatePaymentStatus(
                    $this->paymentData['id_ifthenpay_mbway'], 
                    $status
                );
                break;
            case Gateway::PAYSHOP:
                $this->ifthenpayController->load->model('extension/payment/payshop');		
                $this->ifthenpayController->model_extension_payment_payshop->updatePaymentStatus(
                    $this->paymentData['id_ifthenpay_payshop'], 
                    $status
                );
                break;
            case Gateway::CCARD:
                $this->ifthenpayController->load->model('extension/payment/ccard');		
                $this->ifthenpayController->model_extension_payment_ccard->updatePaymentStatus(
                    $this->paymentData['id_ifthenpay_ccard'], 
                    $status
                );
                break;
            default:
                throw new \Exception("Payment Method model not exist");
                
                break;
        }
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest(array $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;
        $this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);
        $this->ifthenpayController->load->model('checkout/order');
        return $this;
    }

    
}
