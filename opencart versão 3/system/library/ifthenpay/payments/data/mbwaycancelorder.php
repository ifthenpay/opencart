<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\MbWayPaymentStatus;

class MbwayCancelOrder
{
	private $ifthenpayController;
    private $gatewayDataBuilder;
    private $mbwayPaymentStatus;

    public function __construct(GatewayDataBuilder $gatewayDataBuilder, MbWayPaymentStatus $mbwayPaymentStatus)
	{
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->mbwayPaymentStatus = $mbwayPaymentStatus;
	}
    
    
    public function cancelOrder(): void
    {
        if ($this->ifthenpayController->config->get('payment_mbway_activate_cancelMbwayOrder')) {
            $this->ifthenpayController->load->model('extension/payment/mbway');
            $mbwayPendingOrders = $this->ifthenpayController->model_extension_payment_mbway->getAllMbwayPendingOrders();
            if (!empty($mbwayPendingOrders)) {
                date_default_timezone_set('Europe/Lisbon');
                foreach ($mbwayPendingOrders as $mbwayOrder) {
                    $mbwayPayment = $this->ifthenpayController->model_extension_payment_mbway->getPaymentByOrderId($mbwayOrder['order_id'])->row;
                    $this->gatewayDataBuilder->setMbwayKey($this->ifthenpayController->config->get('payment_mbway_mbwayKey'));
                    $this->gatewayDataBuilder->setIdPedido($mbwayPayment['id_transacao']);

                    if (!$this->mbwayPaymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
                        $minutes_to_add = 30;
                        $time = new \DateTime($mbwayOrder['date_added']);
                        $time->add(new \DateInterval('PT' . $minutes_to_add . 'M'));
                        $today = new \DateTime(date("Y-m-d G:i"));
                        if ($time < $today) {
                            $this->ifthenpayController->load->language('extension/payment/mbway');
                            $this->ifthenpayController->load->model('checkout/order');
                            $this->ifthenpayController->model_checkout_order->addOrderHistory(
                                $mbwayOrder['order_id'], 
                                $this->ifthenpayController->config->get('payment_mbway_order_status_canceled_id'),
                                $this->ifthenpayController->language->get('mbwayOrderExpiredCanceled'),
                                true,
                                true
                            );
                            $this->ifthenpayController->model_extension_payment_mbway->log($mbwayOrder['order_id'], 'MB WAY order canceled with success');
                        }
                    }
                }
            }
        }
    }

    /**
     * Set the value of ifthenpayController
     *
     * @return  self
     */ 
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }
}


