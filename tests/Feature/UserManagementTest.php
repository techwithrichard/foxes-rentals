<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use App\Services\PermissionManagementService;
use App\Services\UserActivityService;
use App\Services\PasswordSecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;
    protected PermissionManagementService $permissionService;
    protected UserActivityService $activityService;
    protected PasswordSecurityService $passwordService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userService = app(UserManagementService::class);
        $this->rbacService = app(RoleBasedAccessControlService::class);
        $this->permissionService = app(PermissionManagementService::class);
        $this->activityService = app(UserActivityService::class);
        $this->passwordService = app(PasswordSecurityService::class);
    }

    /** @test */
    public function it_can_create_user_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePassword123!',
            'role' => 'tenant',
        ];

        $user = $this->userService->createUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue($user->hasRole('tenant'));
    }

    /** @test */
    public function it_can_update_user_information()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+1234567890',
        ];

        $updatedUser = $this->userService->updateUser($user->id, $updateData);

        $this->assertEquals('Jane Doe', $updatedUser->name);
        $this->assertEquals('jane@example.com', $updatedUser->email);
        $this->assertEquals('+1234567890', $updatedUser->phone);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');

        $result = $this->userService->deleteUser($user->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_can_restore_deleted_user()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');
        $user->delete();

        $restoredUser = $this->userService->restoreUser($user->id);

        $this->assertInstanceOf(User::class, $restoredUser);
        $this->assertFalse($restoredUser->trashed());
    }

    /** @test */
    public function it_can_toggle_user_status()
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('tenant');

        $updatedUser = $this->userService->toggleUserStatus($user->id);

        $this->assertFalse($updatedUser->is_active);
    }

    /** @test */
    public function it_can_reset_user_password()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');

        $updatedUser = $this->userService->resetUserPassword($user->id, 'NewPassword123!');

        $this->assertTrue(Hash::check('NewPassword123!', $updatedUser->password));
        $this->assertNotNull($updatedUser->password_changed_at);
    }

    /** @test */
    public function it_can_get_users_by_role()
    {
        User::factory()->count(3)->create()->each(function ($user) {
            $user->assignRole('tenant');
        });

        User::factory()->count(2)->create()->each(function ($user) {
            $user->assignRole('landlord');
        });

        $tenantUsers = $this->userService->getUsersByRole('tenant');
        $landlordUsers = $this->userService->getUsersByRole('landlord');

        $this->assertCount(3, $tenantUsers);
        $this->assertCount(2, $landlordUsers);
    }

    /** @test */
    public function it_can_get_user_statistics()
    {
        User::factory()->count(5)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $statistics = $this->userService->getUserStatistics();

        $this->assertEquals(7, $statistics['total_users']);
        $this->assertEquals(5, $statistics['active_users']);
        $this->assertEquals(2, $statistics['inactive_users']);
    }

    /** @test */
    public function it_can_perform_bulk_actions()
    {
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $results = $this->userService->bulkAction($userIds, 'activate');

        $this->assertCount(3, $results);
        $this->assertArrayHasKey($userIds[0], $results);
        $this->assertEquals('activated', $results[$userIds[0]]);
    }

    /** @test */
    public function it_can_check_user_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $user->givePermissionTo('view_users');

        $this->assertTrue($this->rbacService->hasPermission($user, 'view_users'));
        $this->assertFalse($this->rbacService->hasPermission($user, 'delete_users'));
    }

    /** @test */
    public function it_can_check_user_roles()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($this->rbacService->hasRole($user, 'admin'));
        $this->assertTrue($this->rbacService->hasAnyRole($user, ['admin', 'manager']));
        $this->assertFalse($this->rbacService->hasRole($user, 'tenant'));
    }

    /** @test */
    public function it_can_create_custom_role()
    {
        $role = $this->rbacService->createCustomRole('custom_role', ['view_users'], [
            'display_name' => 'Custom Role',
            'description' => 'A custom role for testing',
            'color' => '#ff0000',
            'icon' => 'fas fa-star',
        ]);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('custom_role', $role->name);
        $this->assertEquals('Custom Role', $role->display_name);
    }

    /** @test */
    public function it_can_manage_role_hierarchy()
    {
        $manager = User::factory()->create();
        $manager->assignRole('admin');

        $target = User::factory()->create();
        $target->assignRole('tenant');

        $this->assertTrue($this->rbacService->canManageUser($manager, $target));
        $this->assertFalse($this->rbacService->canManageUser($target, $manager));
    }

    /** @test */
    public function it_can_log_user_activities()
    {
        $user = User::factory()->create();

        $activity = $this->activityService->logActivity(
            $user,
            'test_action',
            'Test activity description'
        );

        $this->assertInstanceOf(\App\Models\UserActivity::class, $activity);
        $this->assertEquals($user->id, $activity->user_id);
        $this->assertEquals('test_action', $activity->action);
    }

    /** @test */
    public function it_can_log_login_activity()
    {
        $user = User::factory()->create();
        $request = request();

        $activity = $this->activityService->logLogin($user, $request);

        $this->assertEquals('login', $activity->action);
        $this->assertEquals('User logged in successfully', $activity->description);
    }

    /** @test */
    public function it_can_log_profile_update_activity()
    {
        $user = User::factory()->create();
        $updatedFields = ['name', 'email'];

        $activity = $this->activityService->logProfileUpdate($user, $updatedFields);

        $this->assertEquals('profile_updated', $activity->action);
        $this->assertEquals('User profile updated', $activity->description);
    }

    /** @test */
    public function it_can_get_user_activities()
    {
        $user = User::factory()->create();
        
        // Create some activities
        $this->activityService->logActivity($user, 'action1', 'Description 1');
        $this->activityService->logActivity($user, 'action2', 'Description 2');

        $activities = $this->activityService->getUserActivities($user->id, 10);

        $this->assertCount(2, $activities->items());
    }

    /** @test */
    public function it_can_validate_password_complexity()
    {
        $errors = $this->passwordService->checkPasswordComplexity('weak');
        
        $this->assertNotEmpty($errors);
        $this->assertContains('Password must be at least 8 characters long.', $errors);
    }

    /** @test */
    public function it_can_check_password_history()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');

        // Set initial password
        $this->passwordService->updatePassword($user, 'Password123!');

        // Try to set the same password again
        $this->expectException(\Exception::class);
        $this->passwordService->updatePassword($user, 'Password123!');
    }

    /** @test */
    public function it_can_generate_secure_password()
    {
        $password = $this->passwordService->generateSecurePassword(12);

        $this->assertEquals(12, strlen($password));
        
        $errors = $this->passwordService->checkPasswordComplexity($password);
        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_can_check_password_expiry()
    {
        $user = User::factory()->create([
            'password_changed_at' => now()->subDays(100)
        ]);

        $this->assertTrue($this->passwordService->isPasswordExpired($user));
    }

    /** @test */
    public function it_can_get_password_strength()
    {
        $strength = $this->passwordService->getPasswordStrength('WeakPassword123!');

        $this->assertArrayHasKey('score', $strength);
        $this->assertArrayHasKey('strength', $strength);
        $this->assertArrayHasKey('feedback', $strength);
    }

    /** @test */
    public function it_can_get_users_with_expired_passwords()
    {
        User::factory()->create(['password_changed_at' => now()->subDays(100)]);
        User::factory()->create(['password_changed_at' => now()->subDays(10)]);

        $expiredUsers = $this->passwordService->getUsersWithExpiredPasswords();

        $this->assertCount(1, $expiredUsers);
    }

    /** @test */
    public function it_can_force_password_reset()
    {
        $user = User::factory()->create();
        $user->assignRole('tenant');

        $newPassword = $this->passwordService->forcePasswordReset($user);

        $this->assertNotEmpty($newPassword);
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    /** @test */
    public function it_can_get_password_security_statistics()
    {
        User::factory()->count(3)->create(['password_changed_at' => now()->subDays(10)]);
        User::factory()->count(2)->create(['password_changed_at' => null]);

        $statistics = $this->passwordService->getPasswordSecurityStatistics();

        $this->assertArrayHasKey('total_users', $statistics);
        $this->assertArrayHasKey('expired_passwords', $statistics);
        $this->assertArrayHasKey('never_changed', $statistics);
    }
}

