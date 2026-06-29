<?php

namespace Ifthenpay;

/**
 * @property \Opencart\System\Library\DB $db
 * @property \Opencart\System\Engine\Loader $load
 * @property \Opencart\Admin\Model\Setting\Cron $model_setting_cron
 */
trait CronTrait
{
	private function installCron(): void
	{
		$this->load->model('setting/cron');

		if (!$this->model_setting_cron->getCronByCode('ifthenpay_check_upgrade')) {
			$this->model_setting_cron->addCron('ifthenpay_check_upgrade', 'Check for ifthenpay module updates', 'day', 'extension/ifthenpay/cron/ifthenpay', true);
		}

		$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");
		$count = (int)($query->row['value'] ?? 0) + 1;
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '0', `code` = 'payment_ifthenpay', `key` = 'payment_ifthenpay_installed_count', `value` = '" . $count . "', `serialized` = '0'");
	}

	private function uninstallCron(): void
	{
		$this->load->model('setting/cron');

		$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");
		$count = max(0, (int)($query->row['value'] ?? 0) - 1);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");

		if ($count > 0) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '0', `code` = 'payment_ifthenpay', `key` = 'payment_ifthenpay_installed_count', `value` = '" . $count . "', `serialized` = '0'");
		} else {
			$this->model_setting_cron->deleteCronByCode('ifthenpay_check_upgrade');
		}
	}
}
