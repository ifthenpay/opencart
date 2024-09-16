<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Request\WebService;


class CofidisConfigForm extends ConfigForm
{
	protected $paymentMethod = Gateway::COFIDIS;
	protected $paymentMethodDefaultTitle = 'Codidis Pay';

	protected $hasCallback = true;


	public function setOptions(): void
	{
		$this->addToOptions();
	}

	protected function checkIfEntidadeSubEntidadeIsSet(): bool
	{
		if (!isset($this->configData['payment_cofidis_cofidisKey']) && !isset($this->data['payment_cofidis_cofidisKey'])) {
			return false;
		}
		return true;
	}

	public function getForm(): array
	{
		$this->data['entry_cofidis_cofidisKey'] = $this->ifthenpayController->language->get('entry_cofidis_cofidisKey');
		if (
			$this->ifthenpayController->config->get('payment_cofidis_userPaymentMethods') &&
			$this->ifthenpayController->config->get('payment_cofidis_userAccount')
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

		if (
			$this->ifthenpayController->config->get('payment_cofidis_userPaymentMethods') &&
			$this->ifthenpayController->config->get('payment_cofidis_userAccount')
		) {

			if (!empty($this->options)) {
				$this->data['cofidis_cofidisKeys'] = $this->options;
			}

			parent::setGatewayBuilderData();
			if (isset($this->ifthenpayController->request->post['payment_cofidis_cofidisKey'])) {
				$this->data['payment_cofidis_cofidisKey'] = $this->ifthenpayController->request->post['payment_cofidis_cofidisKey'];
			} else if (isset($this->configData['payment_cofidis_cofidisKey'])) {
				$this->data['payment_cofidis_cofidisKey'] = $this->configData['payment_cofidis_cofidisKey'];
			}

			if (isset($this->ifthenpayController->request->post['payment_cofidis_order_status_failed_id'])) {
				$this->data['payment_cofidis_order_status_failed_id'] = $this->ifthenpayController->request->post['payment_cofidis_order_status_failed_id'];
			} else {
				$this->data['payment_cofidis_order_status_failed_id'] = $this->ifthenpayController->config->get('payment_cofidis_order_status_failed_id');
			}

			if (isset($this->ifthenpayController->request->post['payment_cofidis_order_status_not_approved_id'])) {
                $this->data['payment_cofidis_order_status_not_approved_id'] = $this->ifthenpayController->request->post['payment_cofidis_order_status_not_approved_id'];
            } else {
                $this->data['payment_cofidis_order_status_not_approved_id'] = $this->ifthenpayController->config->get('payment_cofidis_order_status_not_approved_id');
            }

			if (isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_payment_method_title'])) {
				$this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $this->ifthenpayController
					->request->post['payment_' . $this->paymentMethod . '_payment_method_title'];
			} else {
				$paymentMethodTitleFromConfig = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_payment_method_title');

				$this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $paymentMethodTitleFromConfig != '' ? $paymentMethodTitleFromConfig : $this->paymentMethodDefaultTitle;
			}

			$this->ifthenpayController->load->model('localisation/order_status');
			$this->data['order_statuses'] = $this->ifthenpayController->model_localisation_order_status->getOrderStatuses();


			if (isset($this->data['payment_cofidis_cofidisKey'])) {
				$this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
				$this->gatewayDataBuilder->setSubEntidade($this->data['payment_cofidis_cofidisKey']);
			}


			// if there is no max min set use the values from ifthenpay
			$max = $this->data['payment_cofidis_maximum_value'] ?? '';
			$min = $this->data['payment_cofidis_minimum_value'] ?? '';
			$cofidisKey = $this->data['payment_cofidis_cofidisKey'] ?? '';
			if ($max === '' && $min === '' && $cofidisKey != '') {
				$maxMinArray = $this->ifthenpayGateway->getCofidisMinMax($cofidisKey);

				$this->data['payment_cofidis_maximum_value'] = $maxMinArray['max'];
				$this->data['payment_cofidis_minimum_value'] = $maxMinArray['min'];
			}

		} else {
			parent::setGatewayBuilderData();
		}

	}


	public function deleteConfigValues(): void
	{
		$this->ifthenpayController->load->model('extension/payment/cofidis');
		$this->ifthenpayController->model_extension_payment_cofidis->deleteSettingByKey('payment_cofidis_cofidisKey');
	}
}
