<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

class TokenExtra {

    public function encript(string $input, string $secret): string 
    {
        return hash_hmac('sha256', $input, $secret);
    }
}
