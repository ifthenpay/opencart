<?php

declare(strict_types=1);

namespace Ifthenpay\Exceptions;

/**
 * Custom type of exception only to be able to filter out validation errors
 */
class Checkoutformvalexception extends \Exception {}
