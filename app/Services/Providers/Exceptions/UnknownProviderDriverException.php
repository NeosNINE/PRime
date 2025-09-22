<?php

namespace App\Services\Providers\Exceptions;

use InvalidArgumentException;

class UnknownProviderDriverException extends InvalidArgumentException
{
    public static function make(string $driver): self
    {
        return new self("Provider driver [{$driver}] is not supported.");
    }
}
