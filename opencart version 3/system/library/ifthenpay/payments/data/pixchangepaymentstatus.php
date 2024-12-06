<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Base\CheckPaymentStatusBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;

class PixChangePaymentStatus extends CheckPaymentStatusBase
{
	use ConvertCurrency;

	protected $paymentMethod = Gateway::PIX;

	protected function setGatewayDataBuilder(): void
	{
		$this->setGatewayDataBuilderBackofficeKey();
		$this->gatewayDataBuilder->setPixKey($this->ifthenpayController->config->get('payment_pix_pixKey'));
	}

	protected function getPendingOrders(): void
	{
		$this->ifthenpayController->load->model('extension/payment/pix');
		$this->pendingOrders = $this->ifthenpayController->model_extension_payment_pix->getAllPendingOrders();
	}

	public function changePaymentStatus(): void
	{
		$this->setGatewayDataBuilder();
		$this->getPendingOrders();
		if (!empty($this->pendingOrders)) {
			foreach ($this->pendingOrders as $pendingOrder) {
				$pixPayment = $this->ifthenpayController->model_extension_payment_pix->getPaymentByOrderId($pendingOrder['order_id'])->row;
				if (!empty($pixPayment)) {
					$this->gatewayDataBuilder->setReferencia((string) $pendingOrder['order_id']);
					$this->gatewayDataBuilder->setTotalToPay((string) $this->convertToCurrency($pendingOrder, $this->ifthenpayController));
					if ($this->paymentStatus->setData($this->gatewayDataBuilder)->getPaymentStatus()) {
						$this->savePaymentStatus($pixPayment);
					}
				}

			}
		}
	}
}
