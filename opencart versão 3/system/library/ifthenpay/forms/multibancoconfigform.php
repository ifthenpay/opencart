<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Multibanco;
use Ifthenpay\Payments\Gateway;

class MultibancoConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::MULTIBANCO;

    private function setValidity(): array 
    {
        $deadlines = [];
        $deadlines[] = [
            'value' => $this->ifthenpayController->language->get('multibanco_deadline') 
        ];
        for ($i=0; $i < 32; $i++) {
            $deadlines[] = [
                'value' => $i
            ];
        }
        foreach ([45, 60, 90, 120] as $value) {
            $deadlines[] = [
                'value' => $value
            ];
        }
        return $deadlines;
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
        
        $this->data['entry_multibanco_entidade'] = $this->ifthenpayController->language->get('entry_multibanco_entidade');
        $this->data['entry_multibanco_SubEntidade'] = $this->ifthenpayController->language->get('entry_multibanco_SubEntidade');
        if ($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_userPaymentMethods') && 
        $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_userAccount')) {
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
        if (isset($this->ifthenpayController->request->post['payment_multibanco_entidade'])) {
            $this->data['payment_multibanco_entidade'] = $this->ifthenpayController->request->post['payment_multibanco_entidade'];
        } else if (isset($this->configData['payment_multibanco_entidade'])) {
            $this->data['payment_multibanco_entidade'] = $this->configData['payment_multibanco_entidade'];
            $this->data['multibanco_entidades'] = $this->options;
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
        if(isset($this->configData['payment_multibanco_userAccount'])){
        if ($this->ifthenpayGateway->checkDynamicMb(unserialize($this->configData['payment_multibanco_userAccount']))) {
            $this->data['dynamicMb'] = true;
            if (isset($this->ifthenpayController->request->post['payment_multibanco_deadline'])) {
                $this->data['payment_multibanco_deadline'] = $this->ifthenpayController->request->post['payment_multibanco_deadline'];
            } else if (isset($this->configData['payment_multibanco_deadline'])) {
                $this->data['payment_multibanco_deadline'] = $this->configData['payment_multibanco_deadline'];
                $this->data['multibanco_deadlines'] = $this->setValidity();
            } else {
                $this->data['multibanco_deadlines'] = $this->setValidity();
            }
        } else {
            $this->data['dynamicMb'] = false;
            $this->data['dontHaveAccount_multibanco_dynamic'] = $this->ifthenpayController->language->get('dontHaveAccount_multibanco_dynamic');
            $this->data['actionRequestDynamicMultibancoAccount'] = $this->ifthenpayController->url->link('extension/payment/' . $this->paymentMethod . 
                '/requestDynamicMultibancoAccount', 'user_token=' . $this->ifthenpayController->session->data['user_token'], true
            );
            $this->data['requestAccount_multibanco_dynamic'] = $this->ifthenpayController->language->get('requestAccount_multibanco_dynamic');
        }
    }
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_multibanco_entidade']) && isset($this->data['payment_multibanco_subEntidade'])) {
            $this->gatewayDataBuilder->setEntidade($this->data['payment_multibanco_entidade']);
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_multibanco_subEntidade']);
            if ($this->data['payment_multibanco_entidade'] === Multibanco::DYNAMIC_MB_ENTIDADE) {
                $this->gatewayDataBuilder->setValidade($this->data['payment_multibanco_deadline']);
            }
        }
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
        $this->ifthenpayController->model_extension_payment_multibanco->deleteSettingByKey('payment_multibanco_deadline');   
    }
}