<?php

namespace App\Console\Commands;

use App\Models\PropertyConsolidated;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use App\Models\LeaseProperty;
use App\Models\Property;
use Illuminate\Console\Command;

class TestPropertyMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:test-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the property migration by comparing old and new data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing property migration...');
        $this->newLine();

        // Test rental properties
        $this->testRentalProperties();
        
        // Test sale properties
        $this->testSaleProperties();
        
        // Test lease properties
        $this->testLeaseProperties();
        
        // Test legacy properties
        $this->testLegacyProperties();

        $this->newLine();
        $this->info('Migration test completed!');
    }

    private function testRentalProperties()
    {
        $this->info('Testing Rental Properties Migration:');
        
        $originalCount = RentalProperty::count();
        $migratedCount = PropertyConsolidated::where('property_subtype', 'rental')->count();
        
        $this->line("Original rental properties: {$originalCount}");
        $this->line("Migrated rental properties: {$migratedCount}");
        
        if ($originalCount === $migratedCount) {
            $this->info('✅ Rental properties count matches');
        } else {
            $this->error('❌ Rental properties count mismatch');
        }

        // Test sample data
        if ($originalCount > 0 && $migratedCount > 0) {
            $original = RentalProperty::first();
            $migrated = PropertyConsolidated::where('property_subtype', 'rental')->first();
            
            $this->testPropertyData($original, $migrated, 'Rental');
        }
        
        $this->newLine();
    }

    private function testSaleProperties()
    {
        $this->info('Testing Sale Properties Migration:');
        
        $originalCount = SaleProperty::count();
        $migratedCount = PropertyConsolidated::where('property_subtype', 'sale')->count();
        
        $this->line("Original sale properties: {$originalCount}");
        $this->line("Migrated sale properties: {$migratedCount}");
        
        if ($originalCount === $migratedCount) {
            $this->info('✅ Sale properties count matches');
        } else {
            $this->error('❌ Sale properties count mismatch');
        }

        // Test sample data
        if ($originalCount > 0 && $migratedCount > 0) {
            $original = SaleProperty::first();
            $migrated = PropertyConsolidated::where('property_subtype', 'sale')->first();
            
            $this->testPropertyData($original, $migrated, 'Sale');
        }
        
        $this->newLine();
    }

    private function testLeaseProperties()
    {
        $this->info('Testing Lease Properties Migration:');
        
        $originalCount = LeaseProperty::count();
        $migratedCount = PropertyConsolidated::where('property_subtype', 'lease')->count();
        
        $this->line("Original lease properties: {$originalCount}");
        $this->line("Migrated lease properties: {$migratedCount}");
        
        if ($originalCount === $migratedCount) {
            $this->info('✅ Lease properties count matches');
        } else {
            $this->error('❌ Lease properties count mismatch');
        }

        // Test sample data
        if ($originalCount > 0 && $migratedCount > 0) {
            $original = LeaseProperty::first();
            $migrated = PropertyConsolidated::where('property_subtype', 'lease')->first();
            
            $this->testPropertyData($original, $migrated, 'Lease');
        }
        
        $this->newLine();
    }

    private function testLegacyProperties()
    {
        $this->info('Testing Legacy Properties Migration:');
        
        $originalCount = Property::count();
        $migratedCount = PropertyConsolidated::where('property_subtype', 'rental')->count();
        
        $this->line("Original legacy properties: {$originalCount}");
        $this->line("Migrated legacy properties (as rental): {$migratedCount}");
        
        // Test sample data
        if ($originalCount > 0 && $migratedCount > 0) {
            $original = Property::first();
            $migrated = PropertyConsolidated::where('property_subtype', 'rental')->first();
            
            $this->testPropertyData($original, $migrated, 'Legacy');
        }
        
        $this->newLine();
    }

    private function testPropertyData($original, $migrated, string $type)
    {
        $this->line("Testing {$type} property data integrity:");
        
        $tests = [
            'Name' => $original->name === $migrated->name,
            'Description' => $original->description === $migrated->description,
            'Landlord ID' => $original->landlord_id === $migrated->landlord_id,
            'Property Type ID' => $original->property_type_id === $migrated->property_type_id,
            'Status' => $this->mapStatus($original->status ?? 'active') === $migrated->status,
        ];

        // Test amount fields based on type
        switch ($type) {
            case 'Rental':
                $tests['Rent Amount'] = ($original->rent_amount ?? $original->rent) === $migrated->base_amount;
                break;
            case 'Sale':
                $tests['Sale Price'] = $original->sale_price === $migrated->base_amount;
                break;
            case 'Lease':
                $tests['Lease Amount'] = $original->lease_amount === $migrated->base_amount;
                break;
            case 'Legacy':
                $tests['Rent Amount'] = $original->rent === $migrated->base_amount;
                break;
        }

        foreach ($tests as $field => $passed) {
            if ($passed) {
                $this->info("  ✅ {$field}");
            } else {
                $this->error("  ❌ {$field}");
                $this->line("    Original: " . ($original->{$field} ?? 'N/A'));
                $this->line("    Migrated: " . ($migrated->{$field} ?? 'N/A'));
            }
        }
    }

    private function mapStatus($status)
    {
        $statusMap = [
            'active' => 'active',
            'inactive' => 'inactive',
            'maintenance' => 'maintenance',
            'sold' => 'sold',
            'pending' => 'active',
            'available' => 'active',
            'unavailable' => 'inactive',
        ];

        return $statusMap[$status] ?? 'active';
    }
}
