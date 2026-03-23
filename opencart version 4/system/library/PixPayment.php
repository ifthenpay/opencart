<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';

use Ifthenpay\ApiService;



class PixPayment
{
	public $apiService;


	public function __construct()
	{
		$this->apiService = new ApiService();
	}



	/**
	 * generates a static reference for Pix payment
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $entity
	 * @param string $subEntity
	 * @return string
	 */
	public function generateTransaction(string $pixKey, string $orderId, string $orderTotal, string $returnUrl, string $name, string $cpf, string $email)
	{
		$adaptedResult = ['code' => '999'];

		$result = $this->apiService->requestPixUrl($pixKey, $orderId, $orderTotal, $returnUrl, $name, $cpf, $email);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['status'],
				'message' => $result['message'],
				'payment_url' => $result['paymentUrl'],
				'transaction_id' => $result['requestId']
			];
		}

		return $adaptedResult;
	}
}
