<?php

namespace App\Services;

use App\Models\PropertyConsolidated;
use App\Models\PropertyDetail;
use App\Models\PropertyType;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyService
{
    protected $propertyRepository;
    protected $propertyTypeRepository;

    public function __construct(
        PropertyRepositoryInterface $propertyRepository
    ) {
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * Create a new property
     */
    public function createProperty(array $data, string $type): PropertyConsolidated
    {
        return $this->transaction(function () use ($data, $type) {
            // Validate property type
            $this->validatePropertyType($type);

            // Set property subtype
            $data['property_subtype'] = $type;

            // Set default values
            $data = $this->setDefaultValues($data, $type);

            // Create property
            $property = $this->propertyRepository->create($data);

            // Create property details if provided
            if (isset($data['detail_data'])) {
                $this->createPropertyDetails($property, $data['detail_data'], $type);
            }

            // Create address if provided
            if (isset($data['address'])) {
                $this->createAddress($property, $data['address']);
            }

            Log::info('Property created successfully', [
                'property_id' => $property->id,
                'type' => $type,
                'name' => $property->name
            ]);

            return $property->load(['propertyType', 'landlord', 'address', 'details']);
        });
    }

    /**
     * Update an existing property
     */
    public function updateProperty(PropertyConsolidated $property, array $data): PropertyConsolidated
    {
        return $this->transaction(function () use ($property, $data) {
            // Update property
            $property = $this->propertyRepository->update($property, $data);

            // Update property details if provided
            if (isset($data['detail_data'])) {
                $this->updatePropertyDetails($property, $data['detail_data']);
            }

            // Update address if provided
            if (isset($data['address'])) {
                $this->updateAddress($property, $data['address']);
            }

            Log::info('Property updated successfully', [
                'property_id' => $property->id,
                'name' => $property->name
            ]);

            return $property->load(['propertyType', 'landlord', 'address', 'details']);
        });
    }

    /**
     * Delete a property
     */
    public function deleteProperty(PropertyConsolidated $property): bool
    {
        return $this->transaction(function () use ($property) {
            // Soft delete property details
            $property->details()->delete();

            // Soft delete the property
            $result = $this->propertyRepository->softDelete($property);

            Log::info('Property deleted successfully', [
                'property_id' => $property->id,
                'name' => $property->name
            ]);

            return $result;
        });
    }

    /**
     * Search properties with filters
     */
    public function searchProperties(array $filters): Collection
    {
        return $this->propertyRepository->advancedSearch($filters);
    }

    /**
     * Get properties by type
     */
    public function getPropertiesByType(string $type): Collection
    {
        return $this->propertyRepository->findByType($type);
    }

    /**
     * Get available properties
     */
    public function getAvailableProperties(): Collection
    {
        return $this->propertyRepository->findAvailable();
    }

    /**
     * Get vacant properties
     */
    public function getVacantProperties(): Collection
    {
        return $this->propertyRepository->findVacant();
    }

    /**
     * Get featured properties
     */
    public function getFeaturedProperties(): Collection
    {
        return $this->propertyRepository->findFeatured();
    }

    /**
     * Get properties by landlord
     */
    public function getPropertiesByLandlord(string $landlordId): Collection
    {
        return $this->propertyRepository->findByLandlord($landlordId);
    }

    /**
     * Get property statistics
     */
    public function getPropertyStatistics(): array
    {
        return $this->propertyRepository->getStatistics();
    }

    /**
     * Toggle property status
     */
    public function togglePropertyStatus(PropertyConsolidated $property, string $status): PropertyConsolidated
    {
        $allowedStatuses = ['active', 'inactive', 'maintenance', 'sold'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $property = $this->propertyRepository->update($property, ['status' => $status]);

        Log::info('Property status toggled', [
            'property_id' => $property->id,
            'new_status' => $status
        ]);

        return $property;
    }

    /**
     * Toggle property availability
     */
    public function togglePropertyAvailability(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'is_available' => !$property->is_available
        ]);

        Log::info('Property availability toggled', [
            'property_id' => $property->id,
            'is_available' => $property->is_available
        ]);

        return $property;
    }

    /**
     * Toggle property vacancy
     */
    public function togglePropertyVacancy(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'is_vacant' => !$property->is_vacant
        ]);

        Log::info('Property vacancy toggled', [
            'property_id' => $property->id,
            'is_vacant' => $property->is_vacant
        ]);

        return $property;
    }

    /**
     * Toggle property featured status
     */
    public function togglePropertyFeatured(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'is_featured' => !$property->is_featured
        ]);

        Log::info('Property featured status toggled', [
            'property_id' => $property->id,
            'is_featured' => $property->is_featured
        ]);

        return $property;
    }

    /**
     * Publish/unpublish property
     */
    public function togglePropertyPublished(PropertyConsolidated $property): PropertyConsolidated
    {
        $data = [
            'is_published' => !$property->is_published
        ];

        if (!$property->is_published) {
            $data['published_at'] = now();
        }

        $property = $this->propertyRepository->update($property, $data);

        Log::info('Property published status toggled', [
            'property_id' => $property->id,
            'is_published' => $property->is_published
        ]);

        return $property;
    }

    /**
     * Update property views count
     */
    public function incrementPropertyViews(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'views_count' => $property->views_count + 1
        ]);

        return $property;
    }

    /**
     * Update property inquiries count
     */
    public function incrementPropertyInquiries(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'inquiries_count' => $property->inquiries_count + 1
        ]);

        return $property;
    }

    /**
     * Update property applications count
     */
    public function incrementPropertyApplications(PropertyConsolidated $property): PropertyConsolidated
    {
        $property = $this->propertyRepository->update($property, [
            'applications_count' => $property->applications_count + 1
        ]);

        return $property;
    }

    /**
     * Validate property type
     */
    protected function validatePropertyType(string $type): void
    {
        $allowedTypes = ['rental', 'sale', 'lease'];
        
        if (!in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException("Invalid property type: {$type}");
        }
    }

    /**
     * Set default values for property
     */
    protected function setDefaultValues(array $data, string $type): array
    {
        $defaults = [
            'status' => 'active',
            'is_available' => true,
            'is_vacant' => true,
            'is_multi_unit' => false,
            'total_units' => 1,
            'available_units' => 1,
            'furnished' => false,
            'pet_friendly' => false,
            'smoking_allowed' => false,
            'parking_spaces' => 0,
            'bedrooms' => 0,
            'bathrooms' => 0,
            'is_featured' => false,
            'is_published' => false,
            'views_count' => 0,
            'inquiries_count' => 0,
            'applications_count' => 0,
        ];

        return array_merge($defaults, $data);
    }

    /**
     * Create property details
     */
    protected function createPropertyDetails(PropertyConsolidated $property, array $detailData, string $type): void
    {
        PropertyDetail::create([
            'property_id' => $property->id,
            'detail_type' => $type,
            'detail_data' => $detailData,
        ]);
    }

    /**
     * Update property details
     */
    protected function updatePropertyDetails(PropertyConsolidated $property, array $detailData): void
    {
        $property->details()
            ->where('detail_type', $property->property_subtype)
            ->update(['detail_data' => $detailData]);
    }

    /**
     * Create address for property
     */
    protected function createAddress(PropertyConsolidated $property, array $addressData): void
    {
        $property->address()->create($addressData);
    }

    /**
     * Update address for property
     */
    protected function updateAddress(PropertyConsolidated $property, array $addressData): void
    {
        if ($property->address) {
            $property->address->update($addressData);
        } else {
            $this->createAddress($property, $addressData);
        }
    }

    /**
     * Execute database transaction
     */
    protected function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }
}
