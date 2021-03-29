<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Callback;

interface CallbackProcessInterface
{
    public function process(): void;
}
