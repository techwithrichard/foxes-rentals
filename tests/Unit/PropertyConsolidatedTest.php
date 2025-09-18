<?php

namespace Tests\Unit;

use App\Models\PropertyConsolidated;
use App\Models\PropertyDetail;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyConsolidatedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->propertyType = PropertyType::factory()->create();
        $this->landlord = User::factory()->create();
    }

    public function test_can_create_rental_property()
    {
        $property = PropertyConsolidated::create([
            'name' => 'Test Rental Property',
            'description' => 'A beautiful rental property',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'property_subtype' => 'rental',
            'base_amount' => 50000,
            'deposit_amount' => 10000,
            'commission_rate' => 5.0,
            'status' => 'active',
            'is_available' => true,
            'is_vacant' => true,
            'bedrooms' => 2,
            'bathrooms' => 1,
        ]);

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('rental', $property->property_subtype);
        $this->assertEquals(50000, $property->base_amount);
        $this->assertEquals(10000, $property->deposit_amount);
        $this->assertEquals(5.0, $property->commission_rate);
        $this->assertTrue($property->isRental());
        $this->assertFalse($property->isSale());
        $this->assertFalse($property->isLease());
    }

    public function test_can_create_sale_property()
    {
        $property = PropertyConsolidated::create([
            'name' => 'Test Sale Property',
            'description' => 'A beautiful sale property',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'property_subtype' => 'sale',
            'base_amount' => 5000000,
            'commission_rate' => 3.0,
            'status' => 'active',
            'is_available' => true,
            'bedrooms' => 3,
            'bathrooms' => 2,
        ]);

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('sale', $property->property_subtype);
        $this->assertEquals(5000000, $property->base_amount);
        $this->assertFalse($property->isRental());
        $this->assertTrue($property->isSale());
        $this->assertFalse($property->isLease());
    }

    public function test_can_create_lease_property()
    {
        $property = PropertyConsolidated::create([
            'name' => 'Test Lease Property',
            'description' => 'A beautiful lease property',
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
            'property_subtype' => 'lease',
            'base_amount' => 75000,
            'deposit_amount' => 15000,
            'commission_rate' => 4.0,
            'status' => 'active',
            'is_available' => true,
            'bedrooms' => 4,
            'bathrooms' => 3,
        ]);

        $this->assertInstanceOf(PropertyConsolidated::class, $property);
        $this->assertEquals('lease', $property->property_subtype);
        $this->assertEquals(75000, $property->base_amount);
        $this->assertFalse($property->isRental());
        $this->assertFalse($property->isSale());
        $this->assertTrue($property->isLease());
    }

    public function test_property_scopes()
    {
        // Create properties of different types
        PropertyConsolidated::factory()->create(['property_subtype' => 'rental', 'status' => 'active']);
        PropertyConsolidated::factory()->create(['property_subtype' => 'sale', 'status' => 'active']);
        PropertyConsolidated::factory()->create(['property_subtype' => 'lease', 'status' => 'inactive']);

        // Test scopes
        $this->assertEquals(1, PropertyConsolidated::rental()->count());
        $this->assertEquals(1, PropertyConsolidated::sale()->count());
        $this->assertEquals(1, PropertyConsolidated::lease()->count());
        $this->assertEquals(2, PropertyConsolidated::active()->count());
        $this->assertEquals(1, PropertyConsolidated::where('status', 'inactive')->count());
    }

    public function test_property_relationships()
    {
        $property = PropertyConsolidated::factory()->create([
            'property_type_id' => $this->propertyType->id,
            'landlord_id' => $this->landlord->id,
        ]);

        // Test relationships
        $this->assertInstanceOf(PropertyType::class, $property->propertyType);
        $this->assertInstanceOf(User::class, $property->landlord);
        $this->assertEquals($this->propertyType->id, $property->propertyType->id);
        $this->assertEquals($this->landlord->id, $property->landlord->id);
    }

    public function test_property_details_relationship()
    {
        $property = PropertyConsolidated::factory()->create(['property_subtype' => 'rental']);

        // Create property details
        $detail = PropertyDetail::create([
            'property_id' => $property->id,
            'detail_type' => 'rental',
            'detail_data' => [
                'minimum_lease_period' => 12,
                'maximum_lease_period' => 24,
                'utilities_included' => ['water', 'electricity'],
            ],
        ]);

        $this->assertInstanceOf(PropertyDetail::class, $property->rentalDetails);
        $this->assertEquals('rental', $property->rentalDetails->detail_type);
        $this->assertIsArray($property->rentalDetails->detail_data);
        $this->assertEquals(12, $property->rentalDetails->detail_data['minimum_lease_period']);
    }

    public function test_property_accessors()
    {
        $rentalProperty = PropertyConsolidated::factory()->create([
            'property_subtype' => 'rental',
            'base_amount' => 50000,
        ]);

        $saleProperty = PropertyConsolidated::factory()->create([
            'property_subtype' => 'sale',
            'base_amount' => 5000000,
        ]);

        // Test accessors
        $this->assertEquals(50000, $rentalProperty->rent_amount);
        $this->assertNull($rentalProperty->sale_price);
        $this->assertNull($rentalProperty->lease_amount);

        $this->assertEquals(5000000, $saleProperty->sale_price);
        $this->assertNull($saleProperty->rent_amount);
        $this->assertNull($saleProperty->lease_amount);

        // Test formatted amount
        $this->assertEquals('KSh 50,000.00', $rentalProperty->formatted_amount);
        $this->assertEquals('KSh 5,000,000.00', $saleProperty->formatted_amount);
    }

    public function test_property_detail_data_methods()
    {
        $property = PropertyConsolidated::factory()->create(['property_subtype' => 'rental']);

        $detailData = [
            'minimum_lease_period' => 12,
            'maximum_lease_period' => 24,
            'utilities_included' => ['water', 'electricity'],
        ];

        // Test setting detail data
        $property->setDetailData('rental', $detailData);

        // Test getting detail data
        $retrievedData = $property->getDetailData('rental');
        $this->assertEquals($detailData, $retrievedData);
        $this->assertEquals(12, $retrievedData['minimum_lease_period']);
        $this->assertEquals(24, $retrievedData['maximum_lease_period']);
        $this->assertContains('water', $retrievedData['utilities_included']);
    }

    public function test_property_casts()
    {
        $property = PropertyConsolidated::factory()->create([
            'features' => ['parking', 'garden', 'security'],
            'images' => ['image1.jpg', 'image2.jpg'],
            'base_amount' => 50000.50,
            'deposit_amount' => 10000.25,
            'commission_rate' => 5.75,
            'is_available' => true,
            'is_vacant' => false,
        ]);

        // Test array casts
        $this->assertIsArray($property->features);
        $this->assertIsArray($property->images);
        $this->assertContains('parking', $property->features);

        // Test decimal casts
        $this->assertIsFloat($property->base_amount);
        $this->assertEquals(50000.50, $property->base_amount);

        // Test boolean casts
        $this->assertTrue($property->is_available);
        $this->assertFalse($property->is_vacant);
    }
}
