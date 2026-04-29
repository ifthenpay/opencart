<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Cancel;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\Cancel\CancelOrder;
use Ifthenpay\Payments\Cancel\CancelMultibancoOrder;
use Ifthenpay\Payments\Cancel\CancelMbwayOrder;
use Ifthenpay\Payments\Cancel\CancelPayshopOrder;
use Ifthenpay\Payments\Cancel\CancelCCardOrder;
use Ifthenpay\Payments\Cancel\CancelCofidisOrder;
use Ifthenpay\Payments\Cancel\CancelPixOrder;
use Ifthenpay\Payments\Cancel\CancelIfthenpaygatewayOrder;
use Ifthenpay\Payments\Gateway;


class CancelIfthenpayOrderFactory extends Factory
{
	private $gatewayDataBuilder;

	public function __construct(
		GatewayDataBuilder $gatewayDataBuilder,
	) {
		$this->gatewayDataBuilder = $gatewayDataBuilder;
	}

	public function build(): CancelOrder
	{
		switch ($this->type) {
			case Gateway::MULTIBANCO:
				return (
					new CancelMultibancoOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::MBWAY:
				return (
					new CancelMbwayOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::PAYSHOP:
				return (
					new CancelPayshopOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::CCARD:
				return (
					new CancelCCardOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::COFIDIS:
				return (
					new CancelCofidisOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::PIX:
				return (
					new CancelPixOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			case Gateway::IFTHENPAYGATEWAY:
				return (
					new CancelIfthenpaygatewayOrder(
						$this->gatewayDataBuilder
					)
				)->setIfthenpayController($this->ifthenpayController);
			default:
				throw new \Exception('Unknown Cancel Order Class');
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
