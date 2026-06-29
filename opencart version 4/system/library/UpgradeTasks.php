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
