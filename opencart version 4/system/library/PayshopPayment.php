<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;


class PayshopPayment
{
	private $apiService;


	public function __construct()
	{
		$this->apiService = new ApiService();
	}



	/**
	 * generates a static reference for Payshop payment
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $entity
	 * @param string $subEntity
	 * @return string
	 */
	public function generateReference(string $payshopKey, string $orderId, string $orderTotal, string $deadline)
	{
		$deadlineDate = $this->convertDaysToDate($deadline);

		$adaptedResult = ['code' => '999'];

		$result = $this->apiService->requestPayshopReference($payshopKey, $orderId, $orderTotal, $deadlineDate);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'code' => $result['Code'],
				'message' => $result['Message'],
				'reference' => $result['Reference'],
				'transaction_id' => $result['RequestId'],
				'deadline' => $this->formatDateTo_ddmmyyyy($deadlineDate)
			];
		}
		return $adaptedResult;
	}



	/**
	 * converts the deadline in days to a date (date + deadline days)
	 * @param string $deadline
	 * @return string
	 */
	private function convertDaysToDate(string $deadline): string
	{
		if ($deadline === '0' || $deadline === '') {
			return '';
		}
		return (new \DateTime(date("Ymd")))->modify('+' . $deadline . 'day')->format('Ymd');
	}



	/**
	 * formats a date to ddmmyyyy (needed for Payshop, example: 300122020)
	 * @param string $date
	 * @return string
	 */
	private function formatDateTo_ddmmyyyy($date)
	{
		if ($date == '') {
			return '';
		}
		return date("d-m-Y", strtotime($date));
	}
}
