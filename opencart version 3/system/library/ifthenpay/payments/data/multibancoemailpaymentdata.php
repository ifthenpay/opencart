<?php

declare(strict_types=1);

namespace Ifthenpay\Payments\Data;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Payments\Data\EmailPaymentData;
use Ifthenpay\Traits\Payments\FormatReference;

class MultibancoEmailPaymentData extends EmailPaymentData
{
    use FormatReference;

    protected $paymentMethod = Gateway::MULTIBANCO;
    
    protected function setTwigVariables(): void
    {
        $this->setDefaultTwigVariables();
        $this->twigDefaultData->setIfthenpayPaymentPanelEntidade(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelEntidade')
        );
        $this->twigDefaultData->setEntidade($this->payment['entidade']);
        $this->twigDefaultData->setIfthenpayPaymentPanelReferencia(
            $this->ifthenpayController->language->get('ifthenpayPaymentPanelReferencia')
        );
        $this->twigDefaultData->setReferencia($this->formatReference($this->payment['referencia']));
        if ($this->payment['validade']) {
            $this->twigDefaultData->setValidade($this->payment['validade']);
            $this->twigDefaultData->setIfthenpayPaymentPanelValidade(
                $this->ifthenpayController->language->get('ifthenpayPaymentPanelValidade')
            );
        }
    }
}