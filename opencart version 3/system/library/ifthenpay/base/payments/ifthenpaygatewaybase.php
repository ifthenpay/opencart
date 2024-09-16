<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Callback\CallbackVars;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Mix;
use Ifthenpay\Utility\Status;
use Ifthenpay\Utility\Time;
use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Versions;

class IfthenpaygatewayBase extends PaymentBase
{

	protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;
	private $token;
	private $status;

	public function __construct(
		DataBuilder $paymentDefaultData,
		GatewayDataBuilder $gatewayBuilder,
		Gateway $ifthenpayGateway,
		array $configData,
		$ifthenpayController,
		Mix $mix,
		TwigDataBuilder $twigDataBuilder = null,
		Token $token = null,
		Status $status = null
	) {
		parent::__construct($paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $configData, $ifthenpayController, $mix, $twigDataBuilder);
		$this->token = $token;
		$this->status = $status;
		$this->paymentMethodAlias = $this->ifthenpayController->language->get('text_ifthenpaygateway_Alias');
	}


	private function getUrlCallback(): string
	{
		return $this->paymentDefaultData->order['store_url'] .
			'index.php?route=extension/payment/ifthenpaygateway/callback';
	}



	protected function setGatewayBuilderData(): void
	{

		if ($this->paymentMethod === Gateway::IFTHENPAYGATEWAY) {

			$this->gatewayBuilder->setIthenpaygatewayKey($this->configData['payment_ifthenpaygateway_ifthenpaygatewayKey']);
			$language = $this->ifthenpayController->language->get('code') ?? 'pt';
			$this->gatewayBuilder->setLanguage($language);


			$daysToDeadline = $this->ifthenpayController->config->get('payment_ifthenpaygateway_deadline');
			$deadline = Time::dateAfterDays($daysToDeadline);
			$this->gatewayBuilder->setDeadline($deadline);

			$methods = $this->ifthenpayController->config->get('payment_ifthenpaygateway_methods');

			$methodsStr = '';
			foreach ($methods as $key => $value) {
				if ($value != null && $value['is_active'] === '1') {

					$methodsStr .= str_replace(' ', '',$value['account']) . ';';
				}
			}

			$this->gatewayBuilder->setAccounts($methodsStr);

			$this->gatewayBuilder->setSelectedMethod($this->configData['payment_ifthenpaygateway_default_method']);

			$btnCloseUrl = $this->ifthenpayController->url->link('checkout/success');

			$this->gatewayBuilder->setBtnCloseUrl($btnCloseUrl);
			$this->gatewayBuilder->setBtnCloseLabel($this->configData['payment_ifthenpaygateway_btn_close_text']);



			$versionStr = Versions::replaceStringWithVersions('&ec={ec}&mv={mv}');

			$this->gatewayBuilder->setSuccessUrl(
				$this->getUrlCallback() . $versionStr . '&type=online&p=ifthenpaygateway&'. CallbackVars::ORDER_ID .'=' . $this->paymentDefaultData->order['order_id'] . '&qn=' .
					$this->token->encrypt($this->status->getStatusSucess())
			);
			$this->gatewayBuilder->setErrorUrl(
				$this->getUrlCallback() . '&type=online&p=ifthenpaygateway&'. CallbackVars::ORDER_ID .'=' . $this->paymentDefaultData->order['order_id'] . '&qn=' .
					$this->token->encrypt($this->status->getStatusError())
			);
			$this->gatewayBuilder->setCancelUrl(
				$this->getUrlCallback() . '&type=online&p=ifthenpaygateway&'. CallbackVars::ORDER_ID .'=' . $this->paymentDefaultData->order['order_id'] . '&qn=' .
					$this->token->encrypt($this->status->getStatusCancel())
			);
		} else {



			$this->gatewayBuilder->setEntidade($this->configData['payment_ifthenpaygateway_entidade']);
			$this->gatewayBuilder->setSubEntidade($this->configData['payment_ifthenpaygateway_subEntidade']);
			if (isset($this->configData['payment_ifthenpaygateway_deadline']) && $this->configData['payment_ifthenpaygateway_deadline'] != '') {
				$this->gatewayBuilder->setValidade($this->configData['payment_ifthenpaygateway_deadline']);
			}
		}
	}

	protected function saveToDatabase(): void
	{
		$this->ifthenpayController->load->model('extension/payment/ifthenpaygateway');
		$ifthenpaygatewayPayment = $this->ifthenpayController
			->model_extension_payment_ifthenpaygateway->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])->row;
		if (empty($ifthenpaygatewayPayment)) {
			$this->ifthenpayController->model_extension_payment_ifthenpaygateway
				->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
		} else {
			$this->ifthenpayController->model_extension_payment_ifthenpaygateway
				->updatePendingIfthenpaygateway($ifthenpaygatewayPayment['id_ifthenpay_ifthenpaygateway'], $this->paymentDefaultData, $this->paymentGatewayResultData);
		}
	}

	public function getFromDatabaseById(): void
	{
		$this->ifthenpayController->load->model('extension/payment/ifthenpaygateway');

		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_ifthenpaygateway
			->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
			->row;
	}
}
