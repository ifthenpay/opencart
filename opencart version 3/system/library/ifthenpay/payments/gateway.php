<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use ifthenpay\Builders\DataBuilder;
use ifthenpay\Factory\Payment\PaymentFactory;
use ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Request\WebService;
use Ifthenpay\Payments\Multibanco;
use Ifthenpay\Utility\Mix;


class Gateway
{
	const MULTIBANCO = 'multibanco';
	const MBWAY = 'mbway';
	const PAYSHOP = 'payshop';
	const CCARD = 'ccard';
	const COFIDIS = 'cofidis';
	const PIX = 'pix';
	const IFTHENPAYGATEWAY = 'ifthenpaygateway';

	const METHODS_WITH_CALLBACK = [self::MULTIBANCO, self::MBWAY, self::PAYSHOP, self::COFIDIS, self::PIX, self::IFTHENPAYGATEWAY];

	private $webService;
	private $paymentFactory;
	private $account;
	private $paymentMethods = [self::MULTIBANCO, self::MBWAY, self::PAYSHOP, self::CCARD, self::COFIDIS, self::PIX, self::IFTHENPAYGATEWAY];
	private $paymentMethodsCanCancel = [self::MULTIBANCO, self::MBWAY, self::CCARD, self::PAYSHOP, self::COFIDIS, self::PIX, self::IFTHENPAYGATEWAY];
	private $paymentMethodsCanOrderBackend = [self::MULTIBANCO, self::MBWAY, self::PAYSHOP];

	public function __construct(WebService $webService, PaymentFactory $paymentFactory)
	{
		$this->webService = $webService;
		$this->paymentFactory = $paymentFactory;
	}

	public function getPaymentMethodsType(): array
	{
		return $this->paymentMethods;
	}

	public function getPaymentMethodsCanCancel(): array
	{
		return $this->paymentMethodsCanCancel;
	}

	public function checkIfthenpayPaymentMethod(string $paymentMethod): bool
	{
		if (in_array($paymentMethod, $this->paymentMethods)) {
			return true;
		}
		return false;
	}

	public function authenticate(string $backofficeKey, string $paymentMethod): void
	{
		if ($paymentMethod === self::IFTHENPAYGATEWAY) {

			$gatewayKeys = $this->webService->getRequest(
				'https://ifthenpay.com/IfmbWS/ifthenpaymobile.asmx/GetGatewayKeys',
				[
					'backofficekey' => $backofficeKey,
				]
			)->getResponseJson();

			if (empty($gatewayKeys)) {

				$authenticate = $this->webService->postRequest(
					'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/' .
						'getEntidadeSubentidadeJsonV2',
					[
						'chavebackoffice' => $backofficeKey,
					]
				)->getResponseJson();

				if (!$authenticate[0]['Entidade'] && empty($authenticate[0]['SubEntidade'])) {
					throw new \Exception('Backoffice key is invalid');
				} else {
					$this->account = [];
				}
			} else {
				$this->account = $gatewayKeys;
			}

			return;
		}



		$authenticate = $this->webService->postRequest(
			'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/' .
				'getEntidadeSubentidadeJsonV2',
			[
				'chavebackoffice' => $backofficeKey,
			]
		)->getResponseJson();

		if (!$authenticate[0]['Entidade'] && empty($authenticate[0]['SubEntidade'])) {
			throw new \Exception('Backoffice key is invalid');
		} else {
			$this->account = $authenticate;
		}
	}

	public function getAccount(): array
	{
		return $this->account;
	}

	public function setAccount(array $account)
	{
		$this->account = $account;
	}

	public function getPaymentMethods($paymentMethod = ''): array
	{
		$userPaymentMethods = [];

		if ($paymentMethod === self::IFTHENPAYGATEWAY) {
			foreach ($this->account as $account) {

				if (isset($account['GatewayKey'])) {
					$userPaymentMethods[] = [
						'gatewayKey' => $account['GatewayKey'],
						'alias' => $account['Alias'],
						'type' => $account['Tipo']
					];
				}
			}
			return $userPaymentMethods;
		}



		foreach ($this->account as $account) {
			if (in_array(strtolower($account['Entidade']), $this->paymentMethods)) {
				$userPaymentMethods[] = strtolower($account['Entidade']);

				// additional verification required to add the dynamic reference to multibanco payment method
			} elseif (is_numeric($account['Entidade']) || $account['Entidade'] === "MB" || $account['Entidade'] === "mb") {
				$userPaymentMethods[] = $this->paymentMethods[0];
			}
		}
		return array_unique($userPaymentMethods);
	}

