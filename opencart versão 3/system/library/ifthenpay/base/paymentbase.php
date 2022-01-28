<?php

declare(strict_types=1);

namespace Ifthenpay\Base;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Utility\Mix;


abstract class PaymentBase
{
    protected $dataConfig;
    protected $gatewayBuilder;
    protected $paymentDefaultData;
    protected $twigDefaultData;
    protected $paymentGatewayResultData;
    protected $ifthenpayGateway;
    protected $paymentDataFromDb;
    protected $paymentTable;
    protected $paymentMethod;
    protected $paymentMethodAlias;
    protected $ifthenpayController;
    protected $redirectUrl;
    protected $mix;

    public function __construct(
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $configData,
        $ifthenpayController,
        Mix $mix,
        TwigDataBuilder $twigDataBuilder = null
    ) {
        $this->gatewayBuilder = $gatewayBuilder;
        $this->paymentDefaultData = $paymentDefaultData->getData();
        $this->twigDefaultData = $twigDataBuilder;
        $this->ifthenpayGateway = $ifthenpayGateway;
        $this->configData = $configData;
        $this->ifthenpayController = $ifthenpayController;
        $this->mix = $mix;
    }

    public function getRedirectUrl(): array
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(bool $redirect = false, string $url = '')
    {
        $this->redirectUrl = [
            'redirect' => $redirect,
            'url' => $url
        ];
        return $this;
    }

    public function setTwigVariables(): void
    {
        $this->twigDefaultData->setIfthenpayPaymentPanelTitle(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelTitle'));
    }

    public function setPaymentTable(string $tableName): PaymentBase
    {
        $this->paymentTable = $tableName;
        return $this;
    }

    public function getTwigVariables(): TwigDataBuilder
    {
        return $this->twigDefaultData;
    }

    abstract protected function setGatewayBuilderData(): void;
    abstract protected function saveToDatabase(): void;
    abstract public function getFromDatabaseById(): void;
    
    /**
     * Get the value of paymentDataFromDb
     */
    public function getPaymentDataFromDb()
    {
        return $this->paymentDataFromDb;
    }

    /**
     * Get the value of paymentGatewayResultData
     */ 
    public function getPaymentGatewayResultData()
    {
        return $this->paymentGatewayResultData;
    }
}
