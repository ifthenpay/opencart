<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Config\IfthenpaySql;

class ModelExtensionPaymentCcard extends Model {

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

	public function log($data, $title = null) {
		$log = new Log('ifthenpay.log');
		$log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
	}

	public function deleteSettingByKey(string $key, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
	}
}