<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataCofidis implements CallbackDataInterface
{
	public function getData(array $request, $ifthenpayController): array
	{
		$ifthenpayController->load->model('extension/payment/cofidis');

		switch ($request['type']) {
			case 'online':
				return $ifthenpayController->model_extension_payment_cofidis->getCofidisByOrderIdAndHash(
					$request['orderId'],
					$request['hash']
				)->row;

			case 'offline':
				return $ifthenpayController->model_extension_payment_cofidis->getCofidisByRequestId($request['id_transacao'])->row;

			default:
				throw new \Exception('Invalid request type when obtaining callback data');
		}
	}
}
