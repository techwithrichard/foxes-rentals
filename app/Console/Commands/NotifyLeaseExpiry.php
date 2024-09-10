<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Notifications\TenantExpiringLeaseNotification;
use Illuminate\Console\Command;

class NotifyLeaseExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:expiring-leases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies the user that their lease is expiring within either 30, 14 or 7 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        //info message
        $this->info('Notifying users of expiring leases...');

        //Get leases that will expire exactly 30 days or 14 days or 7 days or 1 day from now
        $leases = Lease::with(['tenant', 'property:id,name'])
            ->whereDate('end_date',  now()->addDays(30))
            ->orWhereDate('end_date', now()->addDays(14))
            ->orWhereDate('end_date',  now()->addDays(7))
            ->orWhereDate('end_date',  now()->addDay())
            ->chunkById(100, function ($leases) {
                // process the $leases collection in chunks of 100 records
                foreach ($leases as $lease) {
                    $details = [
                        'property' => $lease->property->name,
                        'end_date' => $lease->end_date->format(' M d, Y'),
                    ];
                    $lease->tenant->notify(new TenantExpiringLeaseNotification($details));
                }

            });




        //show info message
        $this->info('All users have been notified of expiring leases.');

        return Command::SUCCESS;
    }
}
