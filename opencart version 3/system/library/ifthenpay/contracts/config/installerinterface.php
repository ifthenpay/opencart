<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Config;

interface InstallerInterface
{
    public function install(): void;
    public function uninstall(): void;
}
