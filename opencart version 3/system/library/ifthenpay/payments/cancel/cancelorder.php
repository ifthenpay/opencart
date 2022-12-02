<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Traits\Payments\GatewayDataBuilderBackofficeKey;
use Ifthenpay\Builders\GatewayDataBuilder;


abstract class CancelOrder
{
    use GatewayDataBuilderBackofficeKey;
    
    protected $gatewayDataBuilder;
    protected $paymentStatus;
    protected $paymentRepository;
    protected $pendingOrders;
    protected $paymentMethod;
    protected $ifthenpayController;
    protected $dynamicModelName;

    public function __construct(
        GatewayDataBuilder $gatewayDataBuilder, 
        PaymentStatusInterface $paymentStatus
    )
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->paymentStatus = $paymentStatus;
        $this->dynamicModelName = 'model_extension_payment_' . $this->paymentMethod;
	}

    protected function setPendingOrders(): void
    {
        $this->pendingOrders = $this->ifthenpayController->{$this->dynamicModelName}->getAllPendingOrders();
        $this->ifthenpayController->{$this->dynamicModelName}->log($this->pendingOrders, 'pending orders retrieved with success - ' .  $this->paymentMethod);
    }

    protected function checkTimeChangeStatus(array $order, string $days = null, string $paymentDeadline = null, string $dateFormat = null)
    {
        date_default_timezone_set('Europe/Lisbon');
        $today = new \DateTime(date("Y-m-d G:i"));
        $time = new \DateTime($order['date_added']);
        if (!is_null($days) && is_null($paymentDeadline) && is_null($dateFormat)) {
            $time->add(new \DateInterval('P' . $days . 'D'));
            $time->settime(0,0);
            $today->settime(0,0);
        } else if (!is_null($paymentDeadline) && !is_null($dateFormat) && is_null($days)) {
            $time = \DateTime::createFromFormat($dateFormat, $paymentDeadline);
            $time->settime(0,0);
            $today->settime(0,0);
        } else {
            $time->add(new \DateInterval('PT' . 30 . 'M'));
        }
        
        if ($time < $today) {
            $this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);
            $this->ifthenpayController->load->model('checkout/order');
            $this->ifthenpayController->model_checkout_order->addOrderHistory(
                $order['order_id'], 
                $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_canceled_id'),
                $this->ifthenpayController->language->get($this->paymentMethod . 'OrderExpiredCanceled'),
                true,
                true
            );
        }
    }

    protected function logCancelOrder(string $orderId): void
    {
        $this->ifthenpayController->{$this->dynamicModelName}->log($orderId, $this->paymentMethod . ' order canceled with success');
    }

    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        $this->ifthenpayController->load->model('extension/payment/' . $this->paymentMethod);

        return $this;
    }

    abstract function cancelOrder(): void;

    
}
