<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Callback\Callback;
use Illuminate\Container\Container;
use Ifthenpay\Builders\GatewayDataBuilder;

abstract class ConfigForm
{
    protected $paymentMethod;
    protected $form;
    protected $ifthenpayController;
    protected $gatewayDataBuilder;
    private $ifthenpayGateway;
    protected $options;
    protected $formFactory;
    protected $configData;
    protected $data;
    protected $ioc;
    protected $hasCallback = true;

    public function __construct(Container $ioc, GatewayDataBuilder $gatewayDataBuilder, Gateway $gateway)
    {
        $this->ioc = $ioc;
        $this->gatewayDataBuilder = $gatewayDataBuilder;
        $this->ifthenpayGateway = $gateway;
        $this->options = [];
        $this->data = [];
    }

    protected function setHasCallback(): void
    {
        $this->data['hasCallback'] = $this->hasCallback;
    }

    protected function checkIfCallbackIsSet(): bool
    {
        if (!isset($this->configData['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'])
            && !isset($this->configData['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'])
        ) {
            return false;
        }
        return true;
    }
   
    protected function addToOptions(): void
    {
        $this->ifthenpayGateway->setAccount((array) unserialize($this->configData['payment_ifthenpay_userAccount']));
        $this->options[] = [
            'value' => $this->ifthenpayController->language->get('choose_entity')
        ];
        foreach ($this->ifthenpayGateway->getEntidadeSubEntidade($this->paymentMethod) as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $value2) {
                    if (strlen($value2) > 3) {
                        $this->options[] = [
                            'value' => $value2,
                        ];
                    }
                }
            } else {
                $this->options[] = [
                    'value' => $value,
                ];
            }
        }
    }

    protected function setGatewayBuilderData(): void
    {
        $this->gatewayDataBuilder->setBackofficeKey($this->configData['payment_ifthenpay_backofficeKey']);
    }

    protected function getCallbackControllerUrl(): string
    {
        return ($this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) . 
            '/index.php?route=extension/payment/ifthenpay/callback';
    }

    protected function setIfthenpayCallback(): void
    {
        if ($this->checkIfEntidadeSubEntidadeIsSet()) {
            $this->data['displayCallbackTableInfo'] = $this->checkIfCallbackIsSet() ? true : false;
            if (isset($this->configData['payment_ifthenpay_callback_activated_for_' . $this->paymentMethod]) &&
                $this->configData['payment_ifthenpay_callback_activated_for_' . $this->paymentMethod]
            ) {
                $this->data['isCallbackActivated'] = true;
            } else {
                $this->data['isCallbackActivated'] = false;
            }

            if (!isset($this->configData['payment_ifthenpay_activateCallback_' . $this->paymentMethod])) {
                $this->configData['payment_ifthenpay_activateCallback_' . $this->paymentMethod] = $this->ifthenpayController->request->post['payment_ifthenpay_activateCallback_' . $this->paymentMethod];
            }
            
            $activateCallback = !$this->configData['payment_ifthenpay_sandboxMode'] && 
            $this->configData['payment_ifthenpay_activateCallback_' . $this->paymentMethod] && 
            !$this->data['isCallbackActivated'] && !empty($this->ifthenpayController->request->post) ? true : false;
            
            $ifthenpayCallback = $this->ioc->makeWith(Callback::class, ['data' => $this->gatewayDataBuilder]);
            $ifthenpayCallback->make($this->paymentMethod, $this->getCallbackControllerUrl(), $activateCallback);
            $this->ifthenpayController->request->post['payment_ifthenpay_callback_activated_for_' . $this->paymentMethod] = $ifthenpayCallback->getActivatedFor();
            $this->data['payment_ifthenpay_callback_activated_for_' . $this->paymentMethod] = $ifthenpayCallback->getActivatedFor();
            if ($activateCallback) {
                $this->ifthenpayController->request->post['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->ifthenpayController->request->post['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
            } else if (!isset($this->configData['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback']) && !isset($this->configData['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'])) {
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                $this->data['displayCallbackTableInfo'] = true;
            } else {
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'] = $this->configData['payment_ifthenpay_' . $this->paymentMethod . '_urlCallback'];
                $this->data['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'] = $this->configData['payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing'];
            }
        }
    }

    protected function deleteDefaultConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/module/ifthenpay_manage_payment_method');
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_' . $this->paymentMethod . '_urlCallback');
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_' . $this->paymentMethod . '_chaveAntiPhishing');
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_callback_activated_for_' . $this->paymentMethod);
        $this->ifthenpayController->model_extension_module_ifthenpay_manage_payment_method->deleteSettingByKey('payment_ifthenpay_activateCallback_' . $this->paymentMethod);
    }

    abstract protected function setOptions(): void;
    abstract public function getForm(): array;
    abstract public function processForm(): void;
    abstract public function deleteConfigValues(): void;
    abstract protected function checkIfEntidadeSubEntidadeIsSet(): bool;

    /**
     * Set the value of paymentMethod
     *
     * @return  self
     */ 
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Set the value of ifthenpayController
     *
     * @return  self
     */ 
    public function setIfthenpayController($ifthenpayController)
    {
        $this->ifthenpayController = $ifthenpayController;

        return $this;
    }

    /**
     * Set the value of configData
     *
     * @return  self
     */ 
    public function setConfigData($configData)
    {
        $this->configData = $configData;

        return $this;
    }
}