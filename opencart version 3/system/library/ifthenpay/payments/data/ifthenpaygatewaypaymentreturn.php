<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Base\Payments\IfthenpaygatewayBase;
use Ifthenpay\Traits\Payments\ConvertCurrency;

class IfthenpaygatewayPaymentReturn extends IfthenpaygatewayBase implements PaymentReturnInterface
{
    use ConvertCurrency;

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
        $this->twigDefaultData->setIfthenpaygatewayLink($this->gatewayBuilder->getData()->paymentUrl);

		if(isset($this->gatewayBuilder->getData()->deadline) && $this->gatewayBuilder->getData()->deadline != ''){
			$unformatedDate = $this->gatewayBuilder->getData()->deadline;
			$formatedDate = (\DateTime::createFromFormat("Ymd", $unformatedDate))->format('d/m/Y');
			$this->twigDefaultData->setIfthenpaygatewayDeadline($formatedDate);
            $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
		}

    }

    public function getPaymentReturn()
    {
        $this->setGatewayBuilderData();
        $this->paymentGatewayResultData = $this->ifthenpayGateway->execute(
            $this->paymentDefaultData->paymentMethod,
            $this->gatewayBuilder,
            strval($this->paymentDefaultData->order['order_id']),
            strval($this->convertToCurrency($this->paymentDefaultData->order, $this->ifthenpayController))
        )->getData();
        $this->saveToDatabase();
        $this->setTwigVariables();
        $this->setRedirectUrl(true, $this->paymentGatewayResultData->paymentUrl);
        return $this;
    }
}
