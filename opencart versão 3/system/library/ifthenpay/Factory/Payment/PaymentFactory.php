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
use Ifthenpay\Request\Webservice;


class PaymentFactory extends Factory
{
    private $data;
    private $orderId;
    private $valor;
    private $dataBuilder;
    private $webservice;

    public function __construct(Container $ioc, DataBuilder $dataBuilder, Webservice $webservice = null)
	{
        parent::__construct($ioc);
        $this->dataBuilder = $dataBuilder;
        $this->webservice = $webservice;
    }

    
    public function build(): PaymentMethodInterface
    {
        switch ($this->type) {
            case 'multibanco':
                return new Multibanco($this->data, $this->orderId, $this->valor, $this->dataBuilder);
            case 'mbway':
                return new MbWay($this->data, $this->orderId, $this->valor, $this->webservice, $this->dataBuilder);
            case 'payshop':
                return new Payshop($this->data, $this->orderId, $this->valor, $this->webservice, $this->dataBuilder);
            case 'ccard':
                return new CCard($this->data, $this->orderId, $this->valor, $this->webservice, $this->dataBuilder);
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
