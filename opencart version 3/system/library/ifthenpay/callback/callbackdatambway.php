<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;

class CallbackDataMbway implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		$ifthenpayController->load->model('extension/payment/mbway');

		if (!isset($request[CallbackVars::TRANSACTION_ID]) || empty($request[CallbackVars::TRANSACTION_ID])) {
			return [];
		}

		$data = $ifthenpayController->model_extension_payment_mbway->getMbwayByIdTransacao(
			$request[CallbackVars::TRANSACTION_ID]
		)->row;

		// fallback for when request originates from ifthenpaygateway callback
		if (empty($data)) {

			if (!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) {
				return [];
			}

			$data = $ifthenpayController->model_extension_payment_mbway->getPaymentByOrderId(
				$request[CallbackVars::ORDER_ID]
			)->row;
		}

		return $data;
	}
}
