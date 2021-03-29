<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Callback;

interface CallbackDataInterface
{
    public function getData(array $request, $ifthenpayController): array;
}
