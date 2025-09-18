<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserInvitationService
{
    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;

    public function __construct(
        UserManagementService $userService,
        RoleBasedAccessControlService $rbacService
    ) {
        $this->userService = $userService;
        $this->rbacService = $rbacService;
    }

    /**
     * Send invitation to new user
     */
    public function sendInvitation(array $data): UserInvitation
    {
        // Check if user already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            throw new \Exception('User with this email already exists');
        }

        // Check if invitation already exists and is still valid
        $existingInvitation = UserInvitation::where('email', $data['email'])
            ->where('expires_at', '>', now())
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            throw new \Exception('Invitation already sent to this email');
        }

        // Create invitation
        $invitation = UserInvitation::create([
            'email' => $data['email'],
            'name' => $data['name'] ?? null,
            'role' => $data['role'] ?? 'tenant',
            'invited_by' => auth()->id(),
            'token' => Str::random(64),
            'expires_at' => now()->addDays($data['expiry_days'] ?? 7),
            'status' => 'pending',
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Send invitation email
        $this->sendInvitationEmail($invitation);

        Log::info('User invitation sent', [
            'invitation_id' => $invitation->id,
            'email' => $invitation->email,
            'role' => $invitation->role,
            'invited_by' => auth()->id(),
        ]);

        return $invitation;
    }

    /**
     * Resend invitation
     */
    public function resendInvitation(int $invitationId): UserInvitation
    {
        $invitation = UserInvitation::findOrFail($invitationId);

        if ($invitation->status !== 'pending') {
            throw new \Exception('Cannot resend invitation that is not pending');
        }

        // Extend expiry date
        $invitation->update([
            'expires_at' => now()->addDays(7),
            'token' => Str::random(64), // Generate new token
        ]);

        // Send invitation email
        $this->sendInvitationEmail($invitation);

        Log::info('User invitation resent', [
            'invitation_id' => $invitation->id,
            'email' => $invitation->email,
        ]);

        return $invitation;
    }

    /**
     * Accept invitation and create user
     */
    public function acceptInvitation(string $token, array $userData): User
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            throw new \Exception('Invalid or expired invitation');
        }

        // Create user
        $user = $this->userService->createUser([
            'name' => $userData['name'],
            'email' => $invitation->email,
            'password' => $userData['password'],
            'role' => $invitation->role,
            'email_verified_at' => now(),
        ]);

        // Mark invitation as accepted
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accepted_by' => $user->id,
        ]);

        Log::info('User invitation accepted', [
            'invitation_id' => $invitation->id,
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;
    }

    /**
     * Reject invitation
     */
    public function rejectInvitation(string $token): UserInvitation
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            throw new \Exception('Invalid invitation');
        }

        $invitation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        Log::info('User invitation rejected', [
            'invitation_id' => $invitation->id,
            'email' => $invitation->email,
        ]);

        return $invitation;
    }

    /**
     * Cancel invitation
     */
    public function cancelInvitation(int $invitationId): UserInvitation
    {
        $invitation = UserInvitation::findOrFail($invitationId);

        if ($invitation->status !== 'pending') {
            throw new \Exception('Cannot cancel invitation that is not pending');
        }

        $invitation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        Log::info('User invitation cancelled', [
            'invitation_id' => $invitation->id,
            'email' => $invitation->email,
            'cancelled_by' => auth()->id(),
        ]);

        return $invitation;
    }

    /**
     * Get all invitations with filtering
     */
    public function getAllInvitations(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = UserInvitation::with(['invitedBy', 'acceptedBy']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['invited_by'])) {
            $query->where('invited_by', $filters['invited_by']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get invitation statistics
     */
    public function getInvitationStatistics(): array
    {
        return [
            'total_invitations' => UserInvitation::count(),
            'pending_invitations' => UserInvitation::where('status', 'pending')->count(),
            'accepted_invitations' => UserInvitation::where('status', 'accepted')->count(),
            'rejected_invitations' => UserInvitation::where('status', 'rejected')->count(),
            'cancelled_invitations' => UserInvitation::where('status', 'cancelled')->count(),
            'expired_invitations' => UserInvitation::where('expires_at', '<', now())
                ->where('status', 'pending')
                ->count(),
            'invitations_by_role' => UserInvitation::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role'),
            'recent_invitations' => UserInvitation::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Clean up expired invitations
     */
    public function cleanupExpiredInvitations(): int
    {
        $expiredInvitations = UserInvitation::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->update([
                'status' => 'expired',
                'expired_at' => now(),
            ]);

        Log::info('Expired invitations cleaned up', [
            'expired_count' => $expiredInvitations,
        ]);

        return $expiredInvitations;
    }

    /**
     * Send invitation email
     */
    protected function sendInvitationEmail(UserInvitation $invitation): void
    {
        try {
            Mail::to($invitation->email)->send(
                new \App\Mail\UserInvitationMail($invitation)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send invitation email', [
                'invitation_id' => $invitation->id,
                'email' => $invitation->email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Bulk send invitations
     */
    public function bulkSendInvitations(array $invitations): array
    {
        $results = [
            'sent' => [],
            'errors' => [],
        ];

        foreach ($invitations as $invitationData) {
            try {
                $invitation = $this->sendInvitation($invitationData);
                $results['sent'][] = $invitation;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'data' => $invitationData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get invitation by token
     */
    public function getInvitationByToken(string $token): ?UserInvitation
    {
        return UserInvitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Validate invitation token
     */
    public function validateInvitationToken(string $token): bool
    {
        $invitation = $this->getInvitationByToken($token);
        return $invitation !== null;
    }
}
