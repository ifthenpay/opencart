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


	public static function convertDateFormat(string $dateString, string $fromFormat, string $toFormat){
		// Convert the string to a DateTime object
		$date = \DateTime::createFromFormat($fromFormat, $dateString);

		// Check if the conversion was successful
		if ($date === false) {
			return false; // Invalid date format
		}

		return $date->format($toFormat);
	}
}
