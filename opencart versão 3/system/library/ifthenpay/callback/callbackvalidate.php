<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

class CallbackValidate
{
    private $httpRequest;
    private $order;
    private $configurationChaveAntiPhishing;
    private $paymentDataFromDb;
    private $configPaidStatus;

    private function validateOrder(): void
    {
        if (!$this->order) {
            throw new \Exception('Ordem não encontrada.');
        }
    }

    private function validateOrderValue(): void
    {
        $orderTotal = floatval($this->order['total']);
        $requestValor = floatval($this->httpRequest['valor']);
        if (round($orderTotal, 2) !== round($requestValor, 2)) {
            throw new \Exception('Valor não corresponde ao valor da encomenda.');
        }
    }

    private function validateOrderStatus(): void
    {
        if ($this->paymentDataFromDb['status'] === 'paid' && $this->order['order_status'] === $this->configPaidStatus) {
            throw new \Exception('Encomenda já foi paga.');
        }
    }

    private function validateChaveAntiPhishing()
    {
        if (!$this->httpRequest['chave']) {
            throw new \Exception('Chave Anti-Phishing não foi enviada.');
        }

        if ($this->httpRequest['chave'] !== $this->configurationChaveAntiPhishing) {
            throw new \Exception('Chave Anti-Phishing não é válida.');
        }
    }

    public function validate(): bool
    {
        $this->validateChaveAntiPhishing();
        $this->validateOrder();
        $this->validateOrderValue();
        $this->validateOrderStatus();
        return true;
    }

    /**
     * Set the value of httpRequest
     *
     * @return  self
     */ 
    public function setHttpRequest(array $httpRequest)
    {
        $this->httpRequest = $httpRequest;

        return $this;
    }

    /**
     * Set the value of order
     *
     * @return  self
     */ 
    public function setOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set the value of configurationChaveAntiPhishing
     *
     * @return  self
     */ 
    public function setConfigurationChaveAntiPhishing(string $configurationChaveAntiPhishing)
    {
        $this->configurationChaveAntiPhishing = $configurationChaveAntiPhishing;

        return $this;
    }

    /**
     * Set the value of paymentDataFromDb
     *
     * @return  self
     */ 
    public function setPaymentDataFromDb(array $paymentDataFromDb)
    {
        $this->paymentDataFromDb = $paymentDataFromDb;

        return $this;
    }

    /**
     * Set the value of configPaidStatus
     *
     * @return  self
     */ 
    public function setConfigPaidStatus($configPaidStatus)
    {
        $this->configPaidStatus = $configPaidStatus;

        return $this;
    }
}
