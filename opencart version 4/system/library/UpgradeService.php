<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/Utils.php';

use Ifthenpay\Utils;
use Opencart\System\Library\DB;
use Opencart\System\Library\Log;
use Opencart\System\Engine\Registry;

class UpgradeService
{
	private string $extensionDir;
	private DB $db;
	private Registry $registry;
	private Log $log;

	public function __construct(string $extensionDir, DB $db, Registry $registry)
	{
		$this->extensionDir = rtrim($extensionDir, '/\\');
		$this->db = $db;
		$this->registry = $registry;
		$this->log = new Log('ifthenpay.log');
	}



	/**
	 * Download, extract and install the update from the given URL.
	 * Returns ['success' => true] or ['success' => false, 'error' => 'error_key']
	 */
	public function upgrade(string $downloadUrl, ?string $newVersion = null): array
	{
		if (!$this->isValidDownloadUrl($downloadUrl)) {
			$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed, invalid download URL: ' . $downloadUrl);
			return ['success' => false, 'error' => 'invalid_url'];
		}

		$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ifthenpay_upgrade_' . uniqid();

		if (!mkdir($tempDir, 0755, true)) {
			$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed to create temp directory: ' . $tempDir);
			return ['success' => false, 'error' => 'upgrade_failed'];
		}

		try {
			$zipPath = $tempDir . DIRECTORY_SEPARATOR . 'update.zip';

			if (!$this->downloadFile($downloadUrl, $zipPath)) {
				$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed to download update from: ' . $downloadUrl);
				return ['success' => false, 'error' => 'download_failed'];
			}

			$extractDir = $tempDir . DIRECTORY_SEPARATOR . 'extracted';

			if (!mkdir($extractDir, 0755) || !$this->extractZip($zipPath, $extractDir)) {
				$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed to extract zip archive.');
				return ['success' => false, 'error' => 'extract_failed'];
			}

			// Resolve actual root: handles both flat zips and zips with a single top-level folder
			$sourceRoot = $this->resolveZipRoot($extractDir);


			// Read the installed version before any files are overwritten
			$currentVersion = Utils::getModuleVersion(false);

			// Back up current files before overwriting
			$backupDir = $tempDir . DIRECTORY_SEPARATOR . 'backup';
			mkdir($backupDir, 0755);
			try {
				$this->copyExtensionFiles($this->extensionDir, $backupDir);
			} catch (\Throwable $backupError) {
				$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed to create backup: ' . $backupError->getMessage());
				return ['success' => false, 'error' => 'upgrade_failed'];
			}

			// Copy new extension files, restoring backup on failure
			try {
				$this->copyExtensionFiles($sourceRoot, $this->extensionDir);
			} catch (\Throwable $copyError) {
				$this->log->write('IFTHENPAY - [ERROR] - UpgradeService file copy failed, restoring backup: ' . $copyError->getMessage());
				$this->copyExtensionFiles($backupDir, $this->extensionDir);
				throw $copyError;
			}

			// Run upgrade tasks using the version that was installed before this upgrade,
			// so version-gated migrations are correctly applied.
			$newTasksFile = $sourceRoot . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'UpgradeTasks.php';
			if (file_exists($newTasksFile)) {
				require_once $newTasksFile;
				$upgradeTasks = new \Ifthenpay\UpgradeTasks($this->db, $this->registry, $currentVersion, $newVersion);
				$upgradeTasks->run();
			}

			$this->log->write('IFTHENPAY - [INFO] - UpgradeService upgrade to version ' . $newVersion . ' completed successfully.');
			return ['success' => true];
		} catch (\Throwable $th) {
			$this->log->write('IFTHENPAY - [ERROR] - UpgradeService failed to upgrade to version ' . $newVersion . ': ' . json_encode(['message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]));
			return ['success' => false, 'error' => 'upgrade_failed'];
		} finally {
			$this->deleteDirectory($tempDir);
		}
	}



	/**
	 * Allow ifthenpay.com URLs and GitHub releases from the ifthenpay organisation.
	 */
	private function isValidDownloadUrl(string $url): bool
	{
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return false;
		}

		$parsed = parse_url($url);

		if (!isset($parsed['scheme']) || $parsed['scheme'] !== 'https') {
			return false;
		}

		$host = $parsed['host'] ?? '';

		// Allow *.ifthenpay.com
		if (preg_match('/(?:^|\.)ifthenpay\.com$/i', $host)) {
			return true;
		}

		// Allow GitHub releases for the ifthenpay organisation
		if ($host === 'github.com') {
			$path = $parsed['path'] ?? '';
			return (bool) preg_match('#^/ifthenpay/#i', $path);
		}

		return false;
	}



	private function downloadFile(string $url, string $destination): bool
	{
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_SSL_VERIFYPEER => true,
		]);

		$data = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($data === false || $httpCode !== 200) {
			return false;
		}

		return file_put_contents($destination, $data) !== false;
	}



	private function extractZip(string $zipPath, string $extractTo): bool
	{
		$zip = new \ZipArchive();

		if ($zip->open($zipPath) !== true) {
			return false;
		}

		// Guard against path traversal inside the archive
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$name = $zip->getNameIndex($i);
			if ($name === false || strpos($name, '..') !== false) {
				$zip->close();
				return false;
			}
		}

		$result = $zip->extractTo($extractTo);
		$zip->close();

		return $result;
	}



	/**
	 * If the zip contains a single top-level directory (e.g. "ifthenpay-4.2.0/"),
	 * return that as the real root so we can find system/, admin/, catalog/ etc.
	 */
	private function resolveZipRoot(string $extractDir): string
	{
		$items = array_diff(scandir($extractDir), ['.', '..']);

		if (count($items) === 1) {
			$only = reset($items);
			$candidate = $extractDir . DIRECTORY_SEPARATOR . $only;

			if (is_dir($candidate)) {
				return $candidate;
			}
		}

		return $extractDir;
	}



	private function copyExtensionFiles(string $sourceRoot, string $destRoot): void
	{
		$items = ['system', 'catalog', 'admin', 'install.json', 'LICENSE'];

		foreach ($items as $item) {
			$src = $sourceRoot . DIRECTORY_SEPARATOR . $item;
			$dst = $destRoot . DIRECTORY_SEPARATOR . $item;

			if (is_dir($src)) {
				$this->copyDir($src, $dst);
			} elseif (is_file($src)) {
				copy($src, $dst);
			}
		}
	}



	private function copyDir(string $src, string $dst): void
	{
		if (!is_dir($dst)) {
			mkdir($dst, 0755, true);
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($iterator as $item) {
			$destPath = $dst . DIRECTORY_SEPARATOR . $iterator->getSubPathname();

			if ($item->isDir()) {
				if (!is_dir($destPath)) {
					mkdir($destPath, 0755, true);
				}
			} else {
				copy($item->getRealPath(), $destPath);
			}
		}
	}



	private function deleteDirectory(string $dir): void
	{
		if (!is_dir($dir)) {
			return;
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				@rmdir($file->getRealPath());
			} else {
				@unlink($file->getRealPath());
			}
		}

		@rmdir($dir);
	}
}
