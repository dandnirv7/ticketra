<?php

namespace App\Services;

class BulkJadwalConflictException extends \RuntimeException
{
    
    public function __construct(
        string $message,
        public readonly array $conflicts,
    ) {
        parent::__construct($message);
    }
}
