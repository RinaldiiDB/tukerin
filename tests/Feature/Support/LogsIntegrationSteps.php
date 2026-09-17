<?php

namespace Tests\Feature\Support;

use Illuminate\Support\Facades\Log;

/**
 * Mencatat jejak langkah integration testing ke channel `integration`
 * (storage/logs/integration-testing.log) untuk bahan laporan.
 *
 * Konvensi: step($id, $stage, $message, $context)
 * - $id: identifier skenario lowercase, mis. 'usr-03'
 * - $stage: SETUP | ACT | ASSERT | RESULT
 */
trait LogsIntegrationSteps
{
    protected function step(string $id, string $stage, string $message, array $context = []): void
    {
        if (! IntegrationLog::$started) {
            file_put_contents(storage_path('logs/integration-testing.log'), '');
            IntegrationLog::$started = true;
        }

        Log::channel('integration')->info("[{$id}] {$stage}: {$message}", $context);
    }
}
