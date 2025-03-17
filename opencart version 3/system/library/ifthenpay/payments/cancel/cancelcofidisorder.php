<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Traits\Payments\ConvertCurrency;
use Ifthenpay\Request\WebService;
use GuzzleHttp\Client;


class CancelCofidisOrder extends CancelOrder
{
	use ConvertCurrency;

	protected $paymentMethod = Gateway::COFIDIS;

	public function cancelOrder(): void
	{
		if ($this->ifthenpayController->config->get('payment_cofidis_activate_cancelCofidisOrder')) {
			$this->setPendingOrders();
			if (!empty($this->pendingOrders)) {
				foreach ($this->pendingOrders as $order) {
					$cofidisPayment = $this->ifthenpayController->model_extension_payment_cofidis->getPaymentByOrderId($order['order_id'])->row;
					if (!empty($cofidisPayment)) {
						$this->setGatewayDataBuilderBackofficeKey();
						$this->gatewayDataBuilder->setCofidisKey($this->ifthenpayController->config->get('payment_cofidis_cofidisKey'));
						$this->gatewayDataBuilder->setReferencia($cofidisPayment['requestId']);
						$this->gatewayDataBuilder->setTotalToPay((string) $this->convertToCurrency($order, $this->ifthenpayController));

						if ($this->isBeyondDeadline($order, 60)) {

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
		$today = new \DateTime(date("Y-m-d G:i"));
		$time = new \DateTime($order['date_added']);

		$time->add(new \DateInterval('PT' . $deadlineMinutes . 'M'));

		return $time < $today;
	}
}
