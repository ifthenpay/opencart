<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Callback;

use Ifthenpay\Callback\CallbackOnline;
use Ifthenpay\Callback\CallbackOffline;

class CallbackStrategy
{
    private $callbackOffline;
    private $callbackOnline;

	public function __construct(CallbackOffline $callbackOffline, CallbackOnline $callbackOnline)
	{
        $this->callbackOffline = $callbackOffline;
        $this->callbackOnline = $callbackOnline;
	}
    
    public function execute(array $request, $ifthenpayController)
    {
        if ($request['type'] === 'offline') {
            return $this->callbackOffline->setIfthenpayController($ifthenpayController)->setPaymentMethod($request['payment'])->setRequest($request)->process();
        } else {
            return $this->callbackOnline->setIfthenpayController($ifthenpayController)->setPaymentMethod($request['payment'])->setRequest($request)->process();
        }
    }
}

