<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Config;

use Ifthenpay\Factory\Factory;
use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;
use Illuminate\Container\Container;
use Ifthenpay\Forms\CCardConfigForm;
use Ifthenpay\Forms\MbwayConfigForm;
use Ifthenpay\Forms\PayshopConfigForm;
use Ifthenpay\Forms\MultibancoConfigForm;
use Ifthenpay\Builders\GatewayDataBuilder;

class IfthenpayConfigFormFactory extends Factory
{
    private $gatewayDataBuilder;
    private $gateway;

	public function __construct(Container $ioc, GatewayDataBuilder $gatewayDataBuilder, Gateway $gateway)
	{
        parent::__construct($ioc);
        $this->gateway = $gateway;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
	}
    
    public function build(
    ): ConfigForm {
        switch ($this->type) {
            case 'multibanco':
                return new MultibancoConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway
                );
            case 'mbway':
                return new MbwayConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway
                );
            case 'payshop':
                return new PayshopConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway
                );
            case 'ccard':
                return new CCardConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway
                );
            default:
                throw new \Exception('Unknown Admin Config Form');
        }
    }
}
