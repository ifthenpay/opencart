<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\Payments\PixBase;
use Ifthenpay\Contracts\Order\OrderDetailInterface;

class PixOrderDetail extends PixBase implements OrderDetailInterface
{
	public function setTwigVariables(): void
	{
		parent::setTwigVariables();
		$this->twigDefaultData->setIdPedido($this->paymentDataFromDb['requestId']);
	}

	public function getOrderDetail(): OrderDetailInterface
	{
		$this->getFromDatabaseById();
		$this->setTwigVariables();
		return $this;
	}
}
