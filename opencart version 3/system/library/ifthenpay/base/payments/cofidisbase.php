<?php

declare(strict_types=1);

namespace Ifthenpay\Base\Payments;

use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Status;
use Ifthenpay\Base\PaymentBase;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Utility\Mix;
use Ifthenpay\Utility\TokenExtra;

class CofidisBase extends PaymentBase
{
	protected $paymentMethod = Gateway::COFIDIS;
	private $token;

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
		$this->paymentMethodAlias = $this->ifthenpayController->language->get('cofidisAlias');
	}

	private function getUrlCallback(): string
	{
		return $this->paymentDefaultData->order['store_url'] .
			'index.php?route=extension/payment/cofidis/callback';
	}

	protected function saveToDatabase(): void
	{
		$this->ifthenpayController->load->model('extension/payment/cofidis');

		$this->ifthenpayController->model_extension_payment_cofidis->savePayment($this->paymentDefaultData, $this->paymentGatewayResultData);
	}

	protected function setGatewayBuilderData(): void
	{
		$customerData = $this->getCustomerData();

		$this->gatewayBuilder->setCustomerData($customerData);
		$this->gatewayBuilder->setCofidisKey($this->configData['payment_cofidis_cofidisKey']);

		$hash = TokenExtra::generateHashString(20);
		$this->gatewayBuilder->setHash($hash);

		$this->gatewayBuilder->setReturnUrl($this->getUrlCallback() . '&type=online&payment=cofidis&orderId=' . $this->paymentDefaultData->order['order_id'] . '&hash=' . $hash);



	}

	/**
	 * Generates an array with the customer data required to pass as payload for cofidis pay.
	 * @return array
	 */
	private function getCustomerData(): array
	{
		$customerData = [];

		if (isset($this->paymentDefaultData) && isset($this->paymentDefaultData->order)) {
			$orderInfo = $this->paymentDefaultData->order;

			$shippingFirstName = isset($orderInfo['shipping_firstname']) ? $orderInfo['shipping_firstname'] : '';
			$shippingLastName = isset($orderInfo['shipping_lastname']) ? $orderInfo['shipping_lastname'] : '';
			if ($shippingFirstName . $shippingLastName != '') {
				$customerData['customerName'] = trim($shippingFirstName . ' ' . $shippingLastName);
			}

			if (isset($orderInfo['email'])) {
				$customerData['customerEmail'] = $orderInfo['email'];
			}

			if (isset($orderInfo['telephone'])) {
				$customerData['customerPhone'] = $orderInfo['telephone'];
			}

			$shippingAddress1 = isset($orderInfo['shipping_address_1']) ? $orderInfo['shipping_address_1'] : '';
			$shippingAddress2 = isset($orderInfo['shipping_address_2']) ? $orderInfo['shipping_address_2'] : '';
			if ($shippingAddress1 . $shippingAddress2 != '') {
				$customerData['deliveryAddress'] = trim($shippingAddress1 . ' ' . $shippingAddress2);
			}
			if (isset($orderInfo['shipping_postcode'])) {
				$customerData['deliveryZipCode'] = $orderInfo['shipping_postcode'];
			}
			if (isset($orderInfo['shipping_city'])) {
				$customerData['deliveryCity'] = $orderInfo['shipping_city'];
			}

			if (isset($orderInfo['payment_address_1']) && isset($orderInfo['payment_address_2'])) {
				$customerData['billingAddress'] = trim($orderInfo['payment_address_1'] . " " . $orderInfo['payment_address_2']);
			}
			if (isset($orderInfo['payment_postcode'])) {
				$customerData['billingZipCode'] = $orderInfo['payment_postcode'];
			}
			if (isset($orderInfo['payment_city'])) {
				$customerData['billingCity'] = $orderInfo['payment_city'];
			}
		}

		return $customerData;

	}

	public function getFromDatabaseById(): void
	{
		$this->ifthenpayController->load->model('extension/payment/cofidis');

		$this->paymentDataFromDb = $this->ifthenpayController->model_extension_payment_cofidis
			->getPaymentByOrderId($this->paymentDefaultData->order['order_id'])
			->row;
	}
}
