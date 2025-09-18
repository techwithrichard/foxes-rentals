<?php

namespace App\Providers;

use App\Models\PropertyConsolidated;
use App\Models\PropertyDetail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class PropertyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register morph map for polymorphic relationships
        Relation::morphMap([
            'property_consolidated' => PropertyConsolidated::class,
            'property_detail' => PropertyDetail::class,
        ]);

        // Register model observers if needed
        // PropertyConsolidated::observe(PropertyObserver::class);
    }
}
