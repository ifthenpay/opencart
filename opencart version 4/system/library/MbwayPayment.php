<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;



class MbwayPayment
{
	public $apiService;
	private const COUNTRY_CODES_PATH = __DIR__ . '/CountryCodes.json';

	private const ERROR = '999';
	private const SUCCESS = '000'; // this success refers to the request, not the payment status. It is different from the PAID constant
	private const REFUSED = '020';
	private const PAID = '000';
	private const PENDING = '123';



	public function __construct()
	{
		$this->apiService = new ApiService();
	}



	/**
	 * generates a static reference for Mbway payment
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $entity
	 * @param string $subEntity
	 * @return string
	 */
	public function generateTransaction(string $mbwayKey, string $orderId, string $orderTotal, string $phoneNumber)
	{
		$adaptedResult = ['code' => self::ERROR];

		$result = $this->apiService->requestMbwayTransaction($mbwayKey, $orderId, $orderTotal, $phoneNumber);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['Estado'],
				'message' => $result['MsgDescricao'],
				'transaction_id' => $result['IdPedido']
			];
		}

		return $adaptedResult;
	}



	/**
	 * generate an order payment refund
	 * @param string $backofficeKey
	 * @param string $transactionId
	 * @param string $amount
	 */
	public function generateRefund(string $backofficeKey, string $transactionId, string $amount)
	{
		$adaptedResult = ['code' => self::ERROR];
		$result = $this->apiService->requestRefund($backofficeKey, $transactionId, $amount);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['Code'],
				'message' => $result['Message'],
			];
		}

		return $adaptedResult;
	}



	/**
	 * generate an array of country code options to use in a select box for mbway smartphone number
	 * @param string $lang
	 * @return array
	 */
	public static function generateCountryCodeOptions(string $lang): array
	{
		// Read JSON file contents
		$jsonData = file_get_contents(self::COUNTRY_CODES_PATH);

		// Parse JSON data into an associative array
		$countryCodes = json_decode($jsonData, true);

		// get correct language key
		$lang = strtoupper($lang);
		$lang = (isset($countryCodes['mobile_prefixes']) && isset($countryCodes['mobile_prefixes'][0]) && isset($countryCodes['mobile_prefixes'][0][$lang])) ? $lang : 'EN';


		$countryCodeOptions = [];
		foreach ($countryCodes['mobile_prefixes'] as $country) {

			if ($country['Ativo'] != 1) {
				continue; // skip this one
			}

			$countryCodeOptions[] = [
				'value' => $country['Indicativo'],
				'name' => $country[$lang] . ' (+' . $country['Indicativo'] . ')'
			];
		}

		return $countryCodeOptions;
	}



	/**
	 * check mbway payment status by calling the api, and return an adapted result
	 * @param string $transactionId
	 * @param string $mbwayKey
	 * @return array
	 */
	public function checkMbwayPaymentStatus(string $transactionId, string $mbwayKey): array
	{
		$result = $this->apiService->requestCheckMbwayPaymentStatus($transactionId, $mbwayKey);

		$adaptedResult = ['code' => self::ERROR];

		if ($result) {
			$result = json_decode($result, true);

			if (isset($result['Estado']) && $result['Estado'] === self::SUCCESS && isset($result['EstadoPedidos']) && isset($result['EstadoPedidos'][0])) {
				$adaptedResult = [
					'code' => $result['EstadoPedidos'][0]['Estado'],
					'transaction_id' => $result['EstadoPedidos'][0]['IdPedido'],
					'message' => $result['EstadoPedidos'][0]['MsgDescricao'],
				];
			} else {
				$adaptedResult = [
					'code' => self::ERROR,
					'message' => 'Error: Could not get payment status.'
				];
			}
		} else {
			$adaptedResult = [
				'code' => self::ERROR,
				'message' => 'Error: Could not get payment status.'
			];
		}

		if ($adaptedResult) {

			switch ($adaptedResult['code']) {
				case self::PAID:
					$adaptedResult['status'] = 'paid';
					break;
				case self::PENDING:
					$adaptedResult['status'] = 'pending';
					break;
				case self::REFUSED:
					$adaptedResult['status'] = 'refused';
					break;
				default:
					$adaptedResult['status'] = 'error';
					break;
			}
		}

		return $adaptedResult;
	}
}
