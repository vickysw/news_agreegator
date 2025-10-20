<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use App\Interfaces\NewsSourceInterface;
use App\Services\News\RetryPolicy;
use Closure;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

abstract class AbstractNewsSource implements NewsSourceInterface
{
    protected string $apiKey;

    protected string $baseUrl;


    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    abstract public function getName(): string;

    abstract public function mapCallBack(): Closure;

    protected function fetch(string $endpoint, array $params = []): array
    {
        try {
           
                $response = Http::get($this->baseUrl . $endpoint, $params);

                if ( ! $response->successful()) {
                    throw new RequestException($response);
                }

                return $response->json();
         
        } catch (Exception $e) {
            Log::error("Failed to fetch from {$this->getName()}", [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);

            return [];
        }
    }
}