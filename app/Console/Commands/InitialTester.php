<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InitialTester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Output a message to the log every minute';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('This is a test message from the command line');
        return Command::SUCCESS;
    }
}
