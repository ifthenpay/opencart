<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;



class CcardPayment
{
	private const ERROR = '999';
	public $apiService;



	public function __construct()
	{
		$this->apiService = new ApiService();
	}


	/**
	 * generates a ccard payment url to which the customer will be redirected to in order to pay
	 * the payment url is returned in an array with the following keys:
	 * code: the status code of the request
	 * message: the message of the request
	 * transaction_id: the transaction id of the request
	 * payment_url: the payment url
	 * @param string $ccardKey
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $language
	 * @param string $successUrl
	 * @param string $cancelUrl
	 * @param string $errorUrl
	 * @return array
	 */
	public function generateUrl(string $ccardKey, string $orderId, string $orderTotal, string $language, string $successUrl, string $cancelUrl, string $errorUrl)
	{

		$adaptedResult = [];

		$result = $this->apiService->requestCcardUrl($ccardKey, $orderId, $orderTotal, $language, $successUrl, $cancelUrl, $errorUrl);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['Status'],
				'message' => $result['Message'],
				'transaction_id' => $result['RequestId'],
				'payment_url' => $result['PaymentUrl']
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
}
