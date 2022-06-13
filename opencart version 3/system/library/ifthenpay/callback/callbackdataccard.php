<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataCCard implements CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array
    {
        $ifthenpayController->load->model('extension/payment/ccard');
		
		return $ifthenpayController->model_extension_payment_ccard->getCCardByRequestId(
            $request['requestId']
        )->row; 
    }
}
