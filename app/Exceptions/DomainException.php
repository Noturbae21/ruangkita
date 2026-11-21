<?php

namespace App\Exceptions;

use RuntimeException;

class DomainException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 422,
        public readonly string $errorCode = 'BUSINESS_RULE_VIOLATION',
        public readonly array $details = []
    ) {
        parent::__construct($message, $statusCode);
    }
}
