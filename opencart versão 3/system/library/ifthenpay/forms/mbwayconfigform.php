<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;

class MbwayConfigForm extends ConfigForm
{
    protected $paymentMethod = 'mbway';
    

    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (!isset($this->configData['payment_ifthenpay_mbway_mbwayKey']) && !isset($this->data['payment_ifthenpay_mbway_mbwayKey'])) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->setOptions();
        $this->setHasCallback();
        $this->data['entry_mbway_mbwayKey'] = $this->ifthenpayController->language->get('entry_mbway_mbwayKey');
        $this->setGatewayBuilderData(); 
        $this->setIfthenpayCallback();         

        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {
        if (isset($this->ifthenpayController->request->post['payment_ifthenpay_mbway_mbwayKey'])) {
            $this->data['payment_ifthenpay_mbway_mbwayKey'] = $this->ifthenpayController->request->post['payment_ifthenpay_mbway_mbwayKey'];
        } else if (isset($this->configData['payment_ifthenpay_mbway_mbwayKey'])) {
            $this->data['payment_ifthenpay_mbway_mbwayKey'] = $this->configData['payment_ifthenpay_mbway_mbwayKey'];
        } else {
            $this->data['mbway_mbwayKeys'] = $this->options;
        }

        if (isset($this->request->post['payment_ifthenpay_mbway_order_status_canceled_id'])) {
            $this->data['payment_ifthenpay_mbway_order_status_canceled_id'] = $this->ifthenpayController->request->post['payment_ifthenpay_mbway_order_status_canceled_id'];
        } else {
            $this->data['payment_ifthenpay_mbway_order_status_canceled_id'] = $this->ifthenpayController->config->get('payment_ifthenpay_mbway_order_status_canceled_id');
        }

        $this->ifthenpayController->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->ifthenpayController->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_ifthenpay_mbway_activate_cancelMbwayOrder'])) {
			$this->data['payment_ifthenpay_mbway_activate_cancelMbwayOrder'] = $this->request->post['payment_ifthenpay_mbway_activate_cancelMbwayOrder'];
		} else if (isset($this->configData['payment_ifthenpay_mbway_activate_cancelMbwayOrder'])) {
			$this->data['payment_ifthenpay_mbway_activate_cancelMbwayOrder'] = $this->configData['payment_ifthenpay_mbway_activate_cancelMbwayOrder'];
		} else {
			$this->data['payment_ifthenpay_mbway_activate_cancelMbwayOrder'] = '0';
		}    
        
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_ifthenpay_mbway_mbwayKey'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_ifthenpay_mbway_mbwayKey']);
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
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_mbway_mbwayKey');
        $this->deleteDefaultConfigValues();    
    }
}
