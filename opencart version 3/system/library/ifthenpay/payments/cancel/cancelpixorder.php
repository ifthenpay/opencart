<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;


class CancelPixOrder extends CancelOrder
{
	use ConvertCurrency;

	protected $paymentMethod = Gateway::PIX;

	public function cancelOrder(): void
	{
		if ($this->ifthenpayController->config->get('payment_pix_activate_cancelPixOrder')) {
			$this->setPendingOrders();
			if (!empty($this->pendingOrders)) {
				foreach ($this->pendingOrders as $order) {

					$pixPayment = $this->ifthenpayController->model_extension_payment_pix->getPaymentByOrderId($order['order_id'])->row;

					if (!empty($pixPayment)) {

						if ($this->isBeyondDeadline($order, 30)) {

							$this->ifthenpayController->load->language('extension/payment/' . $this->paymentMethod);
							$this->ifthenpayController->load->model('checkout/order');
							$this->ifthenpayController->model_checkout_order->addOrderHistory(
								$order['order_id'],
								$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_order_status_canceled_id'),
								$this->ifthenpayController->language->get($this->paymentMethod . 'OrderExpiredCanceled'),
								true,
								true
							);
							$this->logCancelOrder($order['order_id']);
						}
					}
				}
			}
		}
	}


	private function isBeyondDeadline(array $order, int $deadlineMinutes)
	{
		date_default_timezone_set('Europe/Lisbon');
		$currentTime = new \DateTime(date("Y-m-d G:i"));
		$deadline = new \DateTime($order['date_added']);
		$deadline->add(new \DateInterval('PT' . $deadlineMinutes . 'M'));

		return $deadline < $currentTime;
	}
}
