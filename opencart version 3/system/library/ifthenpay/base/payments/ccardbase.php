<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Status;
use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Utility\Mix;

class CCardBase extends PaymentBase
{
    protected $paymentMethod = Gateway::CCARD;
    private $token;

    public function __construct(
        DataBuilder $paymentDefaultData,
        GatewayDataBuilder $gatewayBuilder,
        Gateway $ifthenpayGateway,
        array $configData,
        $ifthenpayController,
        Mix $mix,
        TwigDataBuilder $twigDataBuilder = null,
        Token $token = null,
        Status $status = null
    ) {
        parent::__construct($paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $configData, $ifthenpayController, $mix, $twigDataBuilder);
        $this->token = $token;
        $this->status = $status;
        $this->paymentMethodAlias = $this->ifthenpayController->language->get('ccardAlias');
    }

    private function getUrlCallback(): string
    {
        return $this->paymentDefaultData->order['store_url'] . 
            'index.php?route=extension/payment/ccard/callback';
    }

    protected function saveToDatabase(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ccard');
		
		$this->ifthenpayController->model_extension_payment_ccard->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayBuilder->setCCardKey($this->configData['payment_ccard_ccardKey']);
        $this->gatewayBuilder->setSuccessUrl($this->getUrlCallback() . '&type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order['order_id'] . '&qn=' . 
            $this->token->encrypt($this->status->getStatusSucess())
        );
        $this->gatewayBuilder->setErrorUrl($this->getUrlCallback() . '&type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order['order_id'] . '&qn=' . 
            $this->token->encrypt($this->status->getStatusError())
        );
        $this->gatewayBuilder->setCancelUrl($this->getUrlCallback() . '&type=online&payment=ccard&orderId=' . $this->paymentDefaultData->order['order_id'] . '&qn=' . 
            $this->token->encrypt($this->status->getStatusCancel())
        );
    }

    public function getFromDatabaseById(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ccard');
		
		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_ccard
            ->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
            ->row; 
    }
}
