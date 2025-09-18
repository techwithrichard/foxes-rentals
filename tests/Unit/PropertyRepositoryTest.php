<?php

namespace Tests\Unit;

use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Address;
use App\Repositories\PropertyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyRepository;
    protected $propertyType;
    protected $landlord;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->propertyRepository = new PropertyRepository(new PropertyConsolidated());
        $this->propertyType = PropertyType::factory()->create();
        $this->landlord = User::factory()->create();
    }

    public function test_can_find_properties_by_type()
    {
        // Create properties of different types
        PropertyConsolidated::factory()->create(['property_subtype' => 'rental']);
        PropertyConsolidated::factory()->create(['property_subtype' => 'sale']);
        PropertyConsolidated::factory()->create(['property_subtype' => 'lease']);

        $rentalProperties = $this->propertyRepository->findByType('rental');
        $saleProperties = $this->propertyRepository->findByType('sale');
        $leaseProperties = $this->propertyRepository->findByType('lease');

        $this->assertCount(1, $rentalProperties);
        $this->assertCount(1, $saleProperties);
        $this->assertCount(1, $leaseProperties);
        
        $this->assertEquals('rental', $rentalProperties->first()->property_subtype);
        $this->assertEquals('sale', $saleProperties->first()->property_subtype);
        $this->assertEquals('lease', $leaseProperties->first()->property_subtype);
    }

    public function test_can_find_available_properties()
    {
        PropertyConsolidated::factory()->create(['is_available' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_available' => false, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_available' => true, 'status' => 'inactive']);

        $availableProperties = $this->propertyRepository->findAvailable();

        $this->assertCount(1, $availableProperties);
        $this->assertTrue($availableProperties->first()->is_available);
        $this->assertEquals('active', $availableProperties->first()->status);
    }

    public function test_can_find_vacant_properties()
    {
        PropertyConsolidated::factory()->create(['is_vacant' => true, 'is_available' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_vacant' => false, 'is_available' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_vacant' => true, 'is_available' => false, 'status' => 'active']);

        $vacantProperties = $this->propertyRepository->findVacant();

        $this->assertCount(1, $vacantProperties);
        $this->assertTrue($vacantProperties->first()->is_vacant);
        $this->assertTrue($vacantProperties->first()->is_available);
    }

    public function test_can_find_occupied_properties()
    {
        PropertyConsolidated::factory()->create(['is_vacant' => false, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_vacant' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_vacant' => false, 'status' => 'inactive']);

        $occupiedProperties = $this->propertyRepository->findOccupied();

        $this->assertCount(1, $occupiedProperties);
        $this->assertFalse($occupiedProperties->first()->is_vacant);
        $this->assertEquals('active', $occupiedProperties->first()->status);
    }

    public function test_can_find_featured_properties()
    {
        PropertyConsolidated::factory()->create(['is_featured' => true, 'is_published' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_featured' => false, 'is_published' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_featured' => true, 'is_published' => false, 'status' => 'active']);

        $featuredProperties = $this->propertyRepository->findFeatured();

        $this->assertCount(1, $featuredProperties);
        $this->assertTrue($featuredProperties->first()->is_featured);
        $this->assertTrue($featuredProperties->first()->is_published);
    }

    public function test_can_find_published_properties()
    {
        PropertyConsolidated::factory()->create(['is_published' => true, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_published' => false, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['is_published' => true, 'status' => 'inactive']);

        $publishedProperties = $this->propertyRepository->findPublished();

        $this->assertCount(1, $publishedProperties);
        $this->assertTrue($publishedProperties->first()->is_published);
        $this->assertEquals('active', $publishedProperties->first()->status);
    }

    public function test_can_search_by_price_range()
    {
        PropertyConsolidated::factory()->create(['base_amount' => 25000, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['base_amount' => 50000, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['base_amount' => 75000, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['base_amount' => 100000, 'status' => 'active']);

        $properties = $this->propertyRepository->searchByPriceRange(30000, 80000);

        $this->assertCount(2, $properties);
        $this->assertTrue($properties->contains('base_amount', 50000));
        $this->assertTrue($properties->contains('base_amount', 75000));
    }

    public function test_can_search_by_bedrooms()
    {
        PropertyConsolidated::factory()->create(['bedrooms' => 1, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['bedrooms' => 2, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['bedrooms' => 3, 'status' => 'active']);

        $properties = $this->propertyRepository->searchByBedrooms(2);

        $this->assertCount(1, $properties);
        $this->assertEquals(2, $properties->first()->bedrooms);
    }

    public function test_can_search_by_bathrooms()
    {
        PropertyConsolidated::factory()->create(['bathrooms' => 1, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['bathrooms' => 2, 'status' => 'active']);
        PropertyConsolidated::factory()->create(['bathrooms' => 3, 'status' => 'active']);

        $properties = $this->propertyRepository->searchByBathrooms(2);

        $this->assertCount(1, $properties);
        $this->assertEquals(2, $properties->first()->bathrooms);
    }

    public function test_can_search_by_features()
    {
        PropertyConsolidated::factory()->create([
            'features' => ['parking', 'garden', 'security'],
            'status' => 'active'
        ]);
        PropertyConsolidated::factory()->create([
            'features' => ['parking', 'gym'],
            'status' => 'active'
        ]);
        PropertyConsolidated::factory()->create([
            'features' => ['garden', 'security'],
            'status' => 'active'
        ]);

        $properties = $this->propertyRepository->searchByFeatures(['parking', 'garden']);

        $this->assertCount(1, $properties);
        $this->assertContains('parking', $properties->first()->features);
        $this->assertContains('garden', $properties->first()->features);
    }

    public function test_can_find_properties_by_landlord()
    {
        $landlord1 = User::factory()->create();
        $landlord2 = User::factory()->create();

        PropertyConsolidated::factory()->create(['landlord_id' => $landlord1->id]);
        PropertyConsolidated::factory()->create(['landlord_id' => $landlord2->id]);
        PropertyConsolidated::factory()->create(['landlord_id' => $landlord1->id]);

        $properties = $this->propertyRepository->findByLandlord($landlord1->id);

        $this->assertCount(2, $properties);
        $this->assertEquals($landlord1->id, $properties->first()->landlord_id);
    }

    public function test_can_find_properties_by_property_type()
    {
        $type1 = PropertyType::factory()->create();
        $type2 = PropertyType::factory()->create();

        PropertyConsolidated::factory()->create(['property_type_id' => $type1->id]);
        PropertyConsolidated::factory()->create(['property_type_id' => $type2->id]);
        PropertyConsolidated::factory()->create(['property_type_id' => $type1->id]);

        $properties = $this->propertyRepository->findByPropertyType($type1->id);

        $this->assertCount(2, $properties);
        $this->assertEquals($type1->id, $properties->first()->property_type_id);
    }

    public function test_can_get_property_statistics()
    {
        // Create test properties
        PropertyConsolidated::factory()->create(['property_subtype' => 'rental', 'status' => 'active', 'is_available' => true, 'is_vacant' => true]);
        PropertyConsolidated::factory()->create(['property_subtype' => 'rental', 'status' => 'active', 'is_available' => true, 'is_vacant' => false]);
        PropertyConsolidated::factory()->create(['property_subtype' => 'sale', 'status' => 'active', 'is_available' => true]);
        PropertyConsolidated::factory()->create(['property_subtype' => 'lease', 'status' => 'inactive', 'is_available' => false]);
        PropertyConsolidated::factory()->create(['property_subtype' => 'rental', 'status' => 'maintenance', 'is_featured' => true]);

        $statistics = $this->propertyRepository->getStatistics();

        $this->assertEquals(5, $statistics['total_properties']);
        $this->assertEquals(3, $statistics['rental_properties']);
        $this->assertEquals(1, $statistics['sale_properties']);
        $this->assertEquals(1, $statistics['lease_properties']);
        $this->assertEquals(3, $statistics['active_properties']);
        $this->assertEquals(1, $statistics['inactive_properties']);
        $this->assertEquals(1, $statistics['maintenance_properties']);
        $this->assertEquals(3, $statistics['available_properties']);
        $this->assertEquals(1, $statistics['vacant_properties']);
        $this->assertEquals(1, $statistics['occupied_properties']);
        $this->assertEquals(1, $statistics['featured_properties']);
    }

    public function test_can_search_by_location()
    {
        $property1 = PropertyConsolidated::factory()->create();
        $property2 = PropertyConsolidated::factory()->create();

        // Create addresses
        Address::factory()->create([
            'addressable_type' => PropertyConsolidated::class,
            'addressable_id' => $property1->id,
            'city' => 'Nairobi',
            'state' => 'Nairobi'
        ]);

        Address::factory()->create([
            'addressable_type' => PropertyConsolidated::class,
            'addressable_id' => $property2->id,
            'city' => 'Mombasa',
            'state' => 'Mombasa'
        ]);

        $properties = $this->propertyRepository->searchByLocation('Nairobi');

        $this->assertCount(1, $properties);
        $this->assertEquals($property1->id, $properties->first()->id);
    }

    public function test_can_perform_advanced_search()
    {
        // Create test properties
        PropertyConsolidated::factory()->create([
            'property_subtype' => 'rental',
            'status' => 'active',
            'base_amount' => 50000,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'features' => ['parking', 'garden']
        ]);

        PropertyConsolidated::factory()->create([
            'property_subtype' => 'rental',
            'status' => 'active',
            'base_amount' => 75000,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'features' => ['parking', 'gym']
        ]);

        PropertyConsolidated::factory()->create([
            'property_subtype' => 'sale',
            'status' => 'active',
            'base_amount' => 5000000,
            'bedrooms' => 2,
            'bathrooms' => 1
        ]);

        $criteria = [
            'subtype' => 'rental',
            'min_price' => 40000,
            'max_price' => 80000,
            'bedrooms' => 2,
            'features' => ['parking']
        ];

        $properties = $this->propertyRepository->advancedSearch($criteria);

        $this->assertCount(1, $properties);
        $this->assertEquals('rental', $properties->first()->property_subtype);
        $this->assertEquals(50000, $properties->first()->base_amount);
        $this->assertEquals(2, $properties->first()->bedrooms);
    }

    public function test_can_create_property()
    {
        $data = [
            'name' => 'Test Property',
            'description' => 'Test Description',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'property_subtype' => 'rental',
            'base_amount' => 50000,
            'status' => 'active',
            'is_available' => true,
            'is_vacant' => true,
        ];

        $property = $this->propertyRepository->create($data);

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('Test Property', $property->name);
        $this->assertEquals('rental', $property->property_subtype);
        $this->assertEquals(50000, $property->base_amount);
    }

    public function test_can_update_property()
    {
        $property = PropertyConsolidated::factory()->create(['name' => 'Original Name']);
        
        $updateData = ['name' => 'Updated Name'];
        $updatedProperty = $this->propertyRepository->update($property, $updateData);

        $this->assertEquals('Updated Name', $updatedProperty->name);
    }

    public function test_can_delete_property()
    {
        $property = PropertyConsolidated::factory()->create();
        
        $result = $this->propertyRepository->delete($property);

        $this->assertTrue($result);
        $this->assertSoftDeleted('properties_consolidated', ['id' => $property->id]);
    }
}
