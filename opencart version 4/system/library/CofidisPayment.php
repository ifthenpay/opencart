<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;



class CofidisPayment
{
	private const ERROR = '999';
	public $apiService;



	public function __construct()
	{
		$this->apiService = new ApiService();
	}


	/**
	 * generates a cofidis payment url to which the customer will be redirected to in order to pay
	 * the payment url is returned in an array with the following keys:
	 * code: the status code of the request
	 * message: the message of the request
	 * transaction_id: the transaction id of the request
	 * payment_url: the payment url
	 * @param string $cofidisKey
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $language
	 * @param string $successUrl
	 * @param string $cancelUrl
	 * @param string $errorUrl
	 * @return array
	 */
	public function generateUrl(string $cofidisKey, string $returnUrl, array $customerData)
	{
		$result = $this->apiService->requestCofidisUrl($cofidisKey, $returnUrl, $customerData);

		$adaptedResult = [];
		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['status'],
				'message' => $result['message'],
				'transaction_id' => $result['requestId'],
				'payment_url' => $result['paymentUrl']
			];
		}

		return $adaptedResult;
	}



}
