<?php
declare(strict_types=1);

namespace Ifthenpay\Factory\Callback;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Callback\CallbackDataCCard;
use Ifthenpay\Callback\CallbackDataMbway;
use Ifthenpay\Callback\CallbackDataPayshop;
use Ifthenpay\Callback\CallbackDataMultibanco;
use Ifthenpay\Contracts\Callback\CallbackDataInterface;
use Ifthenpay\Payments\Gateway;


class CallbackDataFactory extends Factory
{
    public function build(): CallbackDataInterface
    {
        switch (strtolower($this->type)) {
            case Gateway::MULTIBANCO:
                return new CallbackDataMultibanco();
            case Gateway::MBWAY:
                return new CallbackDataMbway();
            case Gateway::PAYSHOP:
                return new CallbackDataPayshop();
            case Gateway::CCARD:
                return new CallbackDataCCard();
            default:
                throw new \Exception('Unknown Callback Data Class');
        }
    }
}
