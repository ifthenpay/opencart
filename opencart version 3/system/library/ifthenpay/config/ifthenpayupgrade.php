<?php

declare(strict_types=1);

namespace Ifthenpay\Config;

use Ifthenpay\Request\WebService;


class IfthenpayUpgrade
{
    const MODULE_VERSION = '1.1.7';
    private $webService;


    public function __construct(WebService $webService)
    {
        $this->webService = $webService;
    }

    public function getModuleVersion(): string
    {
        return self::MODULE_VERSION;
    }

    public function checkModuleUpgrade(): array
    {
        $response = $this->webService->getRequest('https://ifthenpay.com/modulesUpgrade/opencart/upgrade.json')->getResponseJson();
        if (version_compare(str_replace('v', '', $response['version']), self::MODULE_VERSION, '>')) {
            return [
                'upgrade' => true,
                'body' => $response['description'],
                'download' => $response['download']
            ];
        }
        return [
            'upgrade' => false,
        ];
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
}
