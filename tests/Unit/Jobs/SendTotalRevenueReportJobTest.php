<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendTotalRevenueReportJob;
use App\Services\RevenueManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class SendTotalRevenueReportJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Mock Redis
        Redis::shouldReceive('connection')->andReturnSelf();
        Redis::shouldReceive('get')
            ->withArgs(function ($key) {
                return strpos($key, 'completed') !== false;
            })
            ->andReturn(null);

        Redis::shouldReceive('get')
            ->withArgs(function ($key) {
                return strpos($key, '_verification_response') !== false;
            })
            ->andReturn(json_encode(['id' => 'verification_id']));

        Redis::shouldReceive('get')
            ->withArgs(function ($key) {
                return strpos($key, '_report_response') !== false;
            })
            ->andReturn(json_encode(['id' => 'report_id']));

        Redis::shouldReceive('set');
        Redis::shouldReceive('command')->andReturn(true);

        // Mock RevenueManager
        $this->mock(RevenueManager::class, function ($mock) {
            $mock->shouldReceive('calculateTotalRevenue')->andReturn(10010.0);
        });

        // Mock Logging
        Log::shouldReceive('info');
        Log::shouldReceive('error');
    }

    public function testHandleSuccessfulJob()
    {
        // Mock HTTP responses
        Http::fake([
            'https://revenue-verifier.com' => Http::response(['id' => 'verification_id'], 200),
            'https://revenue-reporting.com/reports' => Http::response(['id' => 'report_id'], 200),
            'https://revenue-reporting.com/reports/confirm' => Http::response(['status' => 'confirmed'], 200),
        ]);

        $job = new SendTotalRevenueReportJob();
        $job->handle();

        Http::assertSentCount(3);
        Http::assertSent(function ($request) {
            return $request->hasHeader('Idempotency-Key');
        });
    }

    public function testHandleRequestException()
    {
        // Mock HTTP responses to throw a RequestException
        Http::fake([
            'https://revenue-verifier.com' => Http::response(null, 500),
        ]);

        $job = new SendTotalRevenueReportJob();
        $job->handle();

        Http::assertSentCount(1);
        Log::shouldHaveReceived('error')->withArgs(function ($message, $context) {
            return str_contains($message, 'SendTotalRevenueReportJob failed at verification step');
        });
    }

}
