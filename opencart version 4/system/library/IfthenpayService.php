<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/Gateway.php';
require_once DIR_EXTENSION . 'ifthenpay/system/library/PaymentMethodsEnum.php';

use Ifthenpay\Gateway;
use Ifthenpay\PaymentMethodsEnum;
use Opencart\System\Library\Log;
use Opencart\System\Engine\Registry;


class IfthenpayService
{

	private Gateway $gateway;
	public Registry $registry; // opencart registry, used to get models
	private Log $logger;

	public function __construct(Registry $registry)
	{
		$this->gateway = new Gateway();
		$this->registry = $registry;
		$this->logger = new Log('ifthenpay.log');
	}


	public function RefreshCallbacks(): void
	{

		foreach (PaymentMethodsEnum::cases() as $methodEnum) {
			$method = $methodEnum->value;

			if ($method === 'ccard') {
				continue; // Skip credit card as it doesn't require callback registration
			}

			$this->registry->load->model('setting/setting');
			$modelSetting = $this->registry->get('model_setting_setting');

			$storedSettings = $modelSetting->getSetting('payment_' . $method,);


			switch ($method) {
				case PaymentMethodsEnum::MULTIBANCO->value:
					$callbackStr  = Gateway::MULTIBANCO_CALLBACK_STRING;
					$entity = $storedSettings['payment_' . $method . '_entity'] ?? null;
					$subEntity = $storedSettings['payment_' . $method . '_sub_entity'] ?? null;
					break;
				case PaymentMethodsEnum::MBWAY->value:
					$callbackStr  = Gateway::MBWAY_CALLBACK_STRING;
					$entity = PaymentMethodsEnum::MBWAY->name;
					$subEntity = $storedSettings['payment_' . $method . '_key'] ?? null;
					break;
				case PaymentMethodsEnum::PAYSHOP->value:
					$callbackStr  = Gateway::PAYSHOP_CALLBACK_STRING;
					$entity = PaymentMethodsEnum::PAYSHOP->name;
					$subEntity = $storedSettings['payment_' . $method . '_key'] ?? null;
					break;
				case PaymentMethodsEnum::COFIDIS->value:
					$callbackStr  = Gateway::COFIDIS_CALLBACK_STRING;
					$entity = PaymentMethodsEnum::COFIDIS->name;
					$subEntity = $storedSettings['payment_' . $method . '_key'] ?? null;
					break;
				case PaymentMethodsEnum::IFTHENPAYGATEWAY->value:
					$callbackStr  = Gateway::IFTHENPAYGATEWAY_CALLBACK_STRING;
					$entity = PaymentMethodsEnum::IFTHENPAYGATEWAY->name;
					$subEntity = $storedSettings['payment_' . $method . '_key'] ?? null;
					break;
				case PaymentMethodsEnum::PIX->value:
					$callbackStr  = Gateway::PIX_CALLBACK_STRING;
					$entity = PaymentMethodsEnum::PIX->name;
					$subEntity = $storedSettings['payment_' . $method . '_key'] ?? null;
					break;
				default:
					throw new \Exception("Unsupported payment method: $method");
			}



			if (!$entity || !$subEntity) {

				$this->logger->write("Entity or Sub-entity not found for method $method. Skipping callback registration.");
				continue;
			}


			// ifthenpaygateway has a different callback registration process
			if ($method === PaymentMethodsEnum::IFTHENPAYGATEWAY->value) {

				// get selected payment method from settings
				$paymentMethods = $storedSettings['payment_' . PaymentMethodsEnum::IFTHENPAYGATEWAY->value . '_methods'] ?? [];
				$activePaymentMethods = array_filter($paymentMethods, fn($item) => $item['is_active'] === '1');

				// use same antiPhishingKey for all callbacks of the same ifthenpaygateway
				$antiPhishingKey = md5((string) rand());

				// foreach activate the callback
				foreach ($activePaymentMethods as $gatewayMethod) {

					$methodEntitySubentity = explode('|', $gatewayMethod['account']);
					if (count($methodEntitySubentity) < 2) {
						$this->logger->write("Skipping gateway method — account value missing '|': " . $gatewayMethod['account']);
						continue;
					}
					$methodEntity = trim($methodEntitySubentity[0]);
					$methodSubEntity = trim($methodEntitySubentity[1]);

					try {
						[$urlCallback, $antiPhishingKey] = $this->registerCallback($method, $methodEntity, $methodSubEntity, $callbackStr, $antiPhishingKey);

						$modelSetting->editValue('payment_' . PaymentMethodsEnum::IFTHENPAYGATEWAY->value, 'payment_' . PaymentMethodsEnum::IFTHENPAYGATEWAY->value . '_url_callback', $urlCallback);
						$modelSetting->editValue('payment_' . PaymentMethodsEnum::IFTHENPAYGATEWAY->value, 'payment_' . PaymentMethodsEnum::IFTHENPAYGATEWAY->value . '_anti_phishing_key', $antiPhishingKey);
					} catch (\Throwable $th) {
						$this->logger->write("Error activating callback for method " . PaymentMethodsEnum::IFTHENPAYGATEWAY->value . " and sub-entity $methodSubEntity: " . $th->getMessage());
					}
				}
			} else {
				try {
					[$urlCallback, $antiPhishingKey] = $this->registerCallback($method, $entity, $subEntity, $callbackStr);

					$modelSetting->editValue('payment_' . $method, 'payment_' . $method . '_url_callback', $urlCallback);
					$modelSetting->editValue('payment_' . $method, 'payment_' . $method . '_anti_phishing_key', $antiPhishingKey);
				} catch (\Throwable $th) {
					$this->logger->write("Error activating callback for method $method: " . $th->getMessage());
				}
			}
		}
	}

