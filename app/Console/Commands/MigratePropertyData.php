<?php

namespace App\Console\Commands;

use App\Models\PropertyConsolidated;
use App\Models\PropertyDetail;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use App\Models\LeaseProperty;
use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigratePropertyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:migrate-data 
                            {--dry-run : Run migration without actually moving data}
                            {--batch-size=100 : Number of records to process at once}
                            {--force : Force migration even if consolidated table has data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate property data from old tables to consolidated structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting property data migration...');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No data will be migrated');
        }

        // Check if consolidated table has data
        if (PropertyConsolidated::count() > 0 && !$this->option('force')) {
            $this->error('Consolidated table already has data. Use --force to override.');
            return 1;
        }

        try {
            DB::beginTransaction();

            // Migrate rental properties
            $this->migrateRentalProperties();
            
            // Migrate sale properties
            $this->migrateSaleProperties();
            
            // Migrate lease properties
            $this->migrateLeaseProperties();
            
            // Migrate legacy properties table
            $this->migrateLegacyProperties();

            if (!$this->option('dry-run')) {
                DB::commit();
                $this->info('Property data migration completed successfully!');
            } else {
                DB::rollBack();
                $this->info('Dry run completed - no data was migrated');
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Migration failed: ' . $e->getMessage());
            Log::error('Property migration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }
    }

    private function migrateRentalProperties()
    {
        $this->info('Migrating rental properties...');
        
        $rentalProperties = RentalProperty::with(['propertyType', 'landlord', 'address'])->get();
        $progressBar = $this->output->createProgressBar($rentalProperties->count());
        
        foreach ($rentalProperties as $rental) {
            if (!$this->option('dry-run')) {
                $this->createConsolidatedProperty($rental, 'rental');
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$rentalProperties->count()} rental properties");
    }

    private function migrateSaleProperties()
    {
        $this->info('Migrating sale properties...');
        
        $saleProperties = SaleProperty::with(['propertyType', 'landlord'])->get();
        $progressBar = $this->output->createProgressBar($saleProperties->count());
        
        foreach ($saleProperties as $sale) {
            if (!$this->option('dry-run')) {
                $this->createConsolidatedProperty($sale, 'sale');
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$saleProperties->count()} sale properties");
    }

    private function migrateLeaseProperties()
    {
        $this->info('Migrating lease properties...');
        
        $leaseProperties = LeaseProperty::with(['propertyType', 'landlord'])->get();
        $progressBar = $this->output->createProgressBar($leaseProperties->count());
        
        foreach ($leaseProperties as $lease) {
            if (!$this->option('dry-run')) {
                $this->createConsolidatedProperty($lease, 'lease');
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$leaseProperties->count()} lease properties");
    }

    private function migrateLegacyProperties()
    {
        $this->info('Migrating legacy properties...');
        
        $legacyProperties = Property::with(['propertyType', 'landlord', 'address'])->get();
        $progressBar = $this->output->createProgressBar($legacyProperties->count());
        
        foreach ($legacyProperties as $property) {
            if (!$this->option('dry-run')) {
                $this->createConsolidatedProperty($property, 'rental'); // Default to rental
            }
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Processed {$legacyProperties->count()} legacy properties");
    }

    private function createConsolidatedProperty($sourceProperty, string $subtype)
    {
        // Map common fields
        $data = [
            'id' => $sourceProperty->id, // Keep original UUID
            'name' => $sourceProperty->name,
            'description' => $sourceProperty->description,
            'property_type_id' => $sourceProperty->property_type_id,
            'landlord_id' => $sourceProperty->landlord_id,
            'property_subtype' => $subtype,
            'status' => $this->mapStatus($sourceProperty->status ?? 'active'),
            'is_available' => $sourceProperty->is_available ?? true,
            'is_vacant' => $sourceProperty->is_vacant ?? true,
            'electricity_id' => $sourceProperty->electricity_id,
            'water_id' => $sourceProperty->water_id,
            'furnished' => $sourceProperty->furnished ?? false,
            'pet_friendly' => $sourceProperty->pet_friendly ?? false,
            'smoking_allowed' => $sourceProperty->smoking_allowed ?? false,
            'parking_spaces' => $sourceProperty->parking_spaces ?? 0,
            'bedrooms' => $sourceProperty->bedrooms ?? 0,
            'bathrooms' => $sourceProperty->bathrooms ?? 0,
            'property_size' => $sourceProperty->property_size,
            'lot_size' => $sourceProperty->lot_size,
            'year_built' => $sourceProperty->year_built,
            'latitude' => $sourceProperty->latitude,
            'longitude' => $sourceProperty->longitude,
            'features' => $sourceProperty->features,
            'images' => $sourceProperty->images,
            'is_featured' => $sourceProperty->is_featured ?? false,
            'is_published' => $sourceProperty->is_published ?? false,
            'published_at' => $sourceProperty->published_at,
            'views_count' => $sourceProperty->views_count ?? 0,
            'inquiries_count' => $sourceProperty->inquiries_count ?? 0,
            'applications_count' => $sourceProperty->applications_count ?? 0,
            'created_at' => $sourceProperty->created_at,
            'updated_at' => $sourceProperty->updated_at,
            'deleted_at' => $sourceProperty->deleted_at,
        ];

        // Map subtype-specific fields
        switch ($subtype) {
            case 'rental':
                $data['base_amount'] = $sourceProperty->rent_amount ?? $sourceProperty->rent ?? 0;
                $data['deposit_amount'] = $sourceProperty->deposit_amount ?? $sourceProperty->deposit;
                $data['commission_rate'] = $sourceProperty->commission_rate ?? $sourceProperty->commission ?? 0;
                $data['is_multi_unit'] = $sourceProperty->is_multi_unit ?? false;
                $data['total_units'] = $sourceProperty->total_units ?? 1;
                $data['available_units'] = $sourceProperty->available_units ?? 1;
                break;
                
            case 'sale':
                $data['base_amount'] = $sourceProperty->sale_price ?? 0;
                $data['commission_rate'] = $sourceProperty->commission_rate ?? 0;
                break;
                
            case 'lease':
                $data['base_amount'] = $sourceProperty->lease_amount ?? 0;
                $data['deposit_amount'] = $sourceProperty->deposit_amount;
                $data['commission_rate'] = $sourceProperty->commission_rate ?? 0;
                break;
        }

        // Create consolidated property
        $consolidatedProperty = PropertyConsolidated::create($data);

        // Create property details for subtype-specific data
        $this->createPropertyDetails($consolidatedProperty, $sourceProperty, $subtype);

        // Migrate address if exists
        if ($sourceProperty->address) {
            $this->migrateAddress($consolidatedProperty, $sourceProperty->address);
        }

        return $consolidatedProperty;
    }

    private function createPropertyDetails(PropertyConsolidated $property, $sourceProperty, string $subtype)
    {
        $detailData = [];

        // Collect subtype-specific fields
        switch ($subtype) {
            case 'rental':
                $detailData = [
                    'lease_duration_months' => $sourceProperty->lease_duration_months ?? null,
                    'minimum_lease_period' => $sourceProperty->minimum_lease_period,
                    'maximum_lease_period' => $sourceProperty->maximum_lease_period,
                    'renewal_terms' => $sourceProperty->renewal_terms,
                    'utilities_included' => $sourceProperty->utilities_included,
                    'maintenance_responsibility' => $sourceProperty->maintenance_responsibility,
                    'lease_terms' => $sourceProperty->lease_terms,
                    'notice_period' => $sourceProperty->notice_period,
                    'late_fee_percentage' => $sourceProperty->late_fee_percentage,
                    'late_fee_fixed' => $sourceProperty->late_fee_fixed,
                    'returned_check_fee' => $sourceProperty->returned_check_fee,
                    'early_termination_fee' => $sourceProperty->early_termination_fee,
                ];
                break;
                
            case 'sale':
                $detailData = [
                    'sale_terms' => $sourceProperty->sale_terms ?? null,
                    'special_conditions' => $sourceProperty->special_conditions,
                    'marketing_description' => $sourceProperty->marketing_description,
                    'keywords' => $sourceProperty->keywords,
                ];
                break;
                
            case 'lease':
                $detailData = [
                    'lease_duration_months' => $sourceProperty->lease_duration_months ?? null,
                    'minimum_lease_period' => $sourceProperty->minimum_lease_period,
                    'maximum_lease_period' => $sourceProperty->maximum_lease_period,
                    'renewal_terms' => $sourceProperty->renewal_terms,
                    'lease_terms' => $sourceProperty->lease_terms,
                    'special_conditions' => $sourceProperty->special_conditions,
                    'marketing_description' => $sourceProperty->marketing_description,
                ];
                break;
        }

        // Remove null values
        $detailData = array_filter($detailData, function($value) {
            return $value !== null;
        });

        if (!empty($detailData)) {
            PropertyDetail::create([
                'property_id' => $property->id,
                'detail_type' => $subtype,
                'detail_data' => $detailData,
            ]);
        }
    }

    private function migrateAddress(PropertyConsolidated $property, $address)
    {
        // Create new address record linked to consolidated property
        $address->addressable_type = PropertyConsolidated::class;
        $address->addressable_id = $property->id;
        $address->save();
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
