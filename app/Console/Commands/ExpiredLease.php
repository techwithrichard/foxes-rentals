<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpiredLease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:expired-leases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies the user that their lease has expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
