<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;

class PayshopConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::PAYSHOP;

    public function setOptions(): void
    {
        $this->addToOptions();
    }

    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {

        if (
            !isset($this->configData['payment_payshop_payshopKey']) && !isset($this->data['payment_payshop_payshopKey'])
        ) {
            return false;
        }
        return true;
    }

    public function getForm(): array
    {
        $this->data['entry_payshop_payshopKey'] = $this->ifthenpayController->language->get('entry_payshop_payshopKey');
        $this->data['entry_payshop_validade'] = $this->ifthenpayController->language->get('entry_payshop_validade');
        $this->data['payshop_validade_helper'] = $this->ifthenpayController->language->get('payshop_validade_helper');
        if (
            $this->ifthenpayController->config->get('payment_payshop_userPaymentMethods') &&
            $this->ifthenpayController->config->get('payment_payshop_userAccount')
        ) {
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

        if (!empty($this->options)) {
            $this->data['payshop_payshopKeys'] = $this->options;
        }

        if (isset($this->ifthenpayController->request->post['payment_payshop_payshopKey'])) {
            $this->data['payment_payshop_payshopKey'] = $this->ifthenpayController->request->post['payment_payshop_payshopKey'];
        } else if (isset($this->configData['payment_payshop_payshopKey'])) {
            $this->data['payment_payshop_payshopKey'] = $this->configData['payment_payshop_payshopKey'];
        }
        
        if (isset($this->ifthenpayController->request->post['payment_payshop_validade'])) {
            $this->data['payment_payshop_validade'] = $this->ifthenpayController->request->post['payment_payshop_validade'];
        } else if (isset($this->configData['payment_payshop_validade'])) {
            $this->data['payment_payshop_validade'] = $this->configData['payment_payshop_validade'];
        }
        parent::setGatewayBuilderData();
        if (isset($this->data['payment_payshop_payshopKey']) && isset($this->data['payment_payshop_validade'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_payshop_payshopKey']);
            $this->gatewayDataBuilder->setValidade($this->data['payment_payshop_validade']);
        }
    }


    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/payment/payshop');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_urlCallback');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_chaveAntiPhishing');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_callback_activated');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_activateCallback');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_payshopKey');
        $this->ifthenpayController->model_extension_payment_payshop->deleteSettingByKey('payment_payshop_validade');
    }
}
