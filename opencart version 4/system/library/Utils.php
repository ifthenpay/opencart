<?php

namespace Ifthenpay;

class Utils
{
	public const MODULE_INSTRUCTIONS_URL = 'https://github.com/ifthenpay/opencart/blob/main/README.md';
	public const IFTHENPAY_SUPPORT_URL = 'https://helpdesk.ifthenpay.com/pt-PT/support/home';
	public const REQUEST_IFTHENPAY_ACCOUNT_URL = 'https://www.ifthenpay.com/downloads/ifmb/contratomb.pdf';


	/**
	 * get the module version from install.json
	 * @return string
	 */
	public static function getModuleVersion(bool $addPrefixV = true): string
	{
		$version = '';

		$json = file_get_contents(DIR_EXTENSION . 'ifthenpay/install.json');
		if ($json) {
			$data = json_decode($json, true);

			if (isset($data['version'])) {

				if ($addPrefixV) {
					$version = 'v';
				}

				$version .= $data['version'];
			}
		}

		return $version;
	}

	/**
	 * Encripts a string using base64 and urlencode
	 */
	public static function encrypt(string $input): string
	{
		return urlencode(base64_encode($input));
	}

	/**
	 * Decrypts a string using base64 and urldecode
	 */
	public static function decrypt(string $input): string
	{
		return base64_decode(urldecode($input));
	}


	public static function generateString(int $length): string
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str = substr(str_shuffle($characters), 0, $length);
		return $str;
	}


	public static function generateTransactionId($orderId, $transactionToken): string
	{
		$token = $orderId . bin2hex($orderId  . $transactionToken);
		return substr($token, 0, 20);
	}



	/**
	 * get the flash message from session and return it as an associative array
	 * $sessionData references the session data in opencart $this->session->data
	 * [type => 'error' | 'success', text => 'message']
	 * @return array
	 */
	public static function getFlashMessageAssocArray(&$sessionData): array
	{
		$message = [];

		if (isset($sessionData['error'])) {
			$message['type'] = 'danger';
			$message['text'] = $sessionData['error'];

			unset($sessionData['error']);
		}

		if (isset($sessionData['success'])) {
			$message['type'] = 'success';
			$message['text'] = $sessionData['success'];

			unset($sessionData['success']);
		}

		return $message;
	}



	public static function dateAfterDays(string $numberOfDays): string
	{

		if ($numberOfDays === '') {
			return '';
		}

		$timezone = new \DateTimeZone('Europe/Lisbon');
		$dateTime = new \DateTime('now', $timezone);
		$dateTime->modify("+$numberOfDays days");

		return $dateTime->format('Ymd');
	}



	public static function timeStamp(string $format = 'Y-m-d H:i:s'): string
	{
		$timezone = new \DateTimeZone('Europe/Lisbon');
		$dateTime = new \DateTime('now', $timezone);

		return $dateTime->format($format);
	}


	public static function convertDateFormat(string $dateString, string $fromFormat, string $toFormat)
	{
		// Convert the string to a DateTime object
		$date = \DateTime::createFromFormat($fromFormat, $dateString);

		// Check if the conversion was successful
		if ($date === false) {
			return false; // Invalid date format
		}

		return $date->format($toFormat);
	}


	/**
	 * Generates CSS for displaying payment method icons (exclusive to ifthenpaygateway method) in the checkout page.
	 * This is the currently "good enough" approach to display the multiple payment method icons, since OpenCard does not provide a better option.
	 * Even though the function can display up to 8 icons, the optimal amount is 3, otherwise the icons will be clipped in the selection popup panel.
	 * @param array $paymentMethods (e.g., ['multibanco', 'mbway', 'payshop']).
	 * @return string CSS to be injected in the checkout page header.
	 */
	public static function getIfthenpaygatewayIconCssInjectionScript(array $paymentMethods): string
	{
		$imagesWidth = [
			'multibanco' => 50,
			'mbway' => 90,
			'payshop' => 50,
			'ccard' => 64,
			'cofidis' => 168,
			'google' => 108,
			'apple' => 104,
			'pix' => 100,
		];

		$offset = 0;
		$imgUrlArray = [];
		$backgroundPositionX = [];
		foreach ($paymentMethods as $method) {
			$imgUrlArray[] = 'url("extension/ifthenpay/catalog/view/image/compact/' . $method . '.png")';
			$backgroundPositionX[] = $offset . 'px';
			$offset += $imagesWidth[$method] ?? 0;
		}

		return '
		<style>
			[for="input-payment-method-ifthenpaygateway-ifthenpaygateway"] {
				display: inline-flex !important;
				align-items: center;
				justify-content: flex-start;
				font-size: 0 !important;
				cursor: pointer;
				vertical-align: middle;
				min-height: 40px;
			}

			[for="input-payment-method-ifthenpaygateway-ifthenpaygateway"]::before {
				content: "";
				display: block;
				height: 38px; 
				width: 400px; 
				background-image: ' . implode(',', $imgUrlArray) . ';
				background-size: contain;
				background-repeat: no-repeat;
				background-position-x: ' . implode(',', $backgroundPositionX) . ';
				background-position-y: center;
				margin-right: 0;
				margin-left: 8px;
			}
		</style>
		';
	}

	
	public static function getPaymentIconCssInjectionScript(string $paymentMethod): string
	{
		$imgUrl = 'extension/ifthenpay/catalog/view/image/' . $paymentMethod . '.png';
		
		return '
		<style>
			[for="input-payment-method-'. $paymentMethod .'-'. $paymentMethod .'"] {
				display: inline-flex !important;
				align-items: center;
				justify-content: flex-start;
				font-size: 0 !important;
				cursor: pointer;
				vertical-align: middle;
				min-height: 40px;
			}

			[for="input-payment-method-'. $paymentMethod .'-'. $paymentMethod .'"]::before {
				content: "";
				display: block;
				height: 38px; 
				width: 200px; 
				background-image: url("' . $imgUrl . '");
				background-size: contain;
				background-repeat: no-repeat;
				background-position: center left;
				margin-right: 0;
				margin-left: 8px;
			}
		</style>
		';
	}
}
