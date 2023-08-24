<?php

namespace Ifthenpay;



class ApiService
{
	private const URL_GET_ACCOUNTS_BY_BACKOFFICE = 'https://www.ifthenpay.com/IfmbWS/ifmbws.asmx/getEntidadeSubentidadeJsonV2';
	private const URL_ACTIVATE_CALLBACK = 'https://ifthenpay.com/api/endpoint/callback/activation';
	private const URL_MBWAY_SET_REQUEST = 'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/SetPedidoJSON';
	private const URL_CCARD_SET_REQUEST = 'https://ifthenpay.com/api/creditcard/init/';
	private const URL_MULTIBANCO_DYNAMIC_SET_REQUEST = 'https://ifthenpay.com/api/multibanco/reference/init';
	private const URL_PAYSHOP_SET_REQUEST = 'https://ifthenpay.com/api/payshop/reference/';
	private const URL_IFTHENPAY_POST_REFUND = 'https://ifthenpay.com/api/endpoint/payments/refund';
	private const URL_IFTHENPAY_UPGRADE = 'https://ifthenpay.com/modulesUpgrade/opencart/4/upgrade.json';
	public const URL_MBWAY_GET_PAYMENT_STATUS = 'https://www.ifthenpay.com/mbwayws/ifthenpaymbw.asmx/EstadoPedidosJSON';

	private $curl;
	private $headers;



	public function __construct($headers = [])
	{
		$this->curl = curl_init();
		$this->headers = $headers;
	}



	/**
	 * sends a request to the API and returns the response
	 * if $isPayloadJson is true, the payload will converted to JSON using json_encode()
	 * @param string $method (POST, GET)
	 * @param string $url
	 * @param array $payload
	 * @param bool $isPayloadJson
	 * @return string|null
	 */
	public function sendRequest($method, $url, $payload, $isPayloadJson = true)
	{
		// Set the URL and other cURL options

		curl_setopt_array($this->curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTPHEADER => $this->headers
		]);

