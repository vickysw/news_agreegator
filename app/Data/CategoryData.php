<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CategoryData extends Data
{
    public function __construct(
         readonly public ?int $id,
        readonly public string $name,
    ) {}
}
