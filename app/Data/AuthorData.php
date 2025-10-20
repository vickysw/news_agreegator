<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class AuthorData extends Data
{
    public function __construct(
        readonly public ?int $id,
        readonly public string $name,
    ) {}
}
