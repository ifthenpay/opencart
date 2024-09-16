<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Cancel;

use Ifthenpay\Payments\Gateway;

class CancelIfthenpaygatewayOrder extends CancelOrder
{
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;

	public function cancelOrder(): void
	{
		if ($this->ifthenpayController->config->get('payment_ifthenpaygateway_activate_cancelIfthenpaygatewayOrder')) {
			$this->setPendingOrders();
			if (!empty($this->pendingOrders)) {
				foreach ($this->pendingOrders as $order) {
					$ifthenpaygatewayPayment = $this->ifthenpayController->model_extension_payment_ifthenpaygateway->getPaymentByOrderId($order['order_id'])->row;
					if (!empty($ifthenpaygatewayPayment)) {

						if ($ifthenpaygatewayPayment['deadline'] != '' && $this->isBeyondDeadline($ifthenpaygatewayPayment['deadline'])) {


							// due to restrictions in api, it can only check the local ifthenpay table state
							if ($ifthenpaygatewayPayment['status'] == 'pending') {
								$this->checkTimeChangeStatus($order);
							}

							$this->logCancelOrder($order['order_id']);
						}
					}
				}
			}
		}
	}



	private function isBeyondDeadline(string $deadline)
	{
		$timezone = new \DateTimeZone('Europe/Lisbon');

		$deadline = \DateTime::createFromFormat('Ymd', $deadline, $timezone);
		$deadlineStr = $deadline->format('Y-m-d');

		$currentDateTime = new \DateTime('now', $timezone);
		$currentDateTimeStr = $currentDateTime->format('Y-m-d');

		return strtotime($deadlineStr) < strtotime($currentDateTimeStr);


	}
}
