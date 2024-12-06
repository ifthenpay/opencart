<?php

declare(strict_types=1);

namespace Ifthenpay\Callback;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Utility\Versions;
use IfthenpayController;

use Ifthenpay\Callback\CallbackVars as Cb;

class Callback
{

	private $activateEndpoint = 'https://ifthenpay.com/api/endpoint/callback/activation';
	private $webService;
	private $urlCallback;
	private $chaveAntiPhishing;
	private $backofficeKey;
	private $entidade;
	private $subEntidade;
	private $activatedFor = false;
	protected $ifthenpayContainer;



	// TODO Callback URL partial string, not an actual todo, just for ease of access
	private $urlCallbackParameters = [
		Gateway::MULTIBANCO => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::ENTITY .'=[ENTITY]&'. Cb::REFERENCE .'=[REFERENCE]&'. Cb::AMOUNT .'=[AMOUNT]&'. Cb::ORDER_ID .'=[ID]&'. Cb::PM .'=[PAYMENT_METHOD]',
		Gateway::MBWAY => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::TRANSACTION_ID .'=[REQUEST_ID]&'. Cb::AMOUNT .'=[AMOUNT]&'. Cb::ORDER_ID .'=[ID]&'. Cb::PM .'=[PAYMENT_METHOD]',
		Gateway::PAYSHOP => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::TRANSACTION_ID .'=[REQUEST_ID]&'. Cb::REFERENCE .'=[REFERENCE]&'. Cb::AMOUNT.'=[AMOUNT]&'. Cb::ORDER_ID .'=[ID]&'. Cb::PM .'=[PAYMENT_METHOD]',
		Gateway::CCARD => '&type=offline&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::TRANSACTION_ID .'=[REQUEST_ID]&'. Cb::ORDER_ID .'=[ID]&'. Cb::AMOUNT .'=[AMOUNT]',
		Gateway::COFIDIS => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::TRANSACTION_ID.'=[REQUEST_ID]&'. Cb::AMOUNT .'=[AMOUNT]&'. Cb::ORDER_ID .'=[ID]&'. Cb::PM .'=[PAYMENT_METHOD]',
		Gateway::PIX => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::TRANSACTION_ID.'=[REQUEST_ID]&'. Cb::AMOUNT .'=[AMOUNT]&'. Cb::ORDER_ID .'=[ID]&'. Cb::PM .'=[PAYMENT_METHOD]',
		Gateway::IFTHENPAYGATEWAY => '&type=offline&'. Cb::ECOMMERCE_VERSION .'={ec}&'. Cb::MODULE_VERSION.'={mv}&'. Cb::PAYMENT.'={paymentMethod}&'. Cb::ANTIPHISH_KEY .'=[ANTI_PHISHING_KEY]&'. Cb::ORDER_ID .'=[ID]&'. Cb::ENTITY .'=[ENTITY]&'. Cb::REFERENCE .'=[REFERENCE]&'. Cb::TRANSACTION_ID .'=[REQUEST_ID]&'. Cb::AMOUNT .'=[AMOUNT]&'. Cb::PM .'=[PAYMENT_METHOD]'

	];


	public function __construct(GatewayDataBuilder $data, WebService $webService)
	{
		$this->webService = $webService;
		$this->backofficeKey = $data->getData()->backofficeKey;
		$this->entidade = $data->getData()->entidade;
		$this->subEntidade = $data->getData()->subEntidade;
		$this->ifthenpayContainer = new IfthenpayContainer();
	}

	private function createAntiPhishing(): void
	{
		$this->chaveAntiPhishing = md5((string) rand());
	}

	private function createUrlCallback(string $paymentType, string $moduleLink): void
	{
		$callbackParamStr = str_replace('{paymentMethod}', $paymentType, $this->urlCallbackParameters[$paymentType]);
		$callbackParamStr = Versions::replaceStringWithVersions($callbackParamStr);

		$this->urlCallback = $moduleLink . $callbackParamStr;
	}