	public function registerCallback(string $method, string $entity, string $subEntity, string $callbackStr, string $antiPhishingKey = ''): array
	{
		try {
			$this->registry->load->model('setting/setting');
			$modelSetting = $this->registry->get('model_setting_setting');

			$storedSettings = $modelSetting->getSetting('payment_' . $method,);
			$backofficeKey = $storedSettings['payment_' . $method . '_backoffice_key'] ?? null;
			if (!$backofficeKey) {
				throw new \Exception("Backoffice key not found for method $method");
			}

			$antiPhishingKey = $antiPhishingKey === '' ? md5((string) rand()) : $antiPhishingKey;
			// get callback url for catalog
			$moduleVersion = Utils::getModuleVersion(false);
			$callbackStr = str_replace('{mv}', $moduleVersion, $callbackStr);
			$opencartVersion = defined('VERSION') ? VERSION : 'na';
			$callbackStr = str_replace('{ec}', 'op_' . $opencartVersion, $callbackStr);

			$urlCallback = $this->registry->get('url')->link('extension/ifthenpay/payment/' . $method . '|callback', '', true) . $callbackStr;
			$urlCallback = str_replace(HTTP_SERVER, HTTP_CATALOG, $urlCallback);
			$urlCallback = str_replace('{ec}', 'op_' . (defined('VERSION') ? VERSION : 'unknown'), $urlCallback);
			$urlCallback = str_replace('{mv}', Utils::getModuleVersion(), $urlCallback);

			$gateway = new Gateway();
			$result = $gateway->requestActivateCallback($backofficeKey, $entity, $subEntity, $antiPhishingKey, $urlCallback);

			if (strpos($result, 'OK') === false) {
				throw new \Exception("error activating callback");
			}

			return [$urlCallback, $antiPhishingKey];
		} catch (\Throwable $th) {
			$this->logger->write("Error activating callback for entity $entity and sub-entity $subEntity: " . $th->getMessage());
			throw $th;
		}
	}

	public function refreshAccounts(): void
	{
		foreach (PaymentMethodsEnum::cases() as $method) {

			if ($method->value === PaymentMethodsEnum::IFTHENPAYGATEWAY->value) {
				continue; // Skip ifthenpaygateway as it loads accounts from endpoint every time user accesses the payment method settings
			}

			$this->refreshAccount($method->value);
		}
	}

	public function refreshAccount(string $method): void
	{
		try {
			$this->registry->load->model('setting/setting');
			$modelSetting = $this->registry->get('model_setting_setting');

			$storedSettings = $modelSetting->getSetting('payment_' . $method,);
			$backofficeKey = $storedSettings['payment_' . $method . '_backoffice_key'] ?? null;
			if (!$backofficeKey) {
				return;
			}

			$accounts = $this->gateway->getAccountsByBackofficeKeyAndMethod($backofficeKey, strtoupper($method));
			$modelSetting->editValue('payment_' . $method, 'payment_' . $method . '_accounts', json_encode($accounts));
		} catch (\Throwable $th) {
			$this->logger->write("Error refreshing accounts for method $method: " . $th->getMessage());
			throw $th;
		}
	}
}
