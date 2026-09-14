<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/IfthenpayService.php';

use Ifthenpay\IfthenpayService;
use Opencart\System\Library\DB;
use Opencart\System\Engine\Registry;

class UpgradeTasks
{
	private ?string $currentVersion;
	private ?string $newVersion;
	private DB $db;
	private Registry $registry;
	private IfthenpayService $ifthenpayService;

	public function __construct(DB $db, Registry $registry, ?string $currentVersion = null, ?string $newVersion = null)
	{
		$this->db = $db;
		$this->registry = $registry;
		$this->currentVersion = $currentVersion;
		$this->newVersion = $newVersion;
		$this->ifthenpayService = new IfthenpayService($this->registry);
	}


	public function run(): void
	{
		$this->runMigrations();
		$this->reactivateCallbacks();
		$this->refreshAccounts();
	}

	/**
	 * Run database migrations based on version changes.
	 * TODO: this is currently a placeholder for future migrations. Add actual migration logic as needed.
	 * only run migrations for changes to tables, all tables are created in the install method of each payment method model
	 * @return void
	 * @throws \Exception
	 */
	public function runMigrations(): void
	{
		if ($this->currentVersion === null || $this->newVersion === null) {
			return;
		}

		// 4.2.0 example for future migrations
		if (version_compare($this->currentVersion, '4.2.0', '<') && version_compare($this->newVersion, '4.2.0', '>=')) {

			// alter DB
			// $this->db->query("ALTER TABLE `" . DB_PREFIX . "ifthenpay_multibanco` ADD COLUMN IF NOT EXISTS `new_column` VARCHAR(255) NOT NULL DEFAULT '' AFTER `request_id`;");

			// reactivate callbacks/webhooks
		}

		// 4.2.4 removes the Cofidis payment method
		if (version_compare($this->currentVersion, '4.2.4', '<') && version_compare($this->newVersion, '4.2.4', '>=')) {
			$this->removeCofidisPaymentMethod();
		}
	}

	/**
	 * Cofidis was removed as a standalone payment method in 4.2.4. Its controller/model files are gone,
	 * so any site that still has it installed must have its extension registration, event hooks and
	 * settings cleared out — otherwise the dangling event hooks (which fire on every checkout/success
	 * page, not just Cofidis orders) and the checkout payment-method loop would try to load the deleted
	 * files and break checkout for every payment method, not just Cofidis.
	 *
	 * The `ifthenpay_cofidis` DB table is intentionally left untouched so historical orders placed with
	 * Cofidis remain viewable in both the storefront and admin.
	 * @return void
	 */
	private function removeCofidisPaymentMethod(): void
	{
		// remove the event hooks first, regardless of install state, since they run on every checkout/success page
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` IN ('payment_ifthenpay_cofidis_catalog_success_payment_info', 'payment_ifthenpay_cofidis_icon_injection')");

		$query = $this->db->query("SELECT `extension_id` FROM `" . DB_PREFIX . "extension` WHERE `type` = 'payment' AND `extension` = 'ifthenpay' AND `code` = 'cofidis'");

		if (!$query->num_rows) {
			return;
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `type` = 'payment' AND `extension` = 'ifthenpay' AND `code` = 'cofidis'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'payment_cofidis'");

		// keep the shared "how many ifthenpay payment methods are installed" counter (and its upgrade-check cron) in sync
		$countQuery = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");
		$count = max(0, (int) ($countQuery->row['value'] ?? 0) - 1);

		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'payment_ifthenpay' AND `key` = 'payment_ifthenpay_installed_count'");

		if ($count > 0) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '0', `code` = 'payment_ifthenpay', `key` = 'payment_ifthenpay_installed_count', `value` = '" . $count . "', `serialized` = '0'");
		} else {
			$this->registry->load->model('setting/cron');
			$this->registry->get('model_setting_cron')->deleteCronByCode('ifthenpay_check_upgrade');
		}
	}

	public function reactivateCallbacks(): void
	{
		$this->ifthenpayService->refreshCallbacks();
	}

	public function refreshAccounts(): void
	{
		$this->ifthenpayService->refreshAccounts();
	}
}
