<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CommunicationService;
use App\Services\SecurityService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommunicationApiController extends Controller
{
    use ApiResponse;

    protected $communicationService;
    protected $securityService;

    public function __construct(CommunicationService $communicationService, SecurityService $securityService)
    {
        $this->communicationService = $communicationService;
        $this->securityService = $securityService;
    }

    /**
     * Send notification to user
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $this->authorize('send notifications');

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'data' => 'required|array',
            'channels' => 'nullable|array',
        ]);

        $channels = $validated['channels'] ?? ['email', 'sms', 'in_app'];

        try {
            $result = $this->communicationService->sendNotification(
                $validated['recipient_id'],
                $validated['type'],
                $validated['data'],
                $channels
            );
            return $this->successResponse($result, 'Notification sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send notification: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send payment reminders
     */
    public function sendPaymentReminders(): JsonResponse
    {
        $this->authorize('send notifications');

        try {
            $result = $this->communicationService->sendPaymentReminders();
            return $this->successResponse($result, 'Payment reminders sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send payment reminders: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send lease renewal notifications
     */
    public function sendLeaseRenewalNotifications(): JsonResponse
    {
        $this->authorize('send notifications');

        try {
            $result = $this->communicationService->sendLeaseRenewalNotifications();
            return $this->successResponse($result, 'Lease renewal notifications sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send lease renewal notifications: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send maintenance updates
     */
    public function sendMaintenanceUpdates(Request $request, $maintenanceRequestId): JsonResponse
    {
        $this->authorize('send notifications');

        try {
            $result = $this->communicationService->sendMaintenanceUpdates($maintenanceRequestId);
            return $this->successResponse($result, 'Maintenance updates sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send maintenance updates: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send property announcement
     */
    public function sendPropertyAnnouncement(Request $request, $propertyId): JsonResponse
    {
        $this->authorize('send notifications');
        $this->securityService->auditPropertyAccess($propertyId, auth()->id(), 'property_announcement_send');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'recipients' => 'nullable|in:all,tenants,landlord',
        ]);

        $recipients = $validated['recipients'] ?? 'all';

        try {
            $result = $this->communicationService->sendPropertyAnnouncement(
                $propertyId,
                [
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                ],
                $recipients
            );
            return $this->successResponse($result, 'Property announcement sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send property announcement: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get communication analytics
     */
    public function getCommunicationAnalytics(Request $request): JsonResponse
    {
        $this->authorize('view analytics');

        $period = $request->get('period', 30);

        try {
            $analytics = $this->communicationService->getCommunicationAnalytics($period);
            return $this->successResponse($analytics, 'Communication analytics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve communication analytics: ' . $e->getMessage(), 500);
        }
    }
}
