<?php

declare(strict_types=1);

namespace Ifthenpay\Traits\Payments;

trait FormatReference
{
    protected function formatReference(string $reference): string
    {
        return trim(strrev(chunk_split(strrev($reference),3, ' ')));
    }
}





