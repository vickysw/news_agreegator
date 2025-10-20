<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\News\NewsAggregator;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregator $newsAggregator): int
    {
         $this->info('Fetching news...');
        $newsAggregator->fetchNews();
        $this->info('News fetched command execution has completed!');

        return 0;
    }
}
