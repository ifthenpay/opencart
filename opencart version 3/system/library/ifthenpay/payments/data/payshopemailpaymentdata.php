<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Payments\Data\EmailPaymentData;
use Ifthenpay\Traits\Payments\FormatReference;

class PayshopEmailPaymentData extends EmailPaymentData
{
    use FormatReference;

    protected $paymentMethod = Gateway::PAYSHOP;
    
    protected function setTwigVariables(): void
    {
        $this->setDefaultTwigVariables();
        $this->twigDefaultData->setIfthenpayPaymentPanelEntidade(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelEntidade')
        );
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia')
        );
        $this->twigDefaultData->setReferencia($this->formatReference($this->payment['referencia']));
        $this->twigDefaultData->setValidade($this->payment['validade'] ? 
            (new \DateTime($this->payment['validade']))->format('d-m-Y') : ''
        );
        $this->twigDefaultData->setIfthenpayPaymentPanelValidade(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade')
        );
    }
}