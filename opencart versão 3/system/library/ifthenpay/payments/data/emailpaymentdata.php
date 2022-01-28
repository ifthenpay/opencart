<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Traits\Payments\FormatPaymentValue;

abstract class EmailPaymentData 
{
    use FormatPaymentValue;

    protected $ifthenpayController;
    protected $order;
    protected $payment;
    protected $paymentMethod;
    protected $dynamicModelName;
    protected $twigDefaultData;
    protected $paymentValueFormated;
    protected $registry;

	public function __construct(TwigDataBuilder $twigDataBuilder)
	{
        $this->twigDefaultData = $twigDataBuilder;
        $this->dynamicModelName = 'model_extension_payment_' . $this->paymentMethod;
	}

    protected function getOrder(): void
    {
        $this->ifthenpayController->load->model( 'checkout/order' );
        $this->order = $this->ifthenpayController->model_checkout_order->getOrder(
            $this->ifthenpayController->request->get['orderId']
        );
        if (empty($this->order)) {
            throw new \Exception('Order not found');
        }
    }

    protected function getPayment(): void
    {
        $this->payment = $this->ifthenpayController->{$this->dynamicModelName}->getPaymentByOrderId($this->order['order_id'])->row;
        if (empty($this->payment)) {
            throw new \Exception('Ifthenpay payment not found');
        }
    }

    protected function setDefaultTwigVariables(): void
    {
        $this->twigDefaultData->setPaymentMethod($this->paymentMethod);
        $this->twigDefaultData->setIfthenpayPaymentPanelTotalToPay(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelTotalToPay')
        );
        $this->setPaymentValueFormated();
        $this->twigDefaultData->setTotalToPay($this->paymentValueFormated);
    }

    public function sendEmail(): void
    {
        $this->getOrder();
        $this->getPayment();
        $this->setTwigVariables();
        require_once(DIR_APPLICATION . 'controller/mail/order.php');
		$this->ifthenpayController->session->data['payment_method']['code'] = $this->paymentMethod;
		$mailOrderController = new \ControllerMailOrder($this->registry);
		$this->ifthenpayController->session->data['ifthenpayPaymentReturn'] = $this->twigDefaultData->toArray();
		$mailOrderController->add($this->order, $this->order['order_status_id'], null, true);
    }

    abstract protected function setTwigVariables(): void;

    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;
        return $this;
    }

    /**
     * Set the value of registry
     *
     * @return  self
     */ 
    public function setRegistry($registry)
    {
        $this->registry = $registry;

        return $this;
    }
}
