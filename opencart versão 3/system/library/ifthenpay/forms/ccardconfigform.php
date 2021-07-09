<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;

class CCardConfigForm extends ConfigForm
{
    protected $paymentMethod = 'ccard';
    protected $hasCallback = false;


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
        $this->setOptions();
        $this->setHasCallback();
        $this->data['entry_ccard_ccardKey'] = $this->ifthenpayController->language->get('entry_ccard_ccardKey');
        $this->setGatewayBuilderData();        

        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {
        if (isset($this->ifthenpayController->request->post['payment_ccard_ccardKey'])) {
            $this->data['payment_ccard_ccardKey'] = $this->ifthenpayController->request->post['payment_ccard_ccardKey'];
        } else if (isset($this->configData['payment_ccard_ccardKey'])) {
            $this->data['payment_ccard_ccardKey'] = $this->configData['payment_ccard_ccardKey'];
        } else {
            $this->data['ccard_ccardKeys'] = $this->options;
        }
       
        if (isset($this->request->post['payment_ccard_order_status_canceled_id'])) {
            $this->data['payment_ccard_order_status_canceled_id'] = $this->ifthenpayController->request->post['payment_ccard_order_status_canceled_id'];
        } else {
            $this->data['payment_ccard_order_status_canceled_id'] = $this->ifthenpayController->config->get('payment_ccard_order_status_canceled_id');
        }

        if (isset($this->request->post['payment_ccard_order_status_failed_id'])) {
            $this->data['payment_ccard_order_status_failed_id'] = $this->ifthenpayController->request->post['payment_ccard_order_status_failed_id'];
        } else {
            $this->data['payment_ccard_order_status_failed_id'] = $this->ifthenpayController->config->get('payment_ccard_order_status_failed_id');
        }

        $this->ifthenpayController->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->ifthenpayController->model_localisation_order_status->getOrderStatuses();

        parent::setGatewayBuilderData();
        if (isset($this->data['payment_ccard_ccardKey'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_ccard_ccardKey']);
        }
        
    }

    public function processForm(): void
    {
        $this->setHasCallback();
        $this->setGatewayBuilderData();
    }

    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ccard');
        $this->ifthenpayController->model_extension_payment_ccard->deleteSettingByKey('payment_ccard_ccardKey');
    }
}
