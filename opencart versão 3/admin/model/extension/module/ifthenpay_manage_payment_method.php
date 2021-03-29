<?php
class ModelExtensionModuleIfthenpayManagePaymentMethod extends Model {
		
	public function deleteSettingByKey(string $key, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");
	}
}
