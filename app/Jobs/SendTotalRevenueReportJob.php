<?php

namespace App\Jobs;

use App\Services\RevenueManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class SendTotalRevenueReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 50;
    public string $jobId;
    private const EXPIRATION = 24 * 60 * 60; // 24 hours in seconds

    public function __construct()
    {
        $this->jobId = Str::uuid()->toString();
    }

    public function handle(): void
    {

        $steps = ['verification', 'report', 'confirmation'];
        foreach ($steps as $step) {
            if (!$this->isStepCompleted($step)) {
                try {
                    Log::info("Starting {$step} step for job {$this->jobId}");
                    $response = $this->{"post" . ucfirst($step)}();
                    $this->markStepCompleted($step, $response);
                    Log::info("Completed {$step} step for job {$this->jobId}");
                } catch (RequestException $e) {
                    $this->handleRequestException($e, $step);
                    return;
                } catch (\Exception $e) {
                    $this->handleGenericException($e, $step);
                    return;
                }
            }
        }

        $this->clearJobData();
        Log::info("Successfully completed all steps for job {$this->jobId}");
    }

    private function postVerification(): array
    {
        return Http::withHeaders(['Idempotency-Key' => $this->jobId])
            ->post('https://revenue-verifier.com')
            ->throw()
            ->json();
    }

    private function postReport(): array
    {
        $verificationResponse = json_decode($this->getRedisValue("{$this->jobId}_verification_response"), true);

        return Http::withHeaders(['Idempotency-Key' => $this->jobId])
            ->post('https://revenue-reporting.com/reports', [
                'verification_id' => $verificationResponse['id'],
                'total_revenue' => RevenueManager::calculateTotalRevenue(),
            ])->throw()->json();
    }

    private function postConfirmation(): array
    {
        $reportResponse = json_decode($this->getRedisValue("{$this->jobId}_report_response"), true);

        return Http::withHeaders(['Idempotency-Key' => $this->jobId])
            ->post('https://revenue-reporting.com/reports/confirm', [
                'report_id' => $reportResponse['id'],
                'timestamp' => now()->timestamp,
            ])->throw()->json();
    }

    private function isStepCompleted(string $step): bool
    {
        return $this->getRedisValue("{$this->jobId}_{$step}_completed") === 'true';
    }

    private function markStepCompleted(string $step, array $response): void
    {
        $this->setRedisValue("{$this->jobId}_{$step}_completed", 'true', self::EXPIRATION);
        $this->setRedisValue("{$this->jobId}_{$step}_response", json_encode($response), self::EXPIRATION);
    }

    private function handleRequestException(RequestException $e, string $step): void
    {
        Log::error("SendTotalRevenueReportJob failed at {$step} step: " . $e->getMessage(), ['job_id' => $this->jobId]);
        $delay = $this->calculateBackoff();
        $this->release($delay);
    }

    private function handleGenericException(\Exception $e, string $step): void
    {
        Log::error("Unexpected error in SendTotalRevenueReportJob at {$step} step: " . $e->getMessage(), ['job_id' => $this->jobId]);
        $this->fail($e);
    }

    private function clearJobData(): void
    {
        Redis::command('del', [
            "{$this->jobId}_verification_completed",
            "{$this->jobId}_verification_response",
            "{$this->jobId}_report_completed",
            "{$this->jobId}_report_response",
            "{$this->jobId}_confirmation_completed",
            "{$this->jobId}_confirmation_response"
        ]);
    }

    private function calculateBackoff(): int
    {
        return min(pow(2, $this->attempts()) * 30, 3600); // Exponential backoff, max 1 hour
    }

    private function getRedisValue(string $key): ?string
    {
        return Redis::connection()->get($key);
    }

    private function setRedisValue(string $key, string $value, int $expiration): void
    {
        Redis::connection()->set($key, $value, 'EX', $expiration);
    }
}
