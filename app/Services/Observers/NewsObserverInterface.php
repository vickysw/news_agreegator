<?php

namespace App\Services\Observers;

use Illuminate\Support\Collection;

interface NewsObserverInterface
{
    public function onNewsUpdated(Collection $articles): void;
}
