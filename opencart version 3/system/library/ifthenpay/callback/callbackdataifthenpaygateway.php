<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;

class CallbackDataIfthenpaygateway implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		if (!isset($request[CallbackVars::ORDER_ID]) || empty($request[CallbackVars::ORDER_ID])) {
			return [];
		}
		$ifthenpayController->load->model('extension/payment/ifthenpaygateway');

		$data = $ifthenpayController->model_extension_payment_ifthenpaygateway->getIfthenpaygatewayByOrderId(
			$request[CallbackVars::ORDER_ID]
		)->row;

		return $data;
	}
}
