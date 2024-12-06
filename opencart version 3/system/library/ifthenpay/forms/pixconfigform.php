<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Request\WebService;


class PixConfigForm extends ConfigForm
{
	protected $paymentMethod = Gateway::PIX;
	protected $paymentMethodDefaultTitle = 'Pix';

	protected $hasCallback = true;


	public function setOptions(): void
	{
		$this->addToOptions();
	}

	protected function checkIfEntidadeSubEntidadeIsSet(): bool
	{
		if (!isset($this->configData['payment_pix_pixKey']) && !isset($this->data['payment_pix_pixKey'])) {
			return false;
		}
		return true;
	}

	public function getForm(): array
	{
		$this->data['entry_pix_pixKey'] = $this->ifthenpayController->language->get('entry_pix_pixKey');
		if (
			$this->ifthenpayController->config->get('payment_pix_userPaymentMethods') &&
			$this->ifthenpayController->config->get('payment_pix_userAccount')
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
            $this->data['pix_pixKeys'] = $this->options;
        }

        if (isset($this->ifthenpayController->request->post['payment_pix_pixKey']) && $this->ifthenpayController->request->post['payment_pix_pixKey']) {
            $this->data['payment_pix_pixKey'] = $this->ifthenpayController->request->post['payment_pix_pixKey'];
        } else if (isset($this->configData['payment_pix_pixKey'])) {
            $this->data['payment_pix_pixKey'] = $this->configData['payment_pix_pixKey'];
        }

		if (isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_payment_method_title'])) {
			$this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $this->ifthenpayController
				->request->post['payment_' . $this->paymentMethod . '_payment_method_title'];
		} else {
			$paymentMethodTitleFromConfig = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_payment_method_title');

			$this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $paymentMethodTitleFromConfig != '' ? $paymentMethodTitleFromConfig : $this->paymentMethodDefaultTitle;
		}

        parent::setGatewayBuilderData();
        if (isset($this->data['payment_pix_pixKey'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_pix_pixKey']);
        }
    }


	public function deleteConfigValues(): void
	{
		$this->ifthenpayController->load->model('extension/payment/pix');
		$this->ifthenpayController->model_extension_payment_pix->deleteSettingByKey('payment_pix_pixKey');
	}
}
