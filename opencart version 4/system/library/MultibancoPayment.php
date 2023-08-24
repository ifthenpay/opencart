<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;




class MultibancoPayment
{
	private $apiService;

	public const DYNAMIC_ENTITY_NAME = 'MB';



	public function __construct()
	{
		$this->apiService = new ApiService();
	}



	/**
	 * generates a static reference for multibanco payment
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $entity
	 * @param string $subEntity
	 * @return string
	 */
	public function generateStaticReference(string $orderId, string $orderTotal, string $entity, string $subEntity): string
	{
		$orderId = "0000" . $orderId;

		if (strlen($subEntity) === 2) {
			//Apenas sao considerados os 5 caracteres mais a direita do order_id
			$seed = substr($orderId, (strlen($orderId) - 5), strlen($orderId));
			$chk_str = sprintf('%05u%02u%05u%08u', $entity, $subEntity, $seed, round($orderTotal * 100));
		} else {
			//Apenas sao considerados os 4 caracteres mais a direita do order_id
			$seed = substr($orderId, (strlen($orderId) - 4), strlen($orderId));
			$chk_str = sprintf('%05u%03u%04u%08u', $entity, $subEntity, $seed, round($orderTotal * 100));
		}
		$chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);
		$chk_val = 0;
		for ($i = 0; $i < 20; $i++) {
			$chk_int = substr($chk_str, 19 - $i, 1);
			$chk_val += ($chk_int % 10) * $chk_array[$i];
		}
		$chk_val %= 97;
		$chk_digits = sprintf('%02u', 98 - $chk_val);
		//referencia
		return $subEntity . $seed . $chk_digits;
	}



	/**
	 * generates a dynamic reference for multibanco payment
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $entity
	 * @param string $subEntity
	 * @return string
	 */
	public function generateReference(string $entity, string $subEntity, string $orderId, string $orderTotal, string $deadlineDays)
	{

		$adaptedResult = ['code' => '999'];

		$result = $this->apiService->requestMultibancoReference($entity, $subEntity, $orderId, $orderTotal, $deadlineDays);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['Status'],
				'message' => $result['Message'],
				'entity' => $result['Entity'],
				'reference' => $result['Reference'],
				'transaction_id' => $result['RequestId'],
				'deadline' => $result['ExpiryDate']
			];
		}
		return $adaptedResult;
	}
}