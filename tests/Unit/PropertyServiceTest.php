<?php

namespace Tests\Unit;

use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\PropertyDetail;
use App\Services\PropertyService;
use App\Repositories\PropertyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;
    protected $propertyRepository;
    protected $propertyType;
    protected $landlord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->propertyType = PropertyType::factory()->create();
        $this->landlord = User::factory()->create();
        
        // Mock the repository
        $this->propertyRepository = Mockery::mock(PropertyRepository::class);
        $this->propertyService = new PropertyService($this->propertyRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_create_rental_property()
    {
        $data = [
            'name' => 'Test Rental Property',
            'description' => 'A beautiful rental property',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'base_amount' => 50000,
            'deposit_amount' => 10000,
            'commission_rate' => 5.0,
            'bedrooms' => 2,
            'bathrooms' => 1,
        ];

        $expectedProperty = PropertyConsolidated::factory()->create($data);

        $this->propertyRepository
            ->shouldReceive('create')
            ->once()
            ->with(array_merge($data, ['property_subtype' => 'rental']))
            ->andReturn($expectedProperty);

        $property = $this->propertyService->createProperty($data, 'rental');

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('rental', $property->property_subtype);
    }

    public function test_can_create_sale_property()
    {
        $data = [
            'name' => 'Test Sale Property',
            'description' => 'A beautiful sale property',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'base_amount' => 5000000,
            'commission_rate' => 3.0,
            'bedrooms' => 3,
            'bathrooms' => 2,
        ];

        $expectedProperty = PropertyConsolidated::factory()->create($data);

        $this->propertyRepository
            ->shouldReceive('create')
            ->once()
            ->with(array_merge($data, ['property_subtype' => 'sale']))
            ->andReturn($expectedProperty);

        $property = $this->propertyService->createProperty($data, 'sale');

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('sale', $property->property_subtype);
    }

    public function test_can_update_property()
    {
        $property = PropertyConsolidated::factory()->create();
        $updateData = [
            'name' => 'Updated Property Name',
            'base_amount' => 60000,
        ];

        $updatedProperty = PropertyConsolidated::factory()->create(array_merge($property->toArray(), $updateData));

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, $updateData)
            ->andReturn($updatedProperty);

        $result = $this->propertyService->updateProperty($property, $updateData);

        $this->assertInstanceOf(PropertyConsolidated::class, $result);
        $this->assertEquals('Updated Property Name', $result->name);
    }

    public function test_can_delete_property()
    {
        $property = PropertyConsolidated::factory()->create();

        $this->propertyRepository
            ->shouldReceive('softDelete')
            ->once()
            ->with($property)
            ->andReturn(true);

        $result = $this->propertyService->deleteProperty($property);

        $this->assertTrue($result);
    }

    public function test_can_search_properties()
    {
        $filters = [
            'subtype' => 'rental',
            'min_price' => 30000,
            'max_price' => 100000,
            'bedrooms' => 2,
        ];

        $expectedProperties = PropertyConsolidated::factory()->count(3)->create();

        $this->propertyRepository
            ->shouldReceive('advancedSearch')
            ->once()
            ->with($filters)
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->searchProperties($filters);

        $this->assertCount(3, $properties);
    }

    public function test_can_get_properties_by_type()
    {
        $expectedProperties = PropertyConsolidated::factory()->count(2)->create(['property_subtype' => 'rental']);

        $this->propertyRepository
            ->shouldReceive('findByType')
            ->once()
            ->with('rental')
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->getPropertiesByType('rental');

        $this->assertCount(2, $properties);
    }

    public function test_can_get_available_properties()
    {
        $expectedProperties = PropertyConsolidated::factory()->count(3)->create(['is_available' => true]);

        $this->propertyRepository
            ->shouldReceive('findAvailable')
            ->once()
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->getAvailableProperties();

        $this->assertCount(3, $properties);
    }

    public function test_can_get_vacant_properties()
    {
        $expectedProperties = PropertyConsolidated::factory()->count(2)->create(['is_vacant' => true]);

        $this->propertyRepository
            ->shouldReceive('findVacant')
            ->once()
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->getVacantProperties();

        $this->assertCount(2, $properties);
    }

    public function test_can_get_featured_properties()
    {
        $expectedProperties = PropertyConsolidated::factory()->count(1)->create(['is_featured' => true]);

        $this->propertyRepository
            ->shouldReceive('findFeatured')
            ->once()
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->getFeaturedProperties();

        $this->assertCount(1, $properties);
    }

    public function test_can_get_properties_by_landlord()
    {
        $landlordId = $this->landlord->id;
        $expectedProperties = PropertyConsolidated::factory()->count(2)->create(['landlord_id' => $landlordId]);

        $this->propertyRepository
            ->shouldReceive('findByLandlord')
            ->once()
            ->with($landlordId)
            ->andReturn($expectedProperties);

        $properties = $this->propertyService->getPropertiesByLandlord($landlordId);

        $this->assertCount(2, $properties);
    }

    public function test_can_get_property_statistics()
    {
        $expectedStats = [
            'total_properties' => 10,
            'rental_properties' => 6,
            'sale_properties' => 3,
            'lease_properties' => 1,
            'active_properties' => 8,
            'vacant_properties' => 5,
        ];

        $this->propertyRepository
            ->shouldReceive('getStatistics')
            ->once()
            ->andReturn($expectedStats);

        $statistics = $this->propertyService->getPropertyStatistics();

        $this->assertEquals(10, $statistics['total_properties']);
        $this->assertEquals(6, $statistics['rental_properties']);
        $this->assertEquals(3, $statistics['sale_properties']);
    }

    public function test_can_toggle_property_status()
    {
        $property = PropertyConsolidated::factory()->create(['status' => 'active']);
        $updatedProperty = PropertyConsolidated::factory()->create(['status' => 'inactive']);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['status' => 'inactive'])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->togglePropertyStatus($property, 'inactive');

        $this->assertEquals('inactive', $result->status);
    }

    public function test_can_toggle_property_availability()
    {
        $property = PropertyConsolidated::factory()->create(['is_available' => true]);
        $updatedProperty = PropertyConsolidated::factory()->create(['is_available' => false]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['is_available' => false])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->togglePropertyAvailability($property);

        $this->assertFalse($result->is_available);
    }

    public function test_can_toggle_property_vacancy()
    {
        $property = PropertyConsolidated::factory()->create(['is_vacant' => true]);
        $updatedProperty = PropertyConsolidated::factory()->create(['is_vacant' => false]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['is_vacant' => false])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->togglePropertyVacancy($property);

        $this->assertFalse($result->is_vacant);
    }

    public function test_can_toggle_property_featured()
    {
        $property = PropertyConsolidated::factory()->create(['is_featured' => false]);
        $updatedProperty = PropertyConsolidated::factory()->create(['is_featured' => true]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['is_featured' => true])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->togglePropertyFeatured($property);

        $this->assertTrue($result->is_featured);
    }

    public function test_can_toggle_property_published()
    {
        $property = PropertyConsolidated::factory()->create(['is_published' => false]);
        $updatedProperty = PropertyConsolidated::factory()->create(['is_published' => true]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['is_published' => true, 'published_at' => Mockery::type('Illuminate\Support\Carbon')])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->togglePropertyPublished($property);

        $this->assertTrue($result->is_published);
    }

    public function test_can_increment_property_views()
    {
        $property = PropertyConsolidated::factory()->create(['views_count' => 5]);
        $updatedProperty = PropertyConsolidated::factory()->create(['views_count' => 6]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['views_count' => 6])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->incrementPropertyViews($property);

        $this->assertEquals(6, $result->views_count);
    }

    public function test_can_increment_property_inquiries()
    {
        $property = PropertyConsolidated::factory()->create(['inquiries_count' => 3]);
        $updatedProperty = PropertyConsolidated::factory()->create(['inquiries_count' => 4]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['inquiries_count' => 4])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->incrementPropertyInquiries($property);

        $this->assertEquals(4, $result->inquiries_count);
    }

    public function test_can_increment_property_applications()
    {
        $property = PropertyConsolidated::factory()->create(['applications_count' => 2]);
        $updatedProperty = PropertyConsolidated::factory()->create(['applications_count' => 3]);

        $this->propertyRepository
            ->shouldReceive('update')
            ->once()
            ->with($property, ['applications_count' => 3])
            ->andReturn($updatedProperty);

        $result = $this->propertyService->incrementPropertyApplications($property);

        $this->assertEquals(3, $result->applications_count);
    }

    public function test_throws_exception_for_invalid_property_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property type: invalid_type');

        $data = ['name' => 'Test Property'];
        $this->propertyService->createProperty($data, 'invalid_type');
    }

    public function test_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid status: invalid_status');

        $property = PropertyConsolidated::factory()->create();
        $this->propertyService->togglePropertyStatus($property, 'invalid_status');
    }
}
