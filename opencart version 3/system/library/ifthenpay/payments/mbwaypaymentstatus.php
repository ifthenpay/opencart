<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;

class MbWayPaymentStatus
{
	private $webService;

	private static $statusRefusedByUser = '020';
	private static $statusPaid = '000';
	private static $statusPending = '123';


	public function __construct(WebService $webService)
	{
		$this->webService = $webService;
	}

	public function getPaymentStatusWithArgs($mbwayKey, $requestId)
	{
		$response = $this->webService->getRequest(
			'https://api.ifthenpay.com/spg/payment/mbway/status',
			[
				'mbWayKey' => $mbwayKey,
				'requestId' => $requestId
			]
		)->getResponseJson();

		try {
			$status = $response['Status'];

			if ($status === self::$statusPending) {
				return
					['orderStatus' => 'pending'];
			}
			if ($status === self::$statusRefusedByUser) {
				return
					['orderStatus' => 'refused'];
			}
			if ($status === self::$statusPaid) {
				return
					['orderStatus' => 'paid'];
			}

			return
				['orderStatus' => 'error'];
		} catch (\Throwable $th) {
			return
				['orderStatus' => 'error'];
		}
	}
}
