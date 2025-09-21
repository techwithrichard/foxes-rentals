<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileIntegrationService
{
    /**
     * Generate mobile app token for user
     */
    public function generateMobileToken(User $user): array
    {
        $token = $user->createToken('mobile-app', ['mobile:access'])->plainTextToken;
        
        Log::info("Mobile token generated for user", [
            'user_id' => $user->id,
            'token_preview' => substr($token, 0, 10) . '...'
        ]);

        return [
            'token' => $token,
            'expires_at' => now()->addDays(30),
            'user' => $this->formatUserForMobile($user)
        ];
    }

    /**
     * Authenticate mobile user
     */
    public function authenticateMobileUser(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Account is inactive'
            ];
        }

        $tokenData = $this->generateMobileToken($user);

        return [
            'success' => true,
            'message' => 'Authentication successful',
            'data' => $tokenData
        ];
    }

    /**
     * Get user dashboard data for mobile
     */
    public function getMobileDashboard(User $user): array
    {
        $user->load(['leases.property', 'payments', 'bills']);

        $dashboard = [
            'user' => $this->formatUserForMobile($user),
            'summary' => $this->getUserSummary($user),
            'recent_payments' => $this->getRecentPayments($user),
            'upcoming_bills' => $this->getUpcomingBills($user),
            'lease_info' => $this->getLeaseInfo($user),
            'notifications' => $this->getMobileNotifications($user),
            'quick_actions' => $this->getQuickActions($user)
        ];

        return $dashboard;
    }

    /**
     * Get properties for mobile browsing
     */
    public function getMobileProperties(array $filters = []): array
    {
        $query = Property::with(['address', 'landlord:id,name', 'houses'])
            ->where('status', 'active')
            ->select('id', 'name', 'description', 'type', 'rent', 'deposit', 'is_vacant', 'landlord_id', 'created_at');

        // Apply filters
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['min_rent'])) {
            $query->where('rent', '>=', $filters['min_rent']);
        }

        if (isset($filters['max_rent'])) {
            $query->where('rent', '<=', $filters['max_rent']);
        }

        if (isset($filters['is_vacant'])) {
            $query->where('is_vacant', $filters['is_vacant']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $properties = $query->paginate($filters['per_page'] ?? 20);

        return [
            'properties' => $properties->map(function ($property) {
                return $this->formatPropertyForMobile($property);
            }),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total()
            ]
        ];
    }

    /**
     * Get property details for mobile
     */
    public function getMobilePropertyDetails(string $propertyId): array
    {
        $property = Property::with([
            'address',
            'landlord:id,name,email,phone',
            'houses',
            'lease.tenant:id,name',
            'images'
        ])->find($propertyId);

        if (!$property) {
            return [
                'success' => false,
                'message' => 'Property not found'
            ];
        }

        return [
            'success' => true,
            'data' => $this->formatPropertyDetailsForMobile($property)
        ];
    }

    /**
     * Submit property inquiry from mobile
     */
    public function submitMobileInquiry(array $data): array
    {
        $inquiry = [
            'property_id' => $data['property_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'inquiry_type' => 'mobile_app',
            'status' => 'pending',
            'created_at' => now()
        ];

        // Here you would save the inquiry to database
        Log::info("Mobile property inquiry submitted", $inquiry);

        return [
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'inquiry_id' => Str::uuid()
        ];
    }

    /**
     * Get payment methods for mobile
     */
    public function getMobilePaymentMethods(): array
    {
        return [
            'mpesa' => [
                'name' => 'M-Pesa',
                'icon' => 'mpesa-icon',
                'available' => true,
                'description' => 'Pay using M-Pesa mobile money'
            ],
            'paypal' => [
                'name' => 'PayPal',
                'icon' => 'paypal-icon',
                'available' => true,
                'description' => 'Pay using PayPal account'
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'icon' => 'bank-icon',
                'available' => true,
                'description' => 'Direct bank transfer'
            ]
        ];
    }

    /**
     * Process mobile payment
     */
    public function processMobilePayment(array $paymentData): array
    {
        $payment = [
            'id' => Str::uuid(),
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentData['payment_method'],
            'reference_number' => $paymentData['reference_number'],
            'tenant_id' => $paymentData['tenant_id'],
            'property_id' => $paymentData['property_id'],
            'status' => 'pending',
            'created_at' => now()
        ];

        // Here you would process the payment
        Log::info("Mobile payment processed", $payment);

        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'payment_id' => $payment['id'],
            'status' => 'pending'
        ];
    }

    /**
     * Get mobile notifications
     */
    public function getMobileNotifications(User $user): array
    {
        // This would get actual notifications from the database
        return [
            [
                'id' => 1,
                'title' => 'Payment Reminder',
                'message' => 'Your rent payment is due in 3 days',
                'type' => 'payment_reminder',
                'read' => false,
                'created_at' => now()->subHours(2)
            ],
            [
                'id' => 2,
                'title' => 'Lease Update',
                'message' => 'Your lease agreement has been updated',
                'type' => 'lease_update',
                'read' => true,
                'created_at' => now()->subDays(1)
            ]
        ];
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(string $notificationId, User $user): array
    {
        // Here you would update the notification status
        Log::info("Mobile notification marked as read", [
            'notification_id' => $notificationId,
            'user_id' => $user->id
        ]);

        return [
            'success' => true,
            'message' => 'Notification marked as read'
        ];
    }

    /**
     * Get user profile for mobile
     */
    public function getMobileUserProfile(User $user): array
    {
        return [
            'user' => $this->formatUserForMobile($user),
            'lease_history' => $this->getLeaseHistory($user),
            'payment_history' => $this->getPaymentHistory($user),
            'documents' => $this->getUserDocuments($user)
        ];
    }

    /**
     * Update user profile from mobile
     */
    public function updateMobileUserProfile(User $user, array $data): array
    {
        $allowedFields = ['name', 'phone', 'address'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        $user->update($updateData);

        Log::info("Mobile user profile updated", [
            'user_id' => $user->id,
            'updated_fields' => array_keys($updateData)
        ]);

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $this->formatUserForMobile($user)
        ];
    }

    /**
     * Get mobile app settings
     */
    public function getMobileAppSettings(): array
    {
        return [
            'app_version' => '1.0.0',
            'min_android_version' => '7.0',
            'min_ios_version' => '12.0',
            'features' => [
                'property_search' => true,
                'payment_processing' => true,
                'document_upload' => true,
                'push_notifications' => true,
                'offline_mode' => false
            ],
            'api_endpoints' => [
                'base_url' => config('app.url') . '/api/mobile',
                'auth_endpoint' => '/auth/login',
                'properties_endpoint' => '/properties',
                'payments_endpoint' => '/payments'
            ],
            'payment_config' => [
                'mpesa_shortcode' => config('mpesa.shortcode'),
                'paypal_client_id' => config('paypal.client_id')
            ]
        ];
    }

    /**
     * Format user for mobile response
     */
    private function formatUserForMobile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar ?? null,
            'is_active' => $user->is_active,
            'roles' => $user->roles->pluck('name'),
            'last_login_at' => $user->last_login_at
        ];
    }

    /**
     * Format property for mobile response
     */
    private function formatPropertyForMobile(Property $property): array
    {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'description' => $property->description,
            'type' => $property->type,
            'rent' => $property->rent,
            'deposit' => $property->deposit,
            'is_vacant' => $property->is_vacant,
            'landlord_name' => $property->landlord?->name,
            'address' => $property->address ? [
                'city' => $property->address->city,
                'state' => $property->address->state,
                'country' => $property->address->country
            ] : null,
            'images' => $property->images?->pluck('url') ?? [],
            'created_at' => $property->created_at
        ];
    }

    /**
     * Format property details for mobile
     */
    private function formatPropertyDetailsForMobile(Property $property): array
    {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'description' => $property->description,
            'type' => $property->type,
            'rent' => $property->rent,
            'deposit' => $property->deposit,
            'is_vacant' => $property->is_vacant,
            'landlord' => $property->landlord ? [
                'id' => $property->landlord->id,
                'name' => $property->landlord->name,
                'email' => $property->landlord->email,
                'phone' => $property->landlord->phone
            ] : null,
            'address' => $property->address ? [
                'street' => $property->address->street,
                'city' => $property->address->city,
                'state' => $property->address->state,
                'postal_code' => $property->address->postal_code,
                'country' => $property->address->country
            ] : null,
            'houses' => $property->houses->map(function ($house) {
                return [
                    'id' => $house->id,
                    'name' => $house->name,
                    'rent' => $house->rent,
                    'bedrooms' => $house->bedrooms,
                    'bathrooms' => $house->bathrooms,
                    'size' => $house->size,
                    'is_vacant' => $house->is_vacant
                ];
            }),
            'images' => $property->images?->pluck('url') ?? [],
            'current_tenant' => $property->lease?->tenant ? [
                'id' => $property->lease->tenant->id,
                'name' => $property->lease->tenant->name
            ] : null
        ];
    }

    /**
     * Get user summary for mobile
     */
    private function getUserSummary(User $user): array
    {
        return [
            'total_payments' => $user->payments->count(),
            'total_amount_paid' => $user->payments->sum('amount'),
            'pending_bills' => $user->bills->where('status', 'pending')->count(),
            'active_leases' => $user->leases->where('status', 'active')->count()
        ];
    }

    /**
     * Get recent payments for mobile
     */
    private function getRecentPayments(User $user): array
    {
        return $user->payments()
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at,
                    'property_name' => $payment->property?->name
                ];
            });
    }

    /**
     * Get upcoming bills for mobile
     */
    private function getUpcomingBills(User $user): array
    {
        return $user->bills()
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'amount' => $bill->amount,
                    'description' => $bill->description,
                    'due_date' => $bill->due_date,
                    'property_name' => $bill->lease?->property?->name
                ];
            });
    }

    /**
     * Get lease info for mobile
     */
    private function getLeaseInfo(User $user): array
    {
        $activeLease = $user->leases()->where('status', 'active')->first();

        if (!$activeLease) {
            return [];
        }

        return [
            'id' => $activeLease->id,
            'property_name' => $activeLease->property?->name,
            'start_date' => $activeLease->start_date,
            'end_date' => $activeLease->end_date,
            'rent' => $activeLease->rent,
            'rent_cycle' => $activeLease->rent_cycle,
            'next_billing_date' => $activeLease->next_billing_date
        ];
    }

    /**
     * Get quick actions for mobile
     */
    private function getQuickActions(User $user): array
    {
        $actions = [
            [
                'id' => 'make_payment',
                'title' => 'Make Payment',
                'icon' => 'payment-icon',
                'available' => true
            ],
            [
                'id' => 'view_bills',
                'title' => 'View Bills',
                'icon' => 'bills-icon',
                'available' => true
            ],
            [
                'id' => 'contact_landlord',
                'title' => 'Contact Landlord',
                'icon' => 'contact-icon',
                'available' => $user->leases()->where('status', 'active')->exists()
            ],
            [
                'id' => 'report_issue',
                'title' => 'Report Issue',
                'icon' => 'report-icon',
                'available' => true
            ]
        ];

        return array_filter($actions, function ($action) {
            return $action['available'];
        });
    }

    /**
     * Get lease history for mobile
     */
    private function getLeaseHistory(User $user): array
    {
        return $user->leases()
            ->with('property')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($lease) {
                return [
                    'id' => $lease->id,
                    'property_name' => $lease->property?->name,
                    'start_date' => $lease->start_date,
                    'end_date' => $lease->end_date,
                    'rent' => $lease->rent,
                    'status' => $lease->status
                ];
            });
    }

    /**
     * Get payment history for mobile
     */
    private function getPaymentHistory(User $user): array
    {
        return $user->payments()
            ->with('property')
            ->orderBy('paid_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at,
                    'property_name' => $payment->property?->name
                ];
            });
    }

    /**
     * Get user documents for mobile
     */
    private function getUserDocuments(User $user): array
    {
        // This would get actual documents from the database
        return [
            [
                'id' => 1,
                'name' => 'Lease Agreement',
                'type' => 'pdf',
                'url' => '/documents/lease-agreement.pdf',
                'uploaded_at' => now()->subDays(30)
            ],
            [
                'id' => 2,
                'name' => 'Payment Receipt',
                'type' => 'pdf',
                'url' => '/documents/payment-receipt.pdf',
                'uploaded_at' => now()->subDays(7)
            ]
        ];
    }
}
