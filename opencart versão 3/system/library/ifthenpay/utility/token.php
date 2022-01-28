<?php

namespace Ifthenpay\Utility;

class Token {

    public function encrypt(string $input): string {
        return urlencode(base64_encode( $input));
    }

    public function decrypt(string $input): string {
        return base64_decode(urldecode($input));
    }
}