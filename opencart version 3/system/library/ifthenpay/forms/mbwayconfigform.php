<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;

class MbwayConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::MBWAY;
    
    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (!isset($this->configData['payment_mbway_mbwayKey']) && !isset($this->data['payment_mbway_mbwayKey']) && 
            !isset($this->data['payment_mbway_mbwayKey'])) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->data['entry_mbway_mbwayKey'] = $this->ifthenpayController->language->get('entry_mbway_mbwayKey');
        if ($this->ifthenpayController->config->get('payment_mbway_userPaymentMethods') && 
        $this->ifthenpayController->config->get('payment_mbway_userAccount')) {
            $this->addToOptions();
            $this->setHasCallback();
            $this->setGatewayBuilderData(); 
            $this->setIfthenpayCallback();
        } else {
            $this->setDefaultGatewayBuilderData();
        }
        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {

        if (!empty($this->options)) {
            $this->data['mbway_mbwayKeys'] = $this->options;
        }
        
        if (isset($this->ifthenpayController->request->post['payment_mbway_mbwayKey']) && $this->ifthenpayController->request->post['payment_mbway_mbwayKey']) {
            $this->data['payment_mbway_mbwayKey'] = $this->ifthenpayController->request->post['payment_mbway_mbwayKey'];
        } else if (isset($this->configData['payment_mbway_mbwayKey'])) {
            $this->data['payment_mbway_mbwayKey'] = $this->configData['payment_mbway_mbwayKey'];
        }
        
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_mbway_mbwayKey'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_mbway_mbwayKey']);
        }  
    }

    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/payment/mbway');
        $this->ifthenpayController->model_extension_payment_mbway->deleteSettingByKey('payment_mbway_urlCallback');
        $this->ifthenpayController->model_extension_payment_mbway->deleteSettingByKey('payment_mbway_chaveAntiPhishing');
        $this->ifthenpayController->model_extension_payment_mbway->deleteSettingByKey('payment_mbway_callback_activated');
        $this->ifthenpayController->model_extension_payment_mbway->deleteSettingByKey('payment_mbway_activateCallback');
        $this->ifthenpayController->model_extension_payment_mbway->deleteSettingByKey('payment_mbway_mbwayKey');    
    }
}
