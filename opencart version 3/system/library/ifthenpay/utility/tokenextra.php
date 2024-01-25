<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

class TokenExtra
{

	public function encript(string $input, string $secret): string
	{
		return hash_hmac('sha256', $input, $secret);
	}

	public static function generateHashString(int $length): string
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str = substr(str_shuffle($characters), 0, $length);
		return $str;
	}

}
