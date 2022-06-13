<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;

class CCardConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::CCARD;
    protected $hasCallback = true;


    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (!isset($this->configData['payment_ccard_ccardKey']) && !isset($this->data['payment_ccard_ccardKey'])) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->data['entry_ccard_ccardKey'] = $this->ifthenpayController->language->get('entry_ccard_ccardKey');
        if ($this->ifthenpayController->config->get('payment_ccard_userPaymentMethods') && 
        $this->ifthenpayController->config->get('payment_ccard_userAccount')) {
            $this->setOptions();
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
        
        if ($this->ifthenpayController->config->get('payment_ccard_userPaymentMethods') && 
        $this->ifthenpayController->config->get('payment_ccard_userAccount')) {

            if (!empty($this->options)) {
                $this->data['ccard_ccardKeys'] = $this->options;
            }

            parent::setGatewayBuilderData();
            if (isset($this->ifthenpayController->request->post['payment_ccard_ccardKey'])) {
                $this->data['payment_ccard_ccardKey'] = $this->ifthenpayController->request->post['payment_ccard_ccardKey'];
            } else if (isset($this->configData['payment_ccard_ccardKey'])) {
                $this->data['payment_ccard_ccardKey'] = $this->configData['payment_ccard_ccardKey'];
            }
    
            if (isset($this->ifthenpayController->request->post['payment_ccard_order_status_failed_id'])) {
                $this->data['payment_ccard_order_status_failed_id'] = $this->ifthenpayController->request->post['payment_ccard_order_status_failed_id'];
            } else {
                $this->data['payment_ccard_order_status_failed_id'] = $this->ifthenpayController->config->get('payment_ccard_order_status_failed_id');
            }
    
            $this->ifthenpayController->load->model('localisation/order_status');
            $this->data['order_statuses'] = $this->ifthenpayController->model_localisation_order_status->getOrderStatuses();
    
            
            if (isset($this->data['payment_ccard_ccardKey'])) {
                $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
                $this->gatewayDataBuilder->setSubEntidade($this->data['payment_ccard_ccardKey']);
            }
        } else {
            parent::setGatewayBuilderData();
        }
        
    }


    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ccard');
        $this->ifthenpayController->model_extension_payment_ccard->deleteSettingByKey('payment_ccard_ccardKey');
    }
}
