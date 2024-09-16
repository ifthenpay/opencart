<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;


class CallbackDataPayshop implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		if (!isset($request[CallbackVars::TRANSACTION_ID]) || empty($request[CallbackVars::TRANSACTION_ID])) {
			return [];
		}

		$ifthenpayController->load->model('extension/payment/payshop');

		$data = $ifthenpayController->model_extension_payment_payshop->getPayshopByIdTransacao(
			$request[CallbackVars::TRANSACTION_ID]
		)->row;

		// fallback for when request originates from ifthenpaygateway callback
		if (empty($data)) {

			if (!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) {
				return [];
			}

			$data = $ifthenpayController->model_extension_payment_payshop->getPaymentByOrderId(
				$request[CallbackVars::ORDER_ID]
			)->row;
		}

		return $data;
	}
}
