<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/UpgradeService.php';

use Ifthenpay\UpgradeService;

/**
 * @property \Opencart\System\Library\DB $db
 * @property \Opencart\System\Library\Request $request
 * @property \Opencart\System\Library\Response $response
 * @property \Opencart\System\Engine\Loader $load
 * @property \Opencart\System\Library\Language $language
 * @property \Opencart\System\Library\User $user
 * @property array $json
 */
trait UpgradeTrait
{
	public function ajaxUpgrade(): void
	{
		$method = strtolower(substr(static::class, strrpos(static::class, '\\') + 1));

		$this->load->language('extension/ifthenpay/payment/' . $method);

		if (!$this->user->hasPermission('modify', 'extension/ifthenpay/payment/' . $method)) {
			$this->json['error'] = $this->language->get('error_permission');
		} else {
			$downloadUrl = $this->request->post['download_url'] ?? '';
			$newVersion = $this->request->post['new_version'] ?? null;
			$upgradeService = new UpgradeService(DIR_EXTENSION . 'ifthenpay', $this->db, $this->registry);
			$result = $upgradeService->upgrade($downloadUrl, $newVersion);

			if ($result['success']) {
				$this->json['success'] = $this->language->get('success_upgrade');
			} else {
				$errorKey = match($result['error'] ?? '') {
					'invalid_url'     => 'error_upgrade_url_invalid',
					'download_failed' => 'error_upgrade_download',
					'extract_failed'  => 'error_upgrade_extract',
					default           => 'error_upgrade',
				};
				$this->json['error'] = $this->language->get($errorKey);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->json));
	}
}
