<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\IfthenpaygatewayBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class IfthenpaygatewayOrderDetail extends IfthenpaygatewayBase implements OrderDetailInterface
{

    public function setTwigVariables(): void
    {
        parent::setTwigVariables();
		$this->twigDefaultData->setIfthenpaygatewayLink($this->paymentDataFromDb['payment_url'] ?? '');

		if(isset($this->paymentDataFromDb['deadline']) && $this->paymentDataFromDb['deadline'] != ''){
			$unformatedDate = $this->paymentDataFromDb['deadline'];
			$formatedDate = (\DateTime::createFromFormat("Ymd", $unformatedDate))->format('d/m/Y');
			$this->twigDefaultData->setIfthenpaygatewayDeadline($formatedDate);
            $this->twigDefaultData->setIfthenpayPaymentPanelValidade($this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade'));
		}
    }

    public function getOrderDetail(): OrderDetailInterface
    {
        $this->getFromDatabaseById();
        $this->setTwigVariables();
        return $this;
    }
}
