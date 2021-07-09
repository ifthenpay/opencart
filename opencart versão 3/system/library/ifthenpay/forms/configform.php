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
        if (!$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_urlCallback')
            && !$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_chaveAntiPhishing')
        ) {
            return false;
        }
        return true;
    }
   
    protected function addToOptions(): void
    {
        $this->ifthenpayGateway->setAccount((array) unserialize($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_userAccount')));
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
        $backofficeKey = is_null($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_backofficeKey')) ? $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_backofficeKey'] : $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_backofficeKey');
        $this->gatewayDataBuilder->setBackofficeKey($backofficeKey);
    }

    protected function getCallbackControllerUrl(): string
    {
        return ($this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) . 
            '/index.php?route=extension/payment/' . $this->paymentMethod . '/callback';
    }

    protected function setIfthenpayCallback(): void
    {
        if ($this->checkIfEntidadeSubEntidadeIsSet()) {
            $this->data['displayCallbackTableInfo'] = $this->checkIfCallbackIsSet() ? true : false;
            if ($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_callback_activated')
            ) {
                $this->data['isCallbackActivated'] = true;
            } else {
                $this->data['isCallbackActivated'] = false;
            }

            $paymentIfthenpaySandbox = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_sandboxMode');

            if(is_null($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_activateCallback'))) {
                $paymentIfthenpayActivateCallback = $this->ifthenpayController->request->post['payment_' . $this->paymentMethod. '_activateCallback'];
            } else {
                $paymentIfthenpayActivateCallback = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_activateCallback');
            }

            if(!is_null($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_activateCallback')) && isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod. '_activateCallback']) && $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_activateCallback') !== $this->ifthenpayController->request->post['payment_' . $this->paymentMethod. '_activateCallback']) {
                $paymentIfthenpayActivateCallback = $this->ifthenpayController->request->post['payment_' . $this->paymentMethod. '_activateCallback'];
            }

            if(!is_null($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_sandboxMode')) && isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_sandboxMode']) && $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_sandboxMode') !== $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_sandboxMode']) {
                $paymentIfthenpaySandbox = $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_sandboxMode'];
            }
            
            $activateCallback = !$paymentIfthenpaySandbox && $paymentIfthenpayActivateCallback && !$this->data['isCallbackActivated'] && 
                !empty($this->ifthenpayController->request->post) ? true : false;
            
            $ifthenpayCallback = $this->ioc->makeWith(Callback::class, ['data' => $this->gatewayDataBuilder]);
            $ifthenpayCallback->make($this->paymentMethod, $this->getCallbackControllerUrl(), $activateCallback);
            $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_callback_activated'] = $ifthenpayCallback->getActivatedFor();
            $this->data['payment_' . $this->paymentMethod . '_callback_activated_for'] = $ifthenpayCallback->getActivatedFor();
            if ($activateCallback) {
                $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                $this->data['payment_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->data['payment_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
            } else if (!$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_urlCallback') && !$this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_chaveAntiPhishing')) {
                $this->data['payment_' . $this->paymentMethod . '_urlCallback'] = $ifthenpayCallback->getUrlCallback();
                $this->data['payment_' . $this->paymentMethod . '_chaveAntiPhishing'] = $ifthenpayCallback->getChaveAntiPhishing();
                $this->data['displayCallbackTableInfo'] = true;
            } else {
                $this->data['payment_' . $this->paymentMethod . '_urlCallback'] = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_urlCallback');
                $this->data['payment_' . $this->paymentMethod . '_chaveAntiPhishing'] = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_chaveAntiPhishing');
            }
        }
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
