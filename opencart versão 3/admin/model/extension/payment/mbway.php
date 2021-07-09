<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Config\IfthenpaySql;

class ModelExtensionPaymentMbway extends Model {

	public function install(IfthenpayContainer $ifthenpayContainer, string $userPaymentMethod) {
		$ifthenpayContainer->getIoc()->make(IfthenpaySql::class)->setIfthenpayModel($this)->setUserPaymentMethod($userPaymentMethod)->install();
	}

	public function uninstall(IfthenpayContainer $ifthenpayContainer, string $userPaymentMethod) {
        $ifthenpayContainer
        ->getIoc()
        ->make(IfthenpaySql::class)
        ->setIfthenpayModel($this)
        ->setUserPaymentMethod($userPaymentMethod)
        ->uninstall();
	}

    public function getAllMbwayPendingOrders(): array
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE `payment_code` = 'mbway' AND `order_status_id` =" . $this->config->get('payment_mbway_order_status_id'));
		
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function getPaymentByOrderId(string $orderId): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE order_id = '" . $this->db->escape($orderId) . "'");     
    }

	public function log($data, $title = null) {
		$log = new Log('ifthenpay.log');
		$log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
	}

	public function deleteSettingByKey(string $key, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
	}

	public function updatePaymentStatus(string $paymentId, string $paymentStatus): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_mbway` = '" . $paymentId . "' LIMIT 1");
    }
}