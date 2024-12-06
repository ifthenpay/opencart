<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Factory\Payment\StrategyFactory;
use Ifthenpay\Payments\Data\CCardPaymentReturn;
use Ifthenpay\Payments\Data\MbwayPaymentReturn;
use Ifthenpay\Payments\Data\PayshopPaymentReturn;
use Ifthenpay\Payments\Data\MultibancoPaymentReturn;
use Ifthenpay\Payments\Data\CofidisPaymentReturn;
use Ifthenpay\Payments\Data\PixPaymentReturn;
use Ifthenpay\Contracts\Payments\PaymentReturnInterface;
use Ifthenpay\Payments\Data\IfthenpaygatewayPaymentReturn;
use Ifthenpay\Payments\Gateway;


class PaymentReturnFactory extends StrategyFactory
{
	public function build(): PaymentReturnInterface
	{
		switch ($this->type) {
			case Gateway::MULTIBANCO:
				return new MultibancoPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::MBWAY:
				return new MbwayPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::PAYSHOP:
				return new PayshopPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::CCARD:
				return new CCardPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData,
					$this->token,
					$this->status
				);
			case Gateway::COFIDIS:
				return new CofidisPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData,
					$this->token,
					$this->status
				);
			case Gateway::PIX:
				return new PixPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData,
					$this->status
				);
			case Gateway::IFTHENPAYGATEWAY:
				return new IfthenpaygatewayPaymentReturn(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData,
					$this->token,
					$this->status
				);
			default:
				throw new \Exception('Unknown Payment Return Class');
		}
	}
}

// TODO: might not need the token for cofidis
