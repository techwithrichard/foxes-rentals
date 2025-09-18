<?php

namespace App\Providers;

use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Invoice;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\PropertyRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\BaseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(PropertyRepositoryInterface::class, PropertyRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        
        // Bind base repository for other models
        $this->app->bind(BaseRepositoryInterface::class, function ($app, $parameters) {
            $model = $parameters['model'] ?? PropertyConsolidated::class;
            return new BaseRepository(new $model);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
