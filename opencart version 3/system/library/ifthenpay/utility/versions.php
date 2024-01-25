<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Config\IfthenpayUpgrade;


class Versions
{
	public static function replaceStringWithVersions(string $str): string
	{
		$container = new IfthenpayContainer();

		$opencartVersion = VERSION;
		$str = str_replace('{ec}', 'op_' . $opencartVersion, $str);

		$moduleVersion = $container->getIoc()->make(IfthenpayUpgrade::class)->getModuleVersion();
		$str = str_replace('{mv}', $moduleVersion, $str);

		return $str;
	}
}
