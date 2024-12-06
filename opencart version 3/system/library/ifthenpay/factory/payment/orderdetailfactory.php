<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;

use Ifthenpay\Payments\Data\CCardOrderDetail;
use Ifthenpay\Payments\Data\MbwayOrderDetail;
use Ifthenpay\Payments\Data\PayshopOrderDetail;
use Ifthenpay\Payments\Data\MultibancoOrderDetail;
use Ifthenpay\Payments\Data\CofidisOrderDetail;
use Ifthenpay\Payments\Data\PixOrderDetail;
use Ifthenpay\Contracts\Order\OrderDetailInterface;
use Ifthenpay\Payments\Data\IfthenpaygatewayOrderDetail;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Mix;


class OrderDetailFactory extends StrategyFactory
{
	public function build(): OrderDetailInterface
	{
		switch (strtolower($this->type)) {
			case Gateway::MULTIBANCO:
				return new MultibancoOrderDetail(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::MBWAY:
				return new MbwayOrderDetail(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::PAYSHOP:
				return new PayshopOrderDetail(
					$this->paymentDefaultData,
					$this->gatewayBuilder,
					$this->ifthenpayGateway,
					$this->configData,
					$this->ifthenpayController,
					$this->mix,
					$this->twigDefaultData
				);
			case Gateway::CCARD:
				return new CCardOrderDetail(
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
				return new CofidisOrderDetail(
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
				return new PixOrderDetail(
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
				return new IfthenpaygatewayOrderDetail(
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
				throw new \Exception('Unknown Order Detail Class');
		}
	}
}

// TODO: might not need the token for the cofidis payment
