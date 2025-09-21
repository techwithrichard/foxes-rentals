<?php

namespace App\Console\Commands;

use App\Services\AutomationService;
use Illuminate\Console\Command;

class RunAutomationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foxes:run-automation 
                            {--type=all : Type of automation to run (all, lease, payment, property, user)}
                            {--dry-run : Run without executing actions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Foxes Rentals automation rules';

    /**
     * Execute the console command.
     */
    public function handle(AutomationService $automationService): int
    {
        $this->info('🤖 Starting Foxes Rentals Automation System...');
        $this->newLine();

        $startTime = microtime(true);
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        try {
            if ($dryRun) {
                $this->warn('🔍 Running in DRY RUN mode - no actions will be executed');
                $this->newLine();
            }

            $results = $automationService->executeDueRules();

            $duration = microtime(true) - $startTime;

            $this->newLine();
            $this->info("✅ Automation completed in " . round($duration, 2) . " seconds");
            $this->newLine();

            // Display results
            $this->displayResults($results);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Automation failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display automation results
     */
    private function displayResults(array $results): void
    {
        $this->info('📊 Automation Results:');
        $this->newLine();

        $this->line("✅ Executed: {$results['executed']}");
        $this->line("❌ Failed: {$results['failed']}");
        $this->line("⏭️ Skipped: {$results['skipped']}");

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('🚨 Errors:');
            foreach ($results['errors'] as $error) {
                $this->line("  • Rule: {$error['rule_name']} - {$error['error']}");
            }
        }

        $this->newLine();
    }
}
