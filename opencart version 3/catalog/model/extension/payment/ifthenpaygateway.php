<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentIfthenpaygateway extends IfthenpayModel
{
	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;

	public function getMethod($address, $total)
	{

		if (!$this->config->get('payment_ifthenpaygateway_ifthenpaygatewayKey')) {
			$this->paymentStatus = false;
		} else if ($this->config->get('config_currency') !== 'EUR') {
			$this->paymentStatus = false;
		} else {
			$this->paymentStatus = true;
		}

		return parent::getMethod($address, $total);
	}

	public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
	{
		$this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_ifthenpaygateway SET order_id = '" . $paymentDefaultData->order['order_id'] . "', deadline = '" . $dataBuilder->deadline . "', status = 'pending', payment_url = '" . $dataBuilder->paymentUrl . "'");
	}

	public function updatePendingIfthenpaygateway(string $id, \stdClass $paymentDefaultData, \stdClass $dataBuilder): void
	{
		$this->db->query(
			"UPDATE `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` SET `order_id` = '" . $paymentDefaultData->order['order_id'] . "', deadline = '" . $dataBuilder->deadline . "', `status` = 'pending' WHERE `id_ifthenpay_ifthenpaygateway` = '" . $id . "' LIMIT 1"
		);
	}


	public function getIfthenpaygatewayByOrderId(string $orderId): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ifthenpaygateway WHERE order_id = '" . $this->db->escape($orderId) . "'");
	}


	public function getIfthenpaygatewayByIdTransacao(string $idPedido): \stdClass
	{
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ifthenpaygateway WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
	}
}
