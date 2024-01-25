<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;

class CofidisChangePaymentStatus extends CheckPaymentStatusBase
{
	use ConvertCurrency;

	protected $paymentMethod = Gateway::COFIDIS;

	protected function setGatewayDataBuilder(): void
	{
		$this->setGatewayDataBuilderBackofficeKey();
		$this->gatewayDataBuilder->setCofidisKey($this->ifthenpayController->config->get('payment_cofidis_cofidisKey'));
	}

	protected function getPendingOrders(): void
	{
		$this->ifthenpayController->load->model('extension/payment/cofidis');
		$this->pendingOrders = $this->ifthenpayController->model_extension_payment_cofidis->getAllPendingOrders();
	}

	public function changePaymentStatus(): void
	{
		$this->setGatewayDataBuilder();
		$this->getPendingOrders();
		if (!empty($this->pendingOrders)) {
			foreach ($this->pendingOrders as $pendingOrder) {
				$cofidisPayment = $this->ifthenpayController->model_extension_payment_cofidis->getPaymentByOrderId($pendingOrder['order_id'])->row;
				if (!empty($cofidisPayment)) {
					$this->gatewayDataBuilder->setReferencia((string) $pendingOrder['order_id']);
					$this->gatewayDataBuilder->setTotalToPay((string) $this->convertToCurrency($pendingOrder, $this->ifthenpayController));
					if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
						$this->savePaymentStatus($cofidisPayment);
					}
				}

			}
		}
	}
}
