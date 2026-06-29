<?php

namespace Opencart\Catalog\Controller\Extension\ifthenpay\Cron;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';

use Ifthenpay\ApiService;
use Ifthenpay\Utils;

class Ifthenpay extends \Opencart\System\Engine\Controller
{
	public function index(): void
	{
		if (isset($this->request->get['route'])) {
			return;
		}

		$apiResult = (new ApiService())->requestCheckModuleUpgrade();

		if (empty($apiResult['version'])) {
			return;
		}

		$latestVersion = $apiResult['version'];
		$currentVersion = Utils::getModuleVersion(false);

		if (!version_compare($latestVersion, $currentVersion, '>')) {
			return;
		}

		// avoid creating a duplicate notification for the same version
		$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_update_notified_version'");

		if ($query->row && $query->row['value'] === $latestVersion) {
			return;
		}

		$this->load->language('extension/ifthenpay/cron/ifthenpay');

		$title = $this->db->escape(sprintf($this->language->get('notification_update_title'), $latestVersion));
		$text  = $this->db->escape(sprintf($this->language->get('notification_update_text'), $latestVersion, $currentVersion));

		$this->db->query("INSERT INTO `" . DB_PREFIX . "notification` SET `title` = '" . $title . "', `text` = '" . $text . "', `status` = '0', `date_added` = NOW()");

		// remember notified version to avoid duplicates on subsequent daily runs
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_update_notified_version'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '0', `code` = 'payment_ifthenpay', `key` = 'payment_ifthenpay_update_notified_version', `value` = '" . $this->db->escape($latestVersion) . "', `serialized` = '0'");
	}
}
