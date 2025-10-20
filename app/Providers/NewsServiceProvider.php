<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\News\NewsAggregator;
use App\Services\News\Sources\NewsApiSource;
use App\Services\News\Sources\GuardianSource;
use App\Services\News\Sources\NewYorkTimesSource;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NewsAggregator::class, function () {
            $aggregator = new NewsAggregator();

            $aggregator->addSource(new NewYorkTimesSource(env('NYTIMES_API_KEY')));
            $aggregator->addSource(new NewsApiSource(env('NEWSAPI_API_KEY')));
            $aggregator->addSource(new GuardianSource(env('GUARDIAN_API_KEY')));

            return $aggregator;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