	private function activateCallback(): void
	{
		$payload = [
			'chave' => $this->backofficeKey,
			'entidade' => $this->entidade,
			'subentidade' => $this->subEntidade,
			'apKey' => $this->chaveAntiPhishing,
			'urlCb' => $this->urlCallback,
		];

		$request = $this->webService->postRequest(
			$this->activateEndpoint,
			$payload,
			true
		);

		$response = $request->getResponse();
		if (!$response->getStatusCode() === 200 && !$response->getReasonPhrase()) {
			throw new \Exception("Error Activating Callback");
		}
		$this->activatedFor = true;
	}

	public function make(string $paymentType, string $moduleLink, bool $activateCallback = false, IfthenpayController $ifthenpayController): void
	{

		if ($paymentType === Gateway::IFTHENPAYGATEWAY) {

			$this->handleCallbackActivationForIfthenpaygateway($moduleLink, $ifthenpayController);
			return;
		}

		$this->createAntiPhishing();
		$this->createUrlCallback($paymentType, $moduleLink);
		if ($activateCallback) {
			$this->activateCallback();
		}
	}


	private function handleCallbackActivationForIfthenpaygateway(string $moduleLink, IfthenpayController $ifthenpayController)
	{
		$paymentMethods = $ifthenpayController->request->post['payment_' . Gateway::IFTHENPAYGATEWAY . '_methods'] ?? [];
		$storedPaymentMethods = $ifthenpayController->config->get('payment_' . Gateway::IFTHENPAYGATEWAY . '_methods') ?? [];


		$paymentMethodsToActivate = [];

		if (empty($storedPaymentMethods)) {
			$paymentMethodsToActivate = array_filter($paymentMethods, function($item){
				return $item['is_active'] === '1';
			});
		} else {
			foreach ($paymentMethods as $key => $paymentMethod) {

				if (
					(isset($storedPaymentMethods[$key]) && $storedPaymentMethods[$key]['is_active'] === '0' && $paymentMethod['is_active'] === '1') ||
					(!isset($storedPaymentMethods[$key]) && $paymentMethod['is_active'] === '1')
				) {
					$paymentMethodsToActivate[$key] = $paymentMethod;
				}
			}
		}

		$isActivating = isset($ifthenpayController->request->post['payment_' . Gateway::IFTHENPAYGATEWAY . '_activateCallback']) &&
			$ifthenpayController->request->post['payment_' . Gateway::IFTHENPAYGATEWAY . '_activateCallback'] === '1' ? true : false;

		$isSandboxMode = isset($ifthenpayController->request->post['payment_' . Gateway::IFTHENPAYGATEWAY . '_sandboxMode']) &&
			$ifthenpayController->request->post['payment_' . Gateway::IFTHENPAYGATEWAY . '_sandboxMode'] === '1' ? true : false;


		$activateCallback = $isActivating && !$isSandboxMode;



		if (!empty($paymentMethodsToActivate) && $activateCallback) {

			$phishKey = $ifthenpayController->config->get('payment_' . Gateway::IFTHENPAYGATEWAY . '_chaveAntiPhishing') ?? null;
			$this->chaveAntiPhishing = $phishKey ?? md5((string) rand());

			$this->createUrlCallback(Gateway::IFTHENPAYGATEWAY, $moduleLink);

			foreach ($paymentMethodsToActivate as $key => $values) {

				$paymentMethodEntitySubentity = explode('|', $values['account']);
				$paymentMethodEntity = trim($paymentMethodEntitySubentity[0]);
				$paymentMethodSubentity = trim($paymentMethodEntitySubentity[1]);

				$this->entidade = $paymentMethodEntity;
				$this->subEntidade = $paymentMethodSubentity;

				$this->activateCallback();
			}
			$this->activatedFor = true;
		}

		return;
	}

	/**
	 * Get the value of urlCallback
	 */
	public function getUrlCallback(): string
	{
		return $this->urlCallback;
	}

	/**
	 * Get the value of chaveAntiPhishing
	 */
	public function getChaveAntiPhishing(): string
	{
		return $this->chaveAntiPhishing;
	}

	/**
	 * Get the value of activatedFor
	 */
	public function getActivatedFor()
	{
		return $this->activatedFor;
	}
}
