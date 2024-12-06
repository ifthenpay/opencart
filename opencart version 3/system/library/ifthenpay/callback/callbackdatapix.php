<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;

class CallbackDataPix implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		$ifthenpayController->load->model('extension/payment/pix');

		switch ($request['type']) {
			case 'online':

				if ((!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) && (!isset($request['hash']) || empty($request['hash']))) {
					return [];
				}

				return $ifthenpayController->model_extension_payment_pix->getPixByOrderIdAndHash(
					$request[CallbackVars::ORDER_ID],
					$request['hash']
				)->row;

			case 'offline':

				if (!isset($request[CallbackVars::TRANSACTION_ID]) || empty($request[CallbackVars::TRANSACTION_ID])) {
					return [];
				}
				$data = $ifthenpayController->model_extension_payment_pix->getPixByRequestId($request[CallbackVars::TRANSACTION_ID])->row;

				// fallback for when request originates from ifthenpaygateway callback
				if (empty($data)) {

					if (!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) {
						return [];
					}

					$data = $ifthenpayController->model_extension_payment_pix->getPaymentByOrderId(
						$request[CallbackVars::ORDER_ID]
					)->row;
				}

				return $data;

			default:
				throw new \Exception('Invalid request type when obtaining callback data');
		}
	}
}
