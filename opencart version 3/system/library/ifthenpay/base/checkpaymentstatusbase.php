<?php

declare(strict_types=1);

namespace Ifthenpay\Base;

use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Request\WebService;
use Ifthenpay\Traits\Payments\GatewayDataBuilderBackofficeKey;


abstract class CheckPaymentStatusBase
{
    use GatewayDataBuilderBackofficeKey;

    protected $gatewayDataBuilder;
    protected $ifthenpayController;
    protected $paymentStatus;
    protected $pendingOrders;
    protected $webService;
    protected $dynamicModelName;
    protected $paymentMethod;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder,
        PaymentStatusInterface $paymentStatus,
        WebService $webService,
        $ifthenpayController
    ) {
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatus = $paymentStatus;
        $this->ifthenpayController = $ifthenpayController;
        $this->webService = $webService;
        $this->dynamicModelName = 'model_extension_payment_' . $this->paymentMethod;
    }

    protected function savePaymentStatus(array $payment): void
    {
        $this->ifthenpayController->{$this->dynamicModelName}->updatePaymentStatus(
            $payment['id_ifthenpay_' . $this->paymentMethod], 
            'paid'
        );
        $this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);
		$this->ifthenpayController->load->model('checkout/order');
		$this->ifthenpayController->model_checkout_order->addOrderHistory(
			$payment['orderId'], 
			$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_complete_id'),
			$this->ifthenpayController->language->get('paymentConfirmedSuccess'),
			true,
			true
		);
		$this->ifthenpayController->{$this->dynamicModelName}->log($payment['orderId'], $this->paymentMethod . ' Order Status Change to paid with success');
    }

    abstract protected function setGatewayDataBuilder(): void;
    abstract protected function getPendingOrders(): void;
    abstract public function changePaymentStatus(): void;
}
