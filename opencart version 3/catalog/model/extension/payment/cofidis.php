<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentCofidis extends IfthenpayModel
{
	protected $paymentMethod = Gateway::COFIDIS;

	public function getMethod($address, $total)
	{
		if (!$this->config->get('payment_cofidis_cofidisKey')) {
			$this->paymentStatus = false;
		} else {
			$this->paymentStatus = true;
		}

		return parent::getMethod($address, $total);
	}

	public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
	{
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "ifthenpay_cofidis SET requestId = '" . $dataBuilder->idPedido .
			"', order_id = '" . $paymentDefaultData->order['order_id'] .
			"', hash = '" . $dataBuilder->hash .
			"', status = 'pending'"
		);
	}

	public function getCofidisByOrderIdAndHash(string $orderId, string $hash): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_cofidis WHERE order_id = '" . $this->db->escape($orderId) . "' AND hash = '" . $this->db->escape($hash) . "'");
	}

	public function getCofidisByRequestId(string $requestId): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_cofidis WHERE requestId = '" . $this->db->escape($requestId) . "'");
	}
}

?>
