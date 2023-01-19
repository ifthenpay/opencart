<?php


declare(strict_types=1);

namespace Ifthenpay\Strategy\Payments;


use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Factory\Payment\StrategyFactory;
use Ifthenpay\Traits\Payments\FormatPaymentValue;
use Ifthenpay\Utility\Mix;

class IfthenpayStrategy
{
    use FormatPaymentValue;

    protected $paymentDefaultData;
    protected $twigDefaultData;
    protected $emailDefaultData;
    protected $order;
    protected $paymentValueFormated;
    protected $ifthenpayController;
    protected $factory;
    protected $configData;
    protected $mix;

    public function __construct(
        DataBuilder $paymentDataBuilder,
        TwigDataBuilder $twigDataBuilder,
        StrategyFactory $factory,
        Mix $mix
    )
    {
        $this->paymentDefaultData = $paymentDataBuilder;
        $this->twigDefaultData = $twigDataBuilder;
        $this->emailDefaultData = [];
        $this->factory = $factory;
        $this->mix = $mix;
    }

    protected function getPaymentMethodName(string $paymentCode): string
    {
        $parts = explode('_', $paymentCode);
        return end($parts);
    }

    protected function setDefaultData(): void
    {
        $this->paymentDefaultData->setOrder($this->order);
        $this->paymentDefaultData->setPaymentMethod($this->getPaymentMethodName($this->order['payment_code']));
    }

    protected function setDefaultTwigData(): void
    {
        $this->ifthenpayController->load->language('extension/payment/' . $this->order['payment_code']);
        $this->twigDefaultData->setOrderId($this->order['order_id']);
        $this->twigDefaultData->setTotalToPay($this->paymentValueFormated);
        $this->twigDefaultData->setPaymentMethod($this->getPaymentMethodName($this->order['payment_code']));

        $base_url = HTTP_SERVER ?? 'lost';

        $this->twigDefaultData->setBaseUrl($base_url);
    }

    /**
     * Set the value of configData
     *
     * @return  self
     */
    public function setConfigData($configData)
    {
        $this->configData = $configData;

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */
    public function setOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set the value of ifthenpayController
     *
     * @return  self
     */
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;
        $this->setPaymentValueFormated();
        return $this;
    }
}