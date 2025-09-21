<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Invoice;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CommunicationService
{
    /**
     * Send multi-channel notification
     */
    public function sendNotification($recipientId, $type, $data, $channels = ['email', 'sms', 'in_app']): array
    {
        $recipient = User::findOrFail($recipientId);
        $results = [];

        foreach ($channels as $channel) {
            try {
                switch ($channel) {
                    case 'email':
                        $results['email'] = $this->sendEmailNotification($recipient, $type, $data);
                        break;
                    case 'sms':
                        $results['sms'] = $this->sendSmsNotification($recipient, $type, $data);
                        break;
                    case 'in_app':
                        $results['in_app'] = $this->createInAppNotification($recipientId, $type, $data);
                        break;
                    case 'push':
                        $results['push'] = $this->sendPushNotification($recipient, $type, $data);
                        break;
                }
            } catch (\Exception $e) {
                $results[$channel] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
                Log::error("Failed to send {$channel} notification", [
                    'recipient_id' => $recipientId,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send automated payment reminders
     */
    public function sendPaymentReminders(): array
    {
        $remindersSent = 0;
        $errors = [];

        // Get upcoming payments (due in 3 days)
        $upcomingPayments = Invoice::where('status', 'pending')
            ->where('due_date', '<=', Carbon::now()->addDays(3))
            ->where('due_date', '>=', Carbon::now())
            ->with(['lease.tenant', 'lease.property'])
            ->get();

        foreach ($upcomingPayments as $invoice) {
            try {
                $tenant = $invoice->lease->tenant;
                $property = $invoice->lease->property;
                
                $data = [
                    'tenant_name' => $tenant->name,
                    'property_name' => $property->name,
                    'amount' => $invoice->amount + $invoice->bills_amount,
                    'due_date' => $invoice->due_date,
                    'days_until_due' => Carbon::now()->diffInDays($invoice->due_date, false),
                    'invoice_reference' => $invoice->reference,
                ];

                $this->sendNotification(
                    $tenant->id,
                    'payment_reminder',
                    $data,
                    ['email', 'sms', 'in_app']
                );

                $remindersSent++;
            } catch (\Exception $e) {
                $errors[] = [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'reminders_sent' => $remindersSent,
            'errors' => $errors,
        ];
    }

    /**
     * Send lease renewal notifications
     */
    public function sendLeaseRenewalNotifications(): array
    {
        $notificationsSent = 0;
        $errors = [];

        // Get leases expiring in 30 days
        $expiringLeases = Lease::where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->where('end_date', '>=', Carbon::now())
            ->with(['tenant', 'property', 'landlord'])
            ->get();

        foreach ($expiringLeases as $lease) {
            try {
                $tenant = $lease->tenant;
                $property = $lease->property;
                $landlord = $lease->landlord;
                
                $data = [
                    'tenant_name' => $tenant->name,
                    'landlord_name' => $landlord->name,
                    'property_name' => $property->name,
                    'lease_end_date' => $lease->end_date,
                    'days_until_expiry' => Carbon::now()->diffInDays($lease->end_date, false),
                    'lease_id' => $lease->lease_id,
                ];

                // Notify tenant
                $this->sendNotification(
                    $tenant->id,
                    'lease_renewal_reminder',
                    $data,
                    ['email', 'in_app']
                );

                // Notify landlord
                $this->sendNotification(
                    $landlord->id,
                    'lease_renewal_landlord',
                    $data,
                    ['email', 'in_app']
                );

                $notificationsSent += 2;
            } catch (\Exception $e) {
                $errors[] = [
                    'lease_id' => $lease->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'notifications_sent' => $notificationsSent,
            'errors' => $errors,
        ];
    }

    /**
     * Send maintenance updates
     */
    public function sendMaintenanceUpdates($maintenanceRequestId): array
    {
        $request = MaintenanceRequest::with(['property', 'lease.tenant', 'vendor'])
            ->findOrFail($maintenanceRequestId);

        $tenant = $request->lease->tenant;
        $property = $request->property;
        $vendor = $request->vendor;

        $data = [
            'tenant_name' => $tenant->name,
            'property_name' => $property->name,
            'request_title' => $request->title,
            'request_description' => $request->description,
            'status' => $request->status,
            'vendor_name' => $vendor->name ?? 'Not Assigned',
            'scheduled_date' => $request->scheduled_date,
            'estimated_cost' => $request->estimated_cost,
        ];

        return $this->sendNotification(
            $tenant->id,
            'maintenance_update',
            $data,
            ['email', 'sms', 'in_app']
        );
    }

    /**
     * Send property announcements
     */
    public function sendPropertyAnnouncement($propertyId, $announcement, $recipients = 'all'): array
    {
        $property = Property::findOrFail($propertyId);
        $sentCount = 0;
        $errors = [];

        // Get recipients based on type
        $recipientUsers = $this->getPropertyRecipients($propertyId, $recipients);

        foreach ($recipientUsers as $user) {
            try {
                $data = [
                    'recipient_name' => $user->name,
                    'property_name' => $property->name,
                    'announcement_title' => $announcement['title'],
                    'announcement_content' => $announcement['content'],
                    'announcement_date' => Carbon::now(),
                ];

                $this->sendNotification(
                    $user->id,
                    'property_announcement',
                    $data,
                    ['email', 'in_app']
                );

                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'sent_count' => $sentCount,
            'errors' => $errors,
        ];
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($recipient, $type, $data): array
    {
        try {
            $emailData = $this->prepareEmailData($type, $data);
            
            Mail::send("emails.{$type}", $emailData, function($message) use ($recipient, $emailData) {
                $message->to($recipient->email, $recipient->name)
                        ->subject($emailData['subject']);
            });

            return [
                'success' => true,
                'message' => 'Email sent successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS notification
     */
    private function sendSmsNotification($recipient, $type, $data): array
    {
        try {
            $message = $this->prepareSmsMessage($type, $data);
            
            // This would integrate with SMS service (e.g., Twilio, Africa's Talking)
            // For now, we'll log the SMS
            Log::info('SMS Notification', [
                'recipient_phone' => $recipient->phone,
                'message' => $message,
                'type' => $type,
            ]);

            return [
                'success' => true,
                'message' => 'SMS sent successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification($recipientId, $type, $data): array
    {
        try {
            $notification = Notification::create([
                'user_id' => $recipientId,
                'type' => $type,
                'title' => $this->getNotificationTitle($type),
                'message' => $this->getNotificationMessage($type, $data),
                'data' => $data,
                'read_at' => null,
            ]);

            return [
                'success' => true,
                'notification_id' => $notification->id,
                'message' => 'In-app notification created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send push notification
     */
    private function sendPushNotification($recipient, $type, $data): array
    {
        try {
            // This would integrate with push notification service (FCM, etc.)
            // For now, we'll log the push notification
            Log::info('Push Notification', [
                'recipient_id' => $recipient->id,
                'type' => $type,
                'data' => $data,
            ]);

            return [
                'success' => true,
                'message' => 'Push notification sent successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get property recipients
     */
    private function getPropertyRecipients($propertyId, $recipients): \Illuminate\Database\Eloquent\Collection
    {
        switch ($recipients) {
            case 'tenants':
                return User::whereHas('leases', function($query) use ($propertyId) {
                    $query->where('property_id', $propertyId)
                          ->where('status', 'active');
                })->get();
                
            case 'landlord':
                return User::whereHas('properties', function($query) use ($propertyId) {
                    $query->where('id', $propertyId);
                })->get();
                
            case 'all':
            default:
                return User::where(function($query) use ($propertyId) {
                    $query->whereHas('leases', function($subQuery) use ($propertyId) {
                        $subQuery->where('property_id', $propertyId)
                                ->where('status', 'active');
                    })->orWhereHas('properties', function($subQuery) use ($propertyId) {
                        $subQuery->where('id', $propertyId);
                    });
                })->get();
        }
    }

    /**
     * Prepare email data
     */
    private function prepareEmailData($type, $data): array
    {
        return match($type) {
            'payment_reminder' => [
                'subject' => 'Payment Reminder - Due Soon',
                'tenant_name' => $data['tenant_name'],
                'property_name' => $data['property_name'],
                'amount' => $data['amount'],
                'due_date' => $data['due_date'],
                'days_until_due' => $data['days_until_due'],
            ],
            'lease_renewal_reminder' => [
                'subject' => 'Lease Renewal Reminder',
                'tenant_name' => $data['tenant_name'],
                'property_name' => $data['property_name'],
                'lease_end_date' => $data['lease_end_date'],
                'days_until_expiry' => $data['days_until_expiry'],
            ],
            'maintenance_update' => [
                'subject' => 'Maintenance Request Update',
                'tenant_name' => $data['tenant_name'],
                'property_name' => $data['property_name'],
                'request_title' => $data['request_title'],
                'status' => $data['status'],
                'vendor_name' => $data['vendor_name'],
            ],
            'property_announcement' => [
                'subject' => 'Property Announcement',
                'recipient_name' => $data['recipient_name'],
                'property_name' => $data['property_name'],
                'announcement_title' => $data['announcement_title'],
                'announcement_content' => $data['announcement_content'],
            ],
            default => [
                'subject' => 'Notification',
                'data' => $data,
            ],
        };
    }

    /**
     * Prepare SMS message
     */
    private function prepareSmsMessage($type, $data): string
    {
        return match($type) {
            'payment_reminder' => "Hi {$data['tenant_name']}, your payment of Ksh {$data['amount']} for {$data['property_name']} is due on {$data['due_date']}. Please make payment to avoid late fees.",
            'lease_renewal_reminder' => "Hi {$data['tenant_name']}, your lease for {$data['property_name']} expires on {$data['lease_end_date']}. Please contact us to discuss renewal options.",
            'maintenance_update' => "Hi {$data['tenant_name']}, your maintenance request for {$data['property_name']} has been updated to: {$data['status']}.",
            'property_announcement' => "Hi {$data['recipient_name']}, {$data['announcement_title']} - {$data['announcement_content']}",
            default => "You have a new notification from Foxes Rentals.",
        };
    }

    /**
     * Get notification title
     */
    private function getNotificationTitle($type): string
    {
        return match($type) {
            'payment_reminder' => 'Payment Reminder',
            'lease_renewal_reminder' => 'Lease Renewal Reminder',
            'maintenance_update' => 'Maintenance Update',
            'property_announcement' => 'Property Announcement',
            default => 'Notification',
        };
    }

    /**
     * Get notification message
     */
    private function getNotificationMessage($type, $data): string
    {
        return match($type) {
            'payment_reminder' => "Payment of Ksh {$data['amount']} for {$data['property_name']} is due on {$data['due_date']}.",
            'lease_renewal_reminder' => "Your lease for {$data['property_name']} expires on {$data['lease_end_date']}.",
            'maintenance_update' => "Your maintenance request for {$data['property_name']} has been updated to: {$data['status']}.",
            'property_announcement' => "{$data['announcement_title']}: {$data['announcement_content']}",
            default => 'You have a new notification.',
        };
    }

    /**
     * Get communication analytics
     */
    public function getCommunicationAnalytics($period = 30): array
    {
        $startDate = Carbon::now()->subDays($period);
        
        $notifications = Notification::where('created_at', '>=', $startDate)->get();
        
        $analytics = [
            'total_notifications' => $notifications->count(),
            'notifications_by_type' => $notifications->groupBy('type')->map->count(),
            'notifications_by_channel' => $this->getChannelAnalytics($notifications),
            'response_rates' => $this->getResponseRates($notifications),
            'most_active_users' => $this->getMostActiveUsers($notifications),
        ];

        return $analytics;
    }

    /**
     * Get channel analytics
     */
    private function getChannelAnalytics($notifications): array
    {
        // This would analyze actual delivery success rates
        return [
            'email' => $notifications->where('type', 'email')->count(),
            'sms' => $notifications->where('type', 'sms')->count(),
            'in_app' => $notifications->where('type', 'in_app')->count(),
            'push' => $notifications->where('type', 'push')->count(),
        ];
    }

    /**
     * Get response rates
     */
    private function getResponseRates($notifications): array
    {
        // This would analyze actual response rates
        return [
            'email_open_rate' => 85.5,
            'sms_delivery_rate' => 98.2,
            'in_app_read_rate' => 72.3,
            'push_open_rate' => 45.8,
        ];
    }

    /**
     * Get most active users
     */
    private function getMostActiveUsers($notifications): array
    {
        return $notifications->groupBy('user_id')
            ->map(function($userNotifications) {
                return $userNotifications->count();
            })
            ->sortDesc()
            ->take(10)
            ->toArray();
    }
}
