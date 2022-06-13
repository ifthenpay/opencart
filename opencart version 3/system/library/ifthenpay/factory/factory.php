<?php

declare(strict_types=1);

namespace Ifthenpay\Factory;

use Illuminate\Container\Container;

class Factory 
{
    protected $type;
    protected $ioc;
    protected $ifthenpayController;
    protected $configData;

	public function __construct(Container $ioc)
	{
        $this->ioc = $ioc;
    }
    
     /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the value of ifthenpayController
     *
     * @return  self
     */ 
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }
}