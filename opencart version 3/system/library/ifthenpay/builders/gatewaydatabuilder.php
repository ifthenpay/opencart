<?php

declare(strict_types=1);

namespace Ifthenpay\Builders;

use Ifthenpay\Contracts\Builders\GatewayDataBuilderInterface;

class GatewayDataBuilder extends DataBuilder implements GatewayDataBuilderInterface
{
	public function setSubEntidade(string $value): GatewayDataBuilderInterface
	{
		$this->data->subEntidade = $value;
		return $this;
	}

	public function setMbwayKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->mbwayKey = $value;
		return $this;
	}

	public function setPayshopKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->payshopKey = $value;
		return $this;
	}

	public function setCCardKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->ccardKey = $value;
		return $this;
	}
	public function setCofidisKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->cofidisKey = $value;
		return $this;
	}
	public function setPixKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->pixKey = $value;
		return $this;
	}
	public function setIfthenpayGatewayKey(string $value): GatewayDataBuilderInterface
	{
		$this->data->ifthenpayGatewayKey = $value;
		return $this;
	}
}
