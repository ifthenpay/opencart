<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Callback\CallbackVars;

class CallbackDataCCard implements CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array
    {
		$tid = '';
		$tid = $request[CallbackVars::TRANSACTION_ID] ?? '';
		$requestId = $request['requestId'] ?? '';
		$transactionId = $tid = '' ? $tid : $requestId;


		if ($transactionId == '') {
			return [];
		}

        $ifthenpayController->load->model('extension/payment/ccard');

		return $ifthenpayController->model_extension_payment_ccard->getCCardByTransactionId(
            $transactionId
        )->row;
    }
}