	public function getSubEntidadeInEntidade(string $entidade): array
	{
		return array_filter(
			$this->account,
			function ($value) use ($entidade) {
				return $value['Entidade'] === $entidade;
			}
		);
	}



	public function getEntidadeSubEntidade(string $paymentMethod): array
	{
		$list = null;
		if ($paymentMethod === self::MULTIBANCO) {
			$list = array_filter(
				array_column($this->account, 'Entidade'),
				function ($value) {
					return is_numeric($value) || $value === Multibanco::DYNAMIC_MB_ENTIDADE;
				}
			);
		} else {
			$list = [];
			foreach (array_column($this->account, 'SubEntidade', 'Entidade') as $key => $value) {
				if ($key === strtoupper($paymentMethod)) {
					$list[] = $value;
				}
			}
		}
		return $list;
	}

	public function checkDynamicMb(array $userAccount): bool
	{
		$multibancoDynamicKey = array_filter(
			array_column($userAccount, 'Entidade'),
			function ($value) {
				return $value === Multibanco::DYNAMIC_MB_ENTIDADE;
			}
		);
		if ($multibancoDynamicKey) {
			return true;
		}
		return false;
	}



	public function getPaymentLogoUrl(string $paymentMethod, string $catalogUrl): string
	{
		return $catalogUrl . 'view/theme/default/image/ifthenpay/' . $paymentMethod . '_ck.png';
	}

	public function execute(string $paymentMethod, GatewayDataBuilder $data, string $orderId, string $valor): DataBuilder
	{
		$paymentMethod = $this->paymentFactory
			->setType($paymentMethod)
			->setData($data)
			->setOrderId($orderId)
			->setValor($valor)
			->build();
		return $paymentMethod->buy();
	}

	/**
	 * Get the value of paymentMethodsCanOrderBackend
	 */
	public function getPaymentMethodsCanOrderBackend()
	{
		return $this->paymentMethodsCanOrderBackend;
	}

	/**
	 * Used exclusively for cofidis in order to get the min max values from ifthenpay's server
	 *
	 */
	public function getCofidisMinMax($key): array
	{
		$min = '';
		$max = '';
		$responseArray = $this->webService->getRequest('https://ifthenpay.com/api/cofidis/limits/' . $key)->getResponseJson();

		if (isset($responseArray['message']) && $responseArray['message'] == 'success') {

			$min = $responseArray['limits']['minAmount'];
			$max = $responseArray['limits']['maxAmount'];
		}

		return ['max' => $max, 'min' => $min];
	}


	public function getIfthenpayGatewayAccounts(): array
	{
		return $this->account;
	}


	public function getIfthenpayGatewayPaymentMethodsDataByBackofficeKeyAndGatewayKey($backofficeKey, $gatewayKey): array
	{

		$methods = $this->webService->getRequest(
			'https://api.ifthenpay.com/gateway/methods/available',
			[]
		)->getResponseJson();

		if (empty($methods)) {
			return [];
		}

		$accounts = $this->webService->getRequest(
			'https://ifthenpay.com/IfmbWS/ifthenpaymobile.asmx/GetAccountsByGatewayKey',
			[
				'backofficekey' => $backofficeKey,
				'gatewayKey' => $gatewayKey
			]
		)->getResponseJson();

		if (empty($accounts)) {
			return [];
		}


		foreach ($methods as &$method) {

			$methodCode = $method['Entity'];
			$filteredAccounts = array_filter($accounts, function ($item) use ($methodCode) {
				return $item['Entidade'] === $methodCode || ($methodCode === 'MB' && is_numeric($item['Entidade']));
			});

			$method['accounts'] = $filteredAccounts;
		}
		unset($method);

		return $methods;
	}
}
