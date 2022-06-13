<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Contracts\Callback\CallbackDataInterface;

class CallbackDataMbway implements CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array
    {
        $ifthenpayController->load->model('extension/payment/mbway');
		
		$data = $ifthenpayController->model_extension_payment_mbway->getMbwayByIdTransacao(
            $request['id_pedido']
        )->row;

        if (empty($data)) {
            return $ifthenpayController->model_extension_payment_mbway->getPaymentByOrderId(
                $request['referencia']
            )->row;
        } else {
            return $data;
        }
    }
}
