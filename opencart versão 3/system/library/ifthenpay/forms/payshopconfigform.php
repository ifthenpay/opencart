<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;

class PayshopConfigForm extends ConfigForm
{
    protected $paymentMethod = 'payshop';

    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (!isset($this->configData['payment_ifthenpay_payshop_payshopKey'])
            && !isset($this->configData['payment_ifthenpay_payshop_validade']) && !isset($this->data['payment_ifthenpay_payshop_payshopKey']) && !isset($this->data['payment_ifthenpay_payshop_validade'])
        ) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->setOptions();
        $this->setHasCallback();
        $this->data['entry_payshop_payshopKey'] = $this->ifthenpayController->language->get('entry_payshop_payshopKey');
        $this->data['entry_payshop_validade'] = $this->ifthenpayController->language->get('entry_payshop_validade');
        $this->data['payshop_validade_helper'] = $this->ifthenpayController->language->get('payshop_validade_helper');
        $this->setGatewayBuilderData(); 
        $this->setIfthenpayCallback();         

        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {
        if (isset($this->ifthenpayController->request->post['payment_ifthenpay_payshop_payshopKey'])) {
            $this->data['payment_ifthenpay_payshop_payshopKey'] = $this->ifthenpayController->request->post['payment_ifthenpay_payshop_payshopKey'];
        } else if (isset($this->configData['payment_ifthenpay_payshop_payshopKey'])) {
            $this->data['payment_ifthenpay_payshop_payshopKey'] = $this->configData['payment_ifthenpay_payshop_payshopKey'];
        } else {
            $this->data['payshop_payshopKeys'] = $this->options;
        }
        
        if (isset($this->ifthenpayController->request->post['payment_ifthenpay_payshop_validade'])) {
            $this->data['payment_ifthenpay_payshop_validade'] = $this->ifthenpayController->request->post['payment_ifthenpay_payshop_validade'];
        } else if (isset($this->configData['payment_ifthenpay_payshop_validade'])) {
            $this->data['payment_ifthenpay_payshop_validade'] = $this->configData['payment_ifthenpay_payshop_validade']; 
        } else {
            $this->data['payment_ifthenpay_payshop_validade'] = null;
        }
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_ifthenpay_payshop_payshopKey']) && isset($this->data['payment_ifthenpay_payshop_validade'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_ifthenpay_payshop_payshopKey']);
            $this->gatewayDataBuilder->setValidade($this->data['payment_ifthenpay_payshop_validade']);
        }
        
    }

    public function processForm(): void
    {
        $this->setHasCallback();
        $this->setGatewayBuilderData();
        $this->setIfthenpayCallback();
    }

    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/module/ifthenpay_manage_payment_method');
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_payshop_payshopKey');
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_payshop_validade');
        $this->deleteDefaultConfigValues();    
    }
}