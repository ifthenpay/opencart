<?php
declare(strict_types=1);

namespace Ifthenpay\Factory\Callback;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Callback\CallbackDataCCard;
use Ifthenpay\Callback\CallbackDataMbway;
use Ifthenpay\Callback\CallbackDataPayshop;
use Ifthenpay\Callback\CallbackDataMultibanco;
use Ifthenpay\Contracts\Callback\CallbackDataInterface;


class CallbackDataFactory extends Factory
{
    public function build(): CallbackDataInterface
    {
        switch (strtolower($this->type)) {
            case 'multibanco':
                return new CallbackDataMultibanco();
            case 'mbway':
                return new CallbackDataMbway();
            case 'payshop':
                return new CallbackDataPayshop();
            case 'ccard':
                return new CallbackDataCCard();
            default:
                throw new \Exception('Unknown Callback Data Class');
        }
    }
}
