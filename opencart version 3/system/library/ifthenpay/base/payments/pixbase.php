<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Utility\Status;
use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Callback\CallbackVars;
use Ifthenpay\Utility\Mix;
use Ifthenpay\Utility\TokenExtra;

class PixBase extends PaymentBase
{
	protected $paymentMethod = Gateway::PIX;

	public function __construct(
		DataBuilder $paymentDefaultData,
		GatewayDataBuilder $gatewayBuilder,
		Gateway $ifthenpayGateway,
		array $configData,
		$ifthenpayController,
		Mix $mix,
		TwigDataBuilder $twigDataBuilder = null,
		Status $status = null
	) {
		parent::__construct($paymentDefaultData, $gatewayBuilder, $ifthenpayGateway, $configData, $ifthenpayController, $mix, $twigDataBuilder);
		$this->status = $status;
		$this->paymentMethodAlias = $this->ifthenpayController->language->get('pixAlias');
	}

	private function getUrlCallback(): string
	{
		return $this->paymentDefaultData->order['store_url'] .
			'index.php?route=extension/payment/pix/callback';
	}

	protected function saveToDatabase(): void
	{
		$this->ifthenpayController->load->model('extension/payment/pix');

		$this->ifthenpayController->model_extension_payment_pix->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
	}

	protected function setGatewayBuilderData(): void
	{
		$formData = [];
		if (isset($this->ifthenpayController->request->post['customerName']) && $this->ifthenpayController->request->post['customerName']) {
			$formData['customerName'] = $this->ifthenpayController->request->post['customerName'];
		}
		if (isset($this->ifthenpayController->request->post['customerCpf']) && $this->ifthenpayController->request->post['customerCpf']) {
			$formData['customerCpf'] = $this->ifthenpayController->request->post['customerCpf'];
		}
		if (isset($this->ifthenpayController->request->post['customerEmail']) && $this->ifthenpayController->request->post['customerEmail']) {
			$formData['customerEmail'] = $this->ifthenpayController->request->post['customerEmail'];
		}
		if (isset($this->ifthenpayController->request->post['customerPhone']) && $this->ifthenpayController->request->post['customerPhone']) {
			$formData['customerPhone'] = $this->ifthenpayController->request->post['customerPhone'];
		}
		if (isset($this->ifthenpayController->request->post['customerAddress']) && $this->ifthenpayController->request->post['customerAddress']) {
			$formData['customerAddress'] = $this->ifthenpayController->request->post['customerAddress'];
		}
		if (isset($this->ifthenpayController->request->post['customerStreetNumber']) && $this->ifthenpayController->request->post['customerStreetNumber']) {
			$formData['customerStreetNumber'] = $this->ifthenpayController->request->post['customerStreetNumber'];
		}
		if (isset($this->ifthenpayController->request->post['customerCity']) && $this->ifthenpayController->request->post['customerCity']) {
			$formData['customerCity'] = $this->ifthenpayController->request->post['customerCity'];
		}
		if (isset($this->ifthenpayController->request->post['customerZipCode']) && $this->ifthenpayController->request->post['customerZipCode']) {
			$formData['customerZipCode'] = $this->ifthenpayController->request->post['customerZipCode'];
		}
		if (isset($this->ifthenpayController->request->post['customerState']) && $this->ifthenpayController->request->post['customerState']) {
			$formData['customerState'] = $this->ifthenpayController->request->post['customerState'];
		}

		$this->validatePixFormData($formData);

		$this->gatewayBuilder->setPixFormData($formData);
		$this->gatewayBuilder->setPixKey($this->configData['payment_pix_pixKey']);

		$hash = TokenExtra::generateHashString(20);
		$this->gatewayBuilder->setHash($hash);

		$this->gatewayBuilder->setReturnUrl($this->getUrlCallback() . '&type=online&p=pix&' . CallbackVars::ORDER_ID . '=' . $this->paymentDefaultData->order['order_id'] . '&hash=' . $hash);
	}


	/**
	 * throws exception if not valid
	 */
	private function validatePixformData(array $formData): void
	{
		// name
		if (!isset($formData['customerName']) || $formData['customerName'] == '') {
			throw new \Exception("Pix Name field is required.", 1);
		} else if (strlen($formData['customerName']) > 150) {
			throw new \Exception("Pix Name field is invalid. Must not exceed 150 characters.", 1);
		}
		// CPF
		if (!isset($formData['customerCpf']) || $formData['customerCpf'] == '') {
			throw new \Exception("Pix CPF field is required.", 1);
		} else if (!preg_match("/^(\d{3}\.\d{3}\.\d{3}-\d{2}|\d{11})$/", $formData['customerCpf'])) {
			throw new \Exception("Pix CPF field is invalid. Must be comprised of 11 digits with either of the following patterns: 111.111.111-11 or 11111111111", 1);
		}
		// email
		if (!isset($formData['customerEmail']) || $formData['customerEmail'] == '') {
			throw new \Exception("Pix Email field is required.", 1);
		} else if (!filter_var($formData['customerEmail'], FILTER_VALIDATE_EMAIL)) {
			throw new \Exception("Pix Email field is invalid. Must be a valid email address.", 1);
		} else if (strlen($formData['customerEmail']) > 250) {
			throw new \Exception("Pix Email field is invalid. Must not exceed 250 characters.", 1);
		}
		// phone
		if (isset($formData['customerPhone']) && $formData['customerPhone'] != '' && strlen($formData['customerPhone']) > 20) {
			throw new \Exception("Pix Phone field is invalid. Must not exceed 20 characters.", 1);
		}
		// address
		if (isset($formData['customerAddress']) && $formData['customerAddress'] != '' && strlen($formData['customerAddress']) > 250) {
			throw new \Exception("Pix Address field is invalid. Must not exceed 250 characters.", 1);
		}
		// streetNumber
		if (isset($formData['customerStreetNumber']) && $formData['customerStreetNumber'] != '' && strlen($formData['customerStreetNumber']) > 20) {
			throw new \Exception("Pix Street Number field is invalid. Must not exceed 20 characters.", 1);
		}
		// City
		if (isset($formData['customerCity']) && $formData['customerCity'] != '' && strlen($formData['customerCity']) > 50) {
			throw new \Exception("Pix City field is invalid. Must not exceed 50 characters.", 1);
		}
		// Zip Code
		if (isset($formData['customerZipCode']) && $formData['customerZipCode'] != '' && strlen($formData['customerZipCode']) > 20) {
			throw new \Exception("Pix Zip Code field is invalid. Must not exceed 20 characters.", 1);
		}
		// State
		if (isset($formData['customerState']) && $formData['customerState'] != '' && strlen($formData['customerState']) > 50) {
			throw new \Exception("Pix State field is invalid. Must not exceed 50 characters.", 1);
		}
	}


	public function getFromDatabaseById(): void
	{
		$this->ifthenpayController->load->model('extension/payment/pix');

		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_pix
			->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
			->row;
	}
}
