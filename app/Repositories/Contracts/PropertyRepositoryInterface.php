<?php

namespace App\Repositories\Contracts;

use App\Models\PropertyConsolidated;
use Illuminate\Database\Eloquent\Collection;

interface PropertyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find properties by type
     */
    public function findByType(string $type): Collection;

    /**
     * Find available properties
     */
    public function findAvailable(): Collection;

    /**
     * Find vacant properties
     */
    public function findVacant(): Collection;

    /**
     * Find occupied properties
     */
    public function findOccupied(): Collection;

    /**
     * Find featured properties
     */
    public function findFeatured(): Collection;

    /**
     * Find published properties
     */
    public function findPublished(): Collection;

    /**
     * Search properties by location
     */
    public function searchByLocation(string $location): Collection;

    /**
     * Search properties by price range
     */
    public function searchByPriceRange(float $minPrice, float $maxPrice): Collection;

    /**
     * Search properties by bedrooms
     */
    public function searchByBedrooms(int $bedrooms): Collection;

    /**
     * Search properties by bathrooms
     */
    public function searchByBathrooms(int $bathrooms): Collection;

    /**
     * Search properties by features
     */
    public function searchByFeatures(array $features): Collection;

    /**
     * Find properties by landlord
     */
    public function findByLandlord(string $landlordId): Collection;

    /**
     * Find properties by property type
     */
    public function findByPropertyType(string $propertyTypeId): Collection;

    /**
     * Get property statistics
     */
    public function getStatistics(): array;

    /**
     * Get properties with active leases
     */
    public function getWithActiveLeases(): Collection;

    /**
     * Get properties with maintenance requests
     */
    public function getWithMaintenanceRequests(): Collection;

    /**
     * Get properties with inquiries
     */
    public function getWithInquiries(): Collection;

    /**
     * Get properties with applications
     */
    public function getWithApplications(): Collection;
}
