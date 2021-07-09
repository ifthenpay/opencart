<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;

class MultibancoConfigForm extends ConfigForm
{
    protected $paymentMethod = 'multibanco';

    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (!isset($this->configData['payment_multibanco_entidade'])
            && !isset($this->configData['payment_multibanco_subEntidade']) && 
            !isset($this->data['payment_multibanco_entidade']) && !isset($this->data['payment_multibanco_subEntidade'])
        ) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->setOptions();
        $this->setHasCallback();
        $this->data['entry_multibanco_entidade'] = $this->ifthenpayController->language->get('entry_multibanco_entidade');
        $this->data['entry_multibanco_SubEntidade'] = $this->ifthenpayController->language->get('entry_multibanco_SubEntidade');
        $this->setGatewayBuilderData(); 
        $this->setIfthenpayCallback();         

        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {
        if (isset($this->ifthenpayController->request->post['payment_multibanco_entidade'])) {
            $this->data['payment_multibanco_entidade'] = $this->ifthenpayController->request->post['payment_multibanco_entidade'];
        } else if (isset($this->configData['payment_multibanco_entidade'])) {
            $this->data['payment_multibanco_entidade'] = $this->configData['payment_multibanco_entidade'];
        } else {
            $this->data['multibanco_entidades'] = $this->options;
        }
        
        if (isset($this->ifthenpayController->request->post['payment_multibanco_subEntidade'])) {
            $this->data['payment_multibanco_subEntidade'] = $this->ifthenpayController->request->post['payment_multibanco_subEntidade'];
        } else if (isset($this->configData['payment_multibanco_subEntidade'])) {
            $this->data['payment_multibanco_subEntidade'] = $this->configData['payment_multibanco_subEntidade']; 
        } else {
            $this->data['multibanco_subEntidade'] = $this->ifthenpayController->language->get('choose_entity');
        }
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_multibanco_entidade']) && isset($this->data['payment_multibanco_subEntidade'])) {
            $this->gatewayDataBuilder->setEntidade($this->data['payment_multibanco_entidade']);
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_multibanco_subEntidade']);
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
        $this->ifthenpayController->load->model('extension/payment/multibanco');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_urlCallback');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_chaveAntiPhishing');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_callback_activated');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_activateCallback');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_entidade');
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_subEntidade');    
    }
}
