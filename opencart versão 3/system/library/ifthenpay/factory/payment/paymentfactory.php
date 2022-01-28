<?php

declare(strict_types=1);

namespace Ifthenpay\Factory\Payment;


use Illuminate\Container\Container;
use Ifthenpay\Payments\CCard;
use Ifthenpay\Payments\MbWay;
use Ifthenpay\Factory\Factory;
use Ifthenpay\Payments\Payshop;
use Ifthenpay\Payments\Multibanco;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Contracts\Payments\PaymentMethodInterface;
use Ifthenpay\Request\WebService;
use Ifthenpay\Payments\Gateway;


class PaymentFactory extends Factory
{
    private $data;
    private $orderId;
    private $valor;
    private $webService;

    public function __construct(Container $ioc, WebService $webService)
	{
        parent::__construct($ioc);
        $this->webService = $webService;
    }

    
    public function build(): PaymentMethodInterface
    {
        switch ($this->type) {
            case Gateway::MULTIBANCO:
                return new Multibanco($this->data, $this->orderId, $this->valor, $this->webService);
            case Gateway::MBWAY:
                return new MbWay($this->data, $this->orderId, $this->valor, $this->webService);
            case Gateway::PAYSHOP:
                return new Payshop($this->data, $this->orderId, $this->valor, $this->webService);
            case Gateway::CCARD:
                return new CCard($this->data, $this->orderId, $this->valor, $this->webService);
            default:
                throw new \Exception("Unknown Payment Class");
        }
    }

    /**
     * Set the value of orderId
     *
     * @return  self
     */ 
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
