<?php

namespace App\Repositories;

use App\Models\PropertyConsolidated;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PropertyRepository extends BaseRepository implements PropertyRepositoryInterface
{
    public function __construct(PropertyConsolidated $model)
    {
        parent::__construct($model);
    }

    /**
     * Find properties by type
     */
    public function findByType(string $type): Collection
    {
        return $this->getQuery()
            ->where('property_subtype', $type)
            ->get();
    }

    /**
     * Find available properties
     */
    public function findAvailable(): Collection
    {
        return $this->getQuery()
            ->where('is_available', true)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find vacant properties
     */
    public function findVacant(): Collection
    {
        return $this->getQuery()
            ->where('is_vacant', true)
            ->where('is_available', true)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find occupied properties
     */
    public function findOccupied(): Collection
    {
        return $this->getQuery()
            ->where('is_vacant', false)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find featured properties
     */
    public function findFeatured(): Collection
    {
        return $this->getQuery()
            ->where('is_featured', true)
            ->where('is_published', true)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find published properties
     */
    public function findPublished(): Collection
    {
        return $this->getQuery()
            ->where('is_published', true)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Search properties by location
     */
    public function searchByLocation(string $location): Collection
    {
        return $this->getQuery()
            ->whereHas('address', function ($query) use ($location) {
                $query->where('city', 'like', "%{$location}%")
                      ->orWhere('state', 'like', "%{$location}%")
                      ->orWhere('country', 'like', "%{$location}%");
            })
            ->get();
    }

    /**
     * Search properties by price range
     */
    public function searchByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->getQuery()
            ->whereBetween('base_amount', [$minPrice, $maxPrice])
            ->where('status', 'active')
            ->get();
    }

    /**
     * Search properties by bedrooms
     */
    public function searchByBedrooms(int $bedrooms): Collection
    {
        return $this->getQuery()
            ->where('bedrooms', $bedrooms)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Search properties by bathrooms
     */
    public function searchByBathrooms(int $bathrooms): Collection
    {
        return $this->getQuery()
            ->where('bathrooms', $bathrooms)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Search properties by features
     */
    public function searchByFeatures(array $features): Collection
    {
        $query = $this->getQuery()->where('status', 'active');

        foreach ($features as $feature) {
            $query->whereJsonContains('features', $feature);
        }

        return $query->get();
    }

    /**
     * Find properties by landlord
     */
    public function findByLandlord(string $landlordId): Collection
    {
        return $this->getQuery()
            ->where('landlord_id', $landlordId)
            ->get();
    }

    /**
     * Find properties by property type
     */
    public function findByPropertyType(string $propertyTypeId): Collection
    {
        return $this->getQuery()
            ->where('property_type_id', $propertyTypeId)
            ->get();
    }

    /**
     * Get property statistics
     */
    public function getStatistics(): array
    {
        $query = $this->getQuery();

        return [
            'total_properties' => $query->count(),
            'rental_properties' => $query->where('property_subtype', 'rental')->count(),
            'sale_properties' => $query->where('property_subtype', 'sale')->count(),
            'lease_properties' => $query->where('property_subtype', 'lease')->count(),
            'active_properties' => $query->where('status', 'active')->count(),
            'inactive_properties' => $query->where('status', 'inactive')->count(),
            'maintenance_properties' => $query->where('status', 'maintenance')->count(),
            'sold_properties' => $query->where('status', 'sold')->count(),
            'available_properties' => $query->where('is_available', true)->count(),
            'vacant_properties' => $query->where('is_vacant', true)->count(),
            'occupied_properties' => $query->where('is_vacant', false)->count(),
            'featured_properties' => $query->where('is_featured', true)->count(),
            'published_properties' => $query->where('is_published', true)->count(),
            'average_price' => $query->avg('base_amount'),
            'min_price' => $query->min('base_amount'),
            'max_price' => $query->max('base_amount'),
        ];
    }

    /**
     * Get properties with active leases
     */
    public function getWithActiveLeases(): Collection
    {
        return $this->getQuery()
            ->whereHas('activeLeases')
            ->get();
    }

    /**
     * Get properties with maintenance requests
     */
    public function getWithMaintenanceRequests(): Collection
    {
        return $this->getQuery()
            ->whereHas('maintenanceRequests')
            ->get();
    }

    /**
     * Get properties with inquiries
     */
    public function getWithInquiries(): Collection
    {
        return $this->getQuery()
            ->whereHas('inquiries')
            ->get();
    }

    /**
     * Get properties with applications
     */
    public function getWithApplications(): Collection
    {
        return $this->getQuery()
            ->whereHas('applications')
            ->get();
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(array $criteria): Collection
    {
        $query = $this->getQuery();

        // Property subtype
        if (isset($criteria['subtype'])) {
            $query->where('property_subtype', $criteria['subtype']);
        }

        // Status
        if (isset($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }

        // Availability
        if (isset($criteria['available'])) {
            $query->where('is_available', $criteria['available']);
        }

        // Vacancy
        if (isset($criteria['vacant'])) {
            $query->where('is_vacant', $criteria['vacant']);
        }

        // Price range
        if (isset($criteria['min_price'])) {
            $query->where('base_amount', '>=', $criteria['min_price']);
        }
        if (isset($criteria['max_price'])) {
            $query->where('base_amount', '<=', $criteria['max_price']);
        }

        // Bedrooms
        if (isset($criteria['bedrooms'])) {
            $query->where('bedrooms', $criteria['bedrooms']);
        }

        // Bathrooms
        if (isset($criteria['bathrooms'])) {
            $query->where('bathrooms', $criteria['bathrooms']);
        }

        // Features
        if (isset($criteria['features']) && is_array($criteria['features'])) {
            foreach ($criteria['features'] as $feature) {
                $query->whereJsonContains('features', $feature);
            }
        }

        // Location
        if (isset($criteria['location'])) {
            $query->whereHas('address', function ($q) use ($criteria) {
                $q->where('city', 'like', "%{$criteria['location']}%")
                  ->orWhere('state', 'like', "%{$criteria['location']}%");
            });
        }

        // Landlord
        if (isset($criteria['landlord_id'])) {
            $query->where('landlord_id', $criteria['landlord_id']);
        }

        // Property type
        if (isset($criteria['property_type_id'])) {
            $query->where('property_type_id', $criteria['property_type_id']);
        }

        // Featured
        if (isset($criteria['featured'])) {
            $query->where('is_featured', $criteria['featured']);
        }

        // Published
        if (isset($criteria['published'])) {
            $query->where('is_published', $criteria['published']);
        }

        return $query->get();
    }
}
