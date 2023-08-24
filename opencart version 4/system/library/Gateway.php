<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;

class Gateway
{
	private $paymentMethods = [
		'MULTIBANCO',
		'MB',
		'MBWAY',
		'PAYSHOP',
		'CCARD'
	];

	private const MULTIBANCO_DYNAMIC = 'MB';
	private $apiService;


	public const MULTIBANCO_CALLBACK_STRING = '&ec=oc&phish_key=[CHAVE_ANTI_PHISHING]&reference=[REFERENCIA]&amount=[VALOR]';
	public const PAYSHOP_CALLBACK_STRING = '&ec=oc&phish_key=[CHAVE_ANTI_PHISHING]&reference=[REFERENCIA]&amount=[VALOR]';
	public const MBWAY_CALLBACK_STRING = '&ec=oc&phish_key=[CHAVE_ANTI_PHISHING]&transaction_id=[ID_TRANSACAO]&amount=[VALOR]';


	public function __construct()
	{
		$this->apiService = new ApiService();
	}



	/**
	 * request callback activation using apiservice
	 * @param string $key
	 * @param string $entity
	 * @param string $subEntity
	 * @param string $antiPhishingKey
	 * @param string $urlCallback
	 * @return string|null
	 */
	public function requestActivateCallback($key, $entity, $subEntity, $antiPhishingKey, $urlCallback)
	{
		$response = $this->apiService->requestCallbackActivation($key, $entity, $subEntity, $antiPhishingKey, $urlCallback);

		return $response;
	}



	/**
	 * gets accounts from apiservice but only returns the ones that match the payment method
	 * @param string $backofficeKey
	 * @param string $method
	 * @return array
	 */
	public function getAccountsByBackofficeKeyAndMethod($backofficeKey, $method)
	{
		// verify payment method
		if (!in_array($method, $this->paymentMethods)) {
			return [];
		}

		$accounts = $this->apiService->getAccounts($backofficeKey);
		$accounts = json_decode($accounts, true);

		if (isset($accounts[0]) && isset($accounts[0]['Entidade']) && $accounts[0]['Entidade'] === '') {
			return [];
		}

		$paymentMethodAccounts = [];

		foreach ($accounts as $account) {

			if ($method === 'MULTIBANCO') {

				if (is_numeric($account['Entidade']) || $account['Entidade'] === 'MB') {
					$paymentMethodAccounts[] = $account;
				}
			} else if ($account['Entidade'] === $method) {
				$paymentMethodAccounts[] = $account;
			}
		}

		return $paymentMethodAccounts;
	}



	public static function accountsToKeyOptions($accounts)
	{
		if (!$accounts) {
			return [];
		}

		$options = [];

		if (isset($accounts[0]) && isset($accounts[0]['SubEntidade'])) {


			foreach ($accounts[0]['SubEntidade'] as $key) {
				$options[] = [
					'value' => $key,
					'name' => $key
				];
			}
		}

		return $options;
	}



	/**
	 * generates account options for select input
	 * @param array $accounts
	 * @return array
	 */
	public static function accountsToEntityOptions($accounts, $multibancoDynamicLabel)
	{
		if (!$accounts) {
			return [];
		}

		$options = [];

		foreach ($accounts as $account) {

			if ($account['Entidade'] == self::MULTIBANCO_DYNAMIC) {
				$options[] = [
					'value' => $account['Entidade'],
					'name' => $multibancoDynamicLabel
				];
			} else {
				$options[] = [
					'value' => $account['Entidade'],
					'name' => $account['Entidade']
				];
			}
		}
		return $options;
	}



	/**
	 * generates sub entity options for select input
	 * @param array $accounts
	 * @param string $entity
	 * @return array
	 */
	public static function accountsToSubEntityOptions($accounts, $entity)
	{
		if (!$accounts) {
			return [];
		}

		$options = [];

		if ($entity === '') {
			return $options;
		}
		foreach ($accounts as $account) {
			if ($account['Entidade'] === $entity) {

				foreach ($account['SubEntidade'] as $key => $value) {

					$options[] = [
						'value' => $value,
						'name' => $value
					];
				}
			}
		}
		return $options;
	}



	/**
	 * Check if the accounts array has a dynamic multibanco account
	 * @param array $accounts
	 * @return bool
	 */
	public static function hasDynamicMultibanco($accounts)
	{
		if (!$accounts) {
			return false;
		}

		foreach ($accounts as $account) {

			if ($account['Entidade'] === 'MB') {
				return true;
			}
		}
		return false;
	}



	/**
	 * Check if the accounts array has a static multibanco account
	 * @param array $accounts
	 * @return bool
	 */
	public static function hasStaticMultibanco($accounts)
	{
		if (!$accounts) {
			return false;
		}

		foreach ($accounts as $account) {

			if (is_numeric($account['Entidade'])) {
				return true;
			}
		}
		return false;
	}
}
