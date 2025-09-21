<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 2;

    protected User $user;
    protected string $reportType;
    protected array $parameters;
    protected string $format;

    /**
     * Create a new job instance.
     */
    public function __construct(
        User $user,
        string $reportType,
        array $parameters = [],
        string $format = 'pdf'
    ) {
        $this->user = $user;
        $this->reportType = $reportType;
        $this->parameters = $parameters;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportingService $reportingService): void
    {
        try {
            Log::info("Generating report job started", [
                'user_id' => $this->user->id,
                'report_type' => $this->reportType,
                'format' => $this->format
            ]);

            // Generate the report
            $reportData = $this->generateReport($reportingService);

            // Save the report file
            $filePath = $this->saveReport($reportData);

            // Send notification to user
            $this->notifyUser($filePath);

            Log::info("Report generation completed", [
                'user_id' => $this->user->id,
                'report_type' => $this->reportType,
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error("Report generation failed", [
                'user_id' => $this->user->id,
                'report_type' => $this->reportType,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate the report based on type
     */
    private function generateReport(ReportingService $reportingService): array
    {
        switch ($this->reportType) {
            case 'financial':
                return $reportingService->generateFinancialReport(
                    $this->parameters['start_date'] ?? now()->startOfMonth(),
                    $this->parameters['end_date'] ?? now()->endOfMonth(),
                    $this->parameters['landlord_id'] ?? null
                );

            case 'property':
                return $reportingService->generatePropertyReport(
                    $this->parameters['property_id'] ?? null,
                    $this->parameters['start_date'] ?? now()->startOfMonth(),
                    $this->parameters['end_date'] ?? now()->endOfMonth()
                );

            case 'tenant':
                return $reportingService->generateTenantReport(
                    $this->parameters['tenant_id'] ?? null,
                    $this->parameters['start_date'] ?? now()->startOfMonth(),
                    $this->parameters['end_date'] ?? now()->endOfMonth()
                );

            case 'occupancy':
                return $reportingService->generateOccupancyReport(
                    $this->parameters['start_date'] ?? now()->startOfMonth(),
                    $this->parameters['end_date'] ?? now()->endOfMonth()
                );

            default:
                throw new \InvalidArgumentException("Unknown report type: {$this->reportType}");
        }
    }

    /**
     * Save the report to storage
     */
    private function saveReport(array $reportData): string
    {
        $fileName = $this->generateFileName();
        $filePath = "reports/{$this->user->id}/{$fileName}";

        if ($this->format === 'pdf') {
            $content = $this->generatePdfContent($reportData);
            Storage::put($filePath, $content);
        } elseif ($this->format === 'excel') {
            $content = $this->generateExcelContent($reportData);
            Storage::put($filePath, $content);
        } else {
            throw new \InvalidArgumentException("Unsupported format: {$this->format}");
        }

        return $filePath;
    }

    /**
     * Generate file name
     */
    private function generateFileName(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        return "{$this->reportType}_report_{$timestamp}.{$this->format}";
    }

    /**
     * Generate PDF content
     */
    private function generatePdfContent(array $reportData): string
    {
        // This would integrate with a PDF library like DomPDF or TCPDF
        // For now, return a simple HTML representation
        $html = view('reports.pdf', [
            'reportData' => $reportData,
            'reportType' => $this->reportType,
            'generatedAt' => now(),
            'user' => $this->user
        ])->render();

        return $html;
    }

    /**
     * Generate Excel content
     */
    private function generateExcelContent(array $reportData): string
    {
        // This would integrate with Laravel Excel
        // For now, return CSV format
        $csv = $this->arrayToCsv($reportData);
        return $csv;
    }

    /**
     * Convert array to CSV
     */
    private function arrayToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $csv = '';
        $headers = array_keys($data[0]);
        $csv .= implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $csv .= implode(',', array_values($row)) . "\n";
        }

        return $csv;
    }

    /**
     * Notify user about completed report
     */
    private function notifyUser(string $filePath): void
    {
        $this->user->notify(new \App\Notifications\ReportGeneratedNotification(
            $this->reportType,
            $filePath,
            $this->format
        ));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Report generation job failed permanently", [
            'user_id' => $this->user->id,
            'report_type' => $this->reportType,
            'error' => $exception->getMessage()
        ]);

        // Notify user about failure
        $this->user->notify(new \App\Notifications\ReportGenerationFailedNotification(
            $this->reportType,
            $exception->getMessage()
        ));
    }
}
