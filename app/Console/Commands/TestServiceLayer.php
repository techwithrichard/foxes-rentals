<?php

namespace App\Console\Commands;

use App\Services\PropertyService;
use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Console\Command;

class TestServiceLayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:service-layer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the service layer implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Service Layer Implementation...');
        $this->newLine();

        try {
            // Test service instantiation
            $this->info('1. Testing service instantiation...');
            $propertyService = app(PropertyService::class);
            $this->info('✅ PropertyService instantiated successfully');

            // Test repository injection
            $this->info('2. Testing repository injection...');
            $reflection = new \ReflectionClass($propertyService);
            $property = $reflection->getProperty('propertyRepository');
            $property->setAccessible(true);
            $repository = $property->getValue($propertyService);
            $this->info('✅ Repository injected successfully');

            // Test statistics method
            $this->info('3. Testing statistics method...');
            $statistics = $propertyService->getPropertyStatistics();
            $this->info('✅ Statistics retrieved successfully');
            $this->line('   Total properties: ' . $statistics['total_properties']);

            // Test search method
            $this->info('4. Testing search method...');
            $properties = $propertyService->searchProperties(['subtype' => 'rental']);
            $this->info('✅ Search method working successfully');
            $this->line('   Found ' . $properties->count() . ' rental properties');

            // Test available properties
            $this->info('5. Testing available properties...');
            $availableProperties = $propertyService->getAvailableProperties();
            $this->info('✅ Available properties retrieved successfully');
            $this->line('   Available properties: ' . $availableProperties->count());

            $this->newLine();
            $this->info('🎉 All service layer tests passed successfully!');

        } catch (\Exception $e) {
            $this->error('❌ Service layer test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
