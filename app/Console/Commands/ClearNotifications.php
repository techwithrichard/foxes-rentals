<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all notifications which are older than 60 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Delete all notifications which are older than 60 days for all users
        DB::table('notifications')
            ->where('created_at', '<', now()->subDays(60))
            ->delete();

        //show info message
        $this->info('All notifications older than 60 days have been deleted.');


        return Command::SUCCESS;
    }
}
