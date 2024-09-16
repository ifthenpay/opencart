<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;


class CallbackDataMultibanco implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		if (!isset($request[CallbackVars::REFERENCE]) || empty($request[CallbackVars::REFERENCE])) {
			return [];
		}

		$ifthenpayController->load->model('extension/payment/multibanco');

		$data = $ifthenpayController->model_extension_payment_multibanco->getMultibancoByReferencia(
			$request[CallbackVars::REFERENCE]
		)->row;

		// fallback for when request originates from ifthenpaygateway callback
		if (empty($data)) {

			if (!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) {
				return [];
			}

			$data = $ifthenpayController->model_extension_payment_multibanco->getPaymentByOrderId(
				$request[CallbackVars::ORDER_ID]
			)->row;
		}

		return $data;
	}
}