		// Set the request method
		if ($method === 'POST') {
			curl_setopt($this->curl, CURLOPT_POST, 1);

			if ($isPayloadJson) {
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($payload));
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array_merge($this->headers, ['Content-Type: application/json']));
			} else {
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
			}
		} else if ($method === 'GET') {
			curl_setopt($this->curl, CURLOPT_HTTPGET, 1);

			$argStr = $this->generateQueryString($payload);
			curl_setopt($this->curl, CURLOPT_URL, $url . $argStr);

		}

		// Execute the request and fetch the response
		$response = curl_exec($this->curl);

		// Check for cURL errors
		if (curl_errno($this->curl)) {
			$error = curl_error($this->curl);
			// Handle the error appropriately
		}

		// Close the cURL resource
		curl_close($this->curl);

		// Handle the API response
		if ($response !== false) {
			return $response;
		}

		return null;
	}



	public function __destruct()
	{
		curl_close($this->curl);
	}




	/* -------------------------------------------------------------------------- */
	/*                             predefined requests                            */
	/* -------------------------------------------------------------------------- */

	/**
	 * gets accounts by backoffice key
	 * expects a response in string format:
	 * @param string $backofficeKey
	 * @return string|null
	 */
	public function getAccounts($backofficeKey)
	{
		// this endpoint is a bit quirky, it requires the payload to be sent as a string key=value, not as an array
		$payload = 'chavebackoffice=' . $backofficeKey;

		return $this->sendRequest(
			'POST',
			self::URL_GET_ACCOUNTS_BY_BACKOFFICE,
			$payload,
			false
		);
	}



	/**
	 * activates the callback for a given backoffice key, entity and subentity
	 * expects a response in string format: "OK: Callback ativado..."
	 * @param string $key
	 * @param string $entity
	 * @param string $subEntity
	 * @param string $antiPhishingKey
	 * @param string $urlCallback
	 * @return string|null
	 */
	public function requestCallbackActivation($backofficeKey, $entity, $subEntity, $antiPhishingKey, $urlCallback)
	{
		$payload = [
			'chave' => $backofficeKey,
			'entidade' => $entity,
			'subentidade' => $subEntity,
			'apKey' => $antiPhishingKey,
			'urlCb' => $urlCallback
		];

		return $this->sendRequest(
			'POST',
			self::URL_ACTIVATE_CALLBACK,
			$payload
		);
	}



	/**
	 * POST request to get a Multibanco dynamic payment reference
	 * @param string $entity
	 * @param string $subEntity
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $deadlineDays
	 * @return string|null
	 */
	public function requestMultibancoReference($entity, $subEntity, $orderId, $orderTotal, $deadlineDays)
	{
		$payload = [
			'mbKey' => $subEntity,
			'orderId' => $orderId,
			"amount" => $orderTotal,
			"description" => 'opencart 4 request',
		];

		if ($deadlineDays != '') {
			$payload['expiryDays'] = $deadlineDays;
		}

		return $this->sendRequest(
			'POST',
			self::URL_MULTIBANCO_DYNAMIC_SET_REQUEST,
			$payload
		);
	}



	/**
	 * POST request to get a Payshop payment reference
	 * @param string $payshopKey
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $deadline
	 * @return string|null
	 */
	public function requestPayshopReference($payshopKey, $orderId, $orderTotal, $deadline)
	{
		$payload = [
			'payshopkey' => $payshopKey,
			'id' => $orderId,
			'valor' => $orderTotal,
			'validade' => $deadline
		];

		return $this->sendRequest(
			'POST',
			self::URL_PAYSHOP_SET_REQUEST,
			$payload
		);
	}



	/**
	 * POST request to get a MB WAY payment transaction (as a result, the user will receive a notification on his phone)
	 * @param string $mbwayKey
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $phoneNumber
	 * @return string|null
	 */
	public function requestMbwayTransaction($mbwayKey, $orderId, $orderTotal, $phoneNumber)
	{
		$payload = [
			'MbWayKey' => $mbwayKey,
			'canal' => '03',
			'referencia' => $orderId,
			'valor' => $orderTotal,
			'nrtlm' => $phoneNumber,
			'email' => '',
			'descricao' => ''
		];

		return $this->sendRequest(
			'POST',
			self::URL_MBWAY_SET_REQUEST,
			$payload
		);
	}



	/**
	 * GET request to get the payment status of a mbway transaction
	 * @param string $transactionId
	 * @param string $mbwayKey
	 * @return string|null
	 */
	public function requestCheckMbwayPaymentStatus($transactionId, $mbwayKey)
	{
		$payload = [
			'MbWayKey' => $mbwayKey,
			'canal' => '03',
			'idspagamento' => $transactionId
		];

		$result = $this->sendRequest(
			'GET',
			self::URL_MBWAY_GET_PAYMENT_STATUS,
			$payload
		);

		return $result;
	}



	/**
	 * POST request to get a CCard payment url
	 * @param string $ccardKey
	 * @param string $orderId
	 * @param string $orderTotal
	 * @param string $language
	 * @param string $successUrl
	 * @param string $cancelUrl
	 * @param string $errorUrl
	 * @return string|null
	 */
	public function requestCcardUrl($ccardKey, $orderId, $orderTotal, $language, $successUrl, $cancelUrl, $errorUrl)
	{
		$payload = [
			'orderId' => $orderId,
			"amount" => $orderTotal,
			"successUrl" => $successUrl,
			"cancelUrl" => $cancelUrl,
			"errorUrl" => $errorUrl,
			"language" => "pt"
		];

		return $this->sendRequest(
			'POST',
			self::URL_CCARD_SET_REQUEST . $ccardKey,
			$payload
		);
	}



	/**
	 * POST request to generate a refund for a given transaction
	 * @param string $backofficeKey
	 * @param string $transactionId
	 * @param string $amount
	 * @return string|null
	 */
	public function requestRefund($backofficeKey, $transactionId, $amount)
	{
		$payload = [
			'backofficekey' => $backofficeKey,
			'requestId' => $transactionId,
			'amount' => $amount
		];

		return $this->sendRequest(
			'POST',
			self::URL_IFTHENPAY_POST_REFUND,
			$payload
		);
	}



	/**
	 * GET request to test a callback url
	 * @param string $callbackUrl
	 * @return string|null
	 */
	public function requestTestCallback($callbackUrl)
	{
		return $this->sendRequest(
			'GET',
			$callbackUrl,
			[]
		);
	}



	/**
	 * GET request to check if there is a new version of the module
	 * this gets the content of a json file that has the latest version of the module
	 * @return array
	 */
	public function requestCheckModuleUpgrade(): array
	{
		$result = $this->sendRequest('GET', self::URL_IFTHENPAY_UPGRADE, []);

		return json_decode($result, true);
	}



	/**
	 * generates a string with the query string parameters for a get request
	 * @param array $params
	 * @return string
	 */
	private function generateQueryString($params)
	{
		$queryString = '';

		foreach ($params as $key => $value) {
			$queryString .= $key . '=' . $value . '&';
		}

		$queryString = rtrim($queryString, '&');

		return $queryString !== '' ? '?' . $queryString : '';
	}
}
