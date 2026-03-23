<?php

namespace Ifthenpay;

require_once DIR_EXTENSION . 'ifthenpay/system/library/ApiService.php';
use Ifthenpay\ApiService;



class IfthenpaygatewayPayment
{
	public $apiService;



	public function __construct()
	{
		$this->apiService = new ApiService();
	}

	public function generateUrl(string $ifthenpaygatewayKey, string $orderId, string $orderTotal, string $description, string $language, string $expireDate, string $accounts, string $selectedMethod, string $btnCloseUrl, string $btnCloseLabel, string $successUrl, string $cancelUrl, string $errorUrl)
	{


		$result = $this->apiService->requestIfthenpayGatewayUrl($ifthenpaygatewayKey, $orderId, $orderTotal, $description, $language, $expireDate, $accounts, $selectedMethod, $btnCloseUrl, $btnCloseLabel, $successUrl, $cancelUrl, $errorUrl);

		if ($result) {
			$result = json_decode($result, true);

			$adaptedResult = [
				'pin_code' => $result['PinCode'] ?? '',
				'payment_url' => $result['RedirectUrl'] ?? '',
			];
		}

		return $adaptedResult;
	}

}
