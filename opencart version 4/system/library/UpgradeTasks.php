<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/IfthenpayService.php';

use Ifthenpay\IfthenpayService;
use Opencart\System\Library\DB;
use Opencart\System\Library\Log;
use Opencart\System\Engine\Registry;

class UpgradeTasks
{
	private ?string $currentVersion;
	private ?string $newVersion;
	private DB $db;
	private Registry $registry;
	private IfthenpayService $ifthenpayService;
	private Log $logger;

	public function __construct(DB $db, Registry $registry, ?string $currentVersion = null, ?string $newVersion = null)
	{
		$this->db = $db;
		$this->registry = $registry;
		$this->currentVersion = $currentVersion;
		$this->newVersion = $newVersion;
		$this->ifthenpayService = new IfthenpayService($this->registry);
		$this->logger = new Log('ifthenpay.log');
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
	 * Cofidis was removed as a standalone payment method in 4.2.4. Its controller/model files are gone
	 * from this release, but `UpgradeService::copyDir()` only overwrites/adds files from the new release
	 * onto a site's existing installation — it never deletes files that aren't shipped anymore. So a site
	 * upgrading from an older version would still have the old `admin/controller/payment/cofidis.php` (and
	 * the rest of its files) sitting on disk after the copy step, and since the admin Extensions > Payments
	 * list is built by scanning every extension's admin/controller/payment folder for PHP files, Cofidis
	 * would keep showing up there — now with an "Install" button, since its `oc_extension` row is gone but
	 * the installable file remains.
	 *
	 * On top of deleting those obsolete files, any site that still has it installed must have its extension
	 * registration, event hooks and settings cleared out — otherwise the dangling event hooks (which fire on
	 * every checkout/success page, not just Cofidis orders) and the checkout payment-method loop would try to
	 * load the deleted files and break checkout for every payment method, not just Cofidis.
	 *
	 * The `ifthenpay_cofidis` DB table is intentionally left untouched so historical orders placed with
	 * Cofidis remain viewable in both the storefront and admin.
	 * @return void
	 */
	private function removeCofidisPaymentMethod(): void
	{
		$this->deleteObsoleteCofidisFiles();

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

	/**
	 * Deletes the Cofidis files that used to ship with the extension and are no longer part of the release,
	 * which the plain file-copy upgrade step leaves behind on disk. Safe to run even if a given file was
	 * never installed (e.g. a site installed after 4.2.4) since each path is checked before removal.
	 * @return void
	 */
	private function deleteObsoleteCofidisFiles(): void
	{
		$obsoleteFiles = [
			'system/library/CofidisPayment.php',
			'admin/controller/payment/cofidis.php',
			'admin/model/payment/cofidis.php',
			'admin/view/template/payment/cofidis.twig',
			'admin/view/image/cofidis.png',
			'admin/language/en-gb/payment/cofidis.php',
			'admin/language/pt-pt/payment/cofidis.php',
			'catalog/controller/payment/cofidis.php',
			'catalog/model/payment/cofidis.php',
			'catalog/view/template/payment/cofidisConfirmFormAndBtn.twig',
			'catalog/view/template/payment/cofidisSuccessInfo.twig',
			'catalog/view/image/cofidis.png',
			'catalog/view/image/compact/cofidis.png',
			'catalog/language/en-gb/payment/cofidis.php',
			'catalog/language/pt-pt/payment/cofidis.php',
		];

		foreach ($obsoleteFiles as $relativePath) {
			$path = DIR_EXTENSION . 'ifthenpay/' . $relativePath;

			if (is_file($path) && !@unlink($path)) {
				$this->logger->write("IFTHENPAY - [WARNING] - UpgradeTasks failed to delete obsolete file (check file permissions): $path");
			}
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
