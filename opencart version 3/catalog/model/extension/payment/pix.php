<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentPix extends IfthenpayModel
{
	protected $paymentMethod = Gateway::PIX;

	public function getMethod($address, $total)
	{
		if (!$this->config->get('payment_pix_pixKey')) {
			$this->paymentStatus = false;
		} else {
			$this->paymentStatus = true;
		}

		return parent::getMethod($address, $total);
	}

	public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
	{
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "ifthenpay_pix SET requestId = '" . $dataBuilder->idPedido .
			"', order_id = '" . $paymentDefaultData->order['order_id'] .
			"', hash = '" . $dataBuilder->hash .
			"', status = 'pending'"
		);
	}

	public function getPixByOrderIdAndHash(string $orderId, string $hash): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_pix WHERE order_id = '" . $this->db->escape($orderId) . "' AND hash = '" . $this->db->escape($hash) . "'");
	}

	public function getPixByRequestId(string $requestId): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_pix WHERE requestId = '" . $this->db->escape($requestId) . "'");
	}
}

?>
