<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;


class CallbackDataMultibanco implements CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array
    {
        $ifthenpayController->load->model('extension/payment/multibanco');
		
		return $ifthenpayController->model_extension_payment_multibanco->getMultibancoByReferencia(
            $request['referencia']
        )->row; 
    }
}
