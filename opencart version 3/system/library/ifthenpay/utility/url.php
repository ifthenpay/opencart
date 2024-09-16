<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;



class Url
{
	public static function catalogUrl(string $isSecure): string
	{
		if ($isSecure == '1') {
			if (defined('HTTPS_CATALOG')) {
				$url = HTTPS_CATALOG . 'catalog/';
			} else {
				$url = HTTPS_SERVER . 'catalog/';
			}
		} else {
			if (defined('HTTP_CATALOG')) {
				$url = HTTP_CATALOG . 'catalog/';
			} else {
				$url = HTTP_SERVER . 'catalog/';
			}
		}
		return $url;
	}
}
