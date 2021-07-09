<?php

declare(strict_types=1);

namespace Ifthenpay\Strategy\Form;

use Ifthenpay\Factory\Config\IfthenpayConfigFormFactory;

class IfthenpayConfigForms
{
    private $paymentMethod;
    private $ifthenpayController;
    private $configForm;

	public function __construct(IfthenpayConfigFormFactory $ifthenpayConfigFormFactory)
	{
        $this->ifthenpayConfigFormFactory = $ifthenpayConfigFormFactory;
	}
    
    public function buildForm(): array
    {
        return $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->setConfigData($this->configData)
            ->getForm();
    }

    public function processForm(): void
    {
        $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->setConfigData($this->configData)
            ->processForm();
    }

    public function deleteConfigFormValues(): void
    {
        $this->ifthenpayConfigFormFactory->setType($this->paymentMethod)
            ->build()
            ->setIfthenpayController($this->ifthenpayController)
            ->deleteConfigValues();
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

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Set the value of configData
     *
     * @return  self
     */ 
    public function setConfigData($configData)
    {
        $this->configData = $configData;

        return $this;
    }
}
