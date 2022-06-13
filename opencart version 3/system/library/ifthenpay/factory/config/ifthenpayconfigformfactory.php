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
use Ifthenpay\Utility\Mix;

class IfthenpayConfigFormFactory extends Factory
{
    private $gatewayDataBuilder;
    private $gateway;
    private $mix;

	public function __construct(Container $ioc, GatewayDataBuilder $gatewayDataBuilder, Gateway $gateway, Mix $mix)
	{
        parent::__construct($ioc);
        $this->gateway = $gateway;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->mix = $mix;
	}
    
    public function build(
    ): ConfigForm {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new MultibancoConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->mix
                );
            case Gateway::MBWAY:
                return new MbwayConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->mix
                );
            case Gateway::PAYSHOP:
                return new PayshopConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->mix
                );
            case Gateway::CCARD:
                return new CCardConfigForm(
                    $this->ioc,
                    $this->gatewayDataBuilder,
                    $this->gateway,
                    $this->mix
                );
            default:
                throw new \Exception('Unknown Admin Config Form');
        }
    }
}
