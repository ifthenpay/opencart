<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataPayshop implements CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array
    {
        $ifthenpayController->load->model('extension/payment/payshop');
		
		return $ifthenpayController->model_extension_payment_payshop->getPayshopByIdTransacao(
            $request['id_transacao']
        )->row; 
    }
}
