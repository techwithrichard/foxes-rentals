<?php

namespace Tests\Feature\Api;

use App\Models\Property;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PropertyApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $landlord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        // Create landlord user
        $this->landlord = User::factory()->create();
        $this->landlord->assignRole('landlord');
        
        // Authenticate as admin
        Sanctum::actingAs($this->admin);
    }

    /** @test */
    public function it_can_get_all_properties()
    {
        // Create test properties
        Property::factory()->count(3)->create();

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'type',
                        'rent',
                        'status',
                        'landlord_id',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'timestamp'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Properties retrieved successfully'
            ]);
    }

    /** @test */
    public function it_can_get_single_property()
    {
        $property = Property::factory()->create();

        $response = $this->getJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'type',
                    'rent',
                    'status',
                    'landlord_id',
                ],
                'timestamp'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Property retrieved successfully',
                'data' => [
                    'id' => $property->id,
                    'name' => $property->name,
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_property()
    {
        $response = $this->getJson('/api/properties/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Property not found'
            ]);
    }

    /** @test */
    public function it_can_create_property()
    {
        $propertyData = [
            'name' => 'Test Property',
            'description' => 'A test property description',
            'type' => 'house',
            'rent' => 1500.00,
            'deposit' => 3000.00,
            'landlord_id' => $this->landlord->id,
            'commission' => 10,
            'status' => 'active',
            'is_vacant' => true,
            'electricity_id' => 'EL123456',
            'address' => [
                'street' => '123 Test Street',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'country' => 'Test Country',
            ]
        ];

        $response = $this->postJson('/api/properties', $propertyData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'type',
                    'rent',
                    'status',
                    'landlord_id',
                ],
                'timestamp'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => [
                    'name' => 'Test Property',
                    'type' => 'house',
                    'rent' => 1500.00,
                ]
            ]);

        $this->assertDatabaseHas('properties', [
            'name' => 'Test Property',
            'type' => 'house',
            'rent' => 1500.00,
            'landlord_id' => $this->landlord->id,
        ]);
    }

    /** @test */
    public function it_validates_property_creation_data()
    {
        $response = $this->postJson('/api/properties', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'name',
                    'type',
                    'rent',
                    'landlord_id',
                    'status',
                ],
                'timestamp'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ]);
    }

    /** @test */
    public function it_can_update_property()
    {
        $property = Property::factory()->create([
            'landlord_id' => $this->landlord->id,
        ]);

        $updateData = [
            'name' => 'Updated Property Name',
            'description' => 'Updated description',
            'type' => 'apartment',
            'rent' => 2000.00,
            'deposit' => 4000.00,
            'landlord_id' => $this->landlord->id,
            'commission' => 15,
            'status' => 'active',
            'is_vacant' => false,
        ];

        $response = $this->putJson("/api/properties/{$property->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => [
                    'name' => 'Updated Property Name',
                    'rent' => 2000.00,
                ]
            ]);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'Updated Property Name',
            'rent' => 2000.00,
        ]);
    }

    /** @test */
    public function it_can_delete_property()
    {
        $property = Property::factory()->create();

        $response = $this->deleteJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Property deleted successfully'
            ]);

        $this->assertSoftDeleted('properties', [
            'id' => $property->id,
        ]);
    }

    /** @test */
    public function it_can_get_property_statistics()
    {
        // Create test data
        Property::factory()->create(['status' => 'active', 'is_vacant' => true]);
        Property::factory()->create(['status' => 'active', 'is_vacant' => false]);
        Property::factory()->create(['status' => 'inactive', 'is_vacant' => true]);

        $response = $this->getJson('/api/properties/statistics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_properties',
                    'active_properties',
                    'vacant_properties',
                    'occupied_properties',
                    'properties_by_type',
                    'properties_by_status',
                ],
                'timestamp'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Property statistics retrieved successfully'
            ]);
    }

    /** @test */
    public function it_can_filter_properties_by_status()
    {
        Property::factory()->create(['status' => 'active']);
        Property::factory()->create(['status' => 'inactive']);

        $response = $this->getJson('/api/properties?status=active');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('active', $data[0]['status']);
    }

    /** @test */
    public function it_can_search_properties()
    {
        Property::factory()->create(['name' => 'Beautiful House']);
        Property::factory()->create(['name' => 'Modern Apartment']);

        $response = $this->getJson('/api/properties?search=Beautiful');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Beautiful House', $data[0]['name']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/properties');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_proper_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/properties');

        $response->assertStatus(403);
    }
}
