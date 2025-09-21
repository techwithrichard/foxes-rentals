<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkflowAutomationService
{
    protected $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Process lease renewal automation
     */
    public function processLeaseRenewal($leaseId): array
    {
        $lease = Lease::with(['tenant', 'property', 'landlord'])->findOrFail($leaseId);
        
        try {
            DB::beginTransaction();

            // Check if lease is eligible for renewal
            if (!$this->isLeaseEligibleForRenewal($lease)) {
                return [
                    'success' => false,
                    'message' => 'Lease is not eligible for renewal',
                ];
            }

            // Create renewal offer
            $renewalOffer = $this->createRenewalOffer($lease);
            
            // Send renewal notifications
            $this->communicationService->sendNotification(
                $lease->tenant_id,
                'lease_renewal_offer',
                [
                    'lease_id' => $lease->lease_id,
                    'property_name' => $lease->property->name,
                    'current_end_date' => $lease->end_date,
                    'proposed_start_date' => $renewalOffer['start_date'],
                    'proposed_end_date' => $renewalOffer['end_date'],
                    'proposed_rent' => $renewalOffer['rent'],
                    'offer_expiry' => $renewalOffer['expiry_date'],
                ],
                ['email', 'in_app']
            );

            // Log the renewal process
            ActivityLog::create([
                'log_name' => 'lease_renewal',
                'description' => "Lease renewal offer created for {$lease->lease_id}",
                'subject_type' => Lease::class,
                'subject_id' => $lease->id,
                'properties' => $renewalOffer,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lease renewal offer created and sent successfully',
                'renewal_offer' => $renewalOffer,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lease renewal automation failed', [
                'lease_id' => $leaseId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process lease renewal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle payment reminder automation
     */
    public function handlePaymentReminders(): array
    {
        $results = [
            'reminders_sent' => 0,
            'escalations' => 0,
            'errors' => [],
        ];

        // Get overdue invoices
        $overdueInvoices = Invoice::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->with(['lease.tenant', 'lease.property'])
            ->get();

        foreach ($overdueInvoices as $invoice) {
            try {
                $daysOverdue = Carbon::now()->diffInDays($invoice->due_date);
                $tenant = $invoice->lease->tenant;
                $property = $invoice->lease->property;

                // Determine reminder level
                $reminderLevel = $this->getReminderLevel($daysOverdue);
                
                // Send appropriate reminder
                $this->communicationService->sendNotification(
                    $tenant->id,
                    "payment_reminder_{$reminderLevel}",
                    [
                        'tenant_name' => $tenant->name,
                        'property_name' => $property->name,
                        'amount' => $invoice->amount + $invoice->bills_amount,
                        'days_overdue' => $daysOverdue,
                        'late_fee' => $this->calculateLateFee($invoice, $daysOverdue),
                        'invoice_reference' => $invoice->reference,
                    ],
                    ['email', 'sms', 'in_app']
                );

                // Escalate if necessary
                if ($reminderLevel === 'final') {
                    $this->escalateOverduePayment($invoice);
                    $results['escalations']++;
                }

                $results['reminders_sent']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Process maintenance request automation
     */
    public function processMaintenanceRequests(): array
    {
        $results = [
            'requests_processed' => 0,
            'auto_assignments' => 0,
            'errors' => [],
        ];

        // Get pending maintenance requests
        $pendingRequests = MaintenanceRequest::where('status', 'pending')
            ->with(['property', 'lease.tenant'])
            ->get();

        foreach ($pendingRequests as $request) {
            try {
                // Auto-assign vendor based on category and priority
                $vendor = $this->autoAssignVendor($request);
                
                if ($vendor) {
                    $request->update([
                        'vendor_id' => $vendor->id,
                        'status' => 'assigned',
                        'assigned_at' => Carbon::now(),
                    ]);

                    // Notify vendor
                    $this->communicationService->sendNotification(
                        $vendor->user_id,
                        'maintenance_assignment',
                        [
                            'vendor_name' => $vendor->name,
                            'request_title' => $request->title,
                            'property_name' => $request->property->name,
                            'priority' => $request->priority,
                            'estimated_cost' => $request->estimated_cost,
                            'scheduled_date' => $request->scheduled_date,
                        ],
                        ['email', 'sms']
                    );

                    $results['auto_assignments']++;
                }

                // Schedule if not already scheduled
                if (!$request->scheduled_date && $request->priority !== 'urgent') {
                    $request->update([
                        'scheduled_date' => $this->calculateOptimalScheduleDate($request),
                    ]);
                }

                $results['requests_processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Generate monthly reports automation
     */
    public function generateMonthlyReports($propertyId = null): array
    {
        $results = [
            'reports_generated' => 0,
            'recipients_notified' => 0,
            'errors' => [],
        ];

        try {
            $properties = $propertyId ? 
                Property::where('id', $propertyId)->get() : 
                Property::all();

            foreach ($properties as $property) {
                // Generate financial report
                $financialReport = $this->generatePropertyFinancialReport($property);
                
                // Generate maintenance report
                $maintenanceReport = $this->generatePropertyMaintenanceReport($property);
                
                // Generate occupancy report
                $occupancyReport = $this->generatePropertyOccupancyReport($property);

                // Notify landlord
                $this->communicationService->sendNotification(
                    $property->landlord_id,
                    'monthly_report',
                    [
                        'landlord_name' => $property->landlord->name,
                        'property_name' => $property->name,
                        'report_month' => Carbon::now()->subMonth()->format('F Y'),
                        'financial_summary' => $financialReport['summary'],
                        'maintenance_summary' => $maintenanceReport['summary'],
                        'occupancy_summary' => $occupancyReport['summary'],
                    ],
                    ['email', 'in_app']
                );

                $results['reports_generated']++;
                $results['recipients_notified']++;
            }
        } catch (\Exception $e) {
            $results['errors'][] = [
                'error' => $e->getMessage(),
            ];
        }

        return $results;
    }

    /**
     * Check if lease is eligible for renewal
     */
    private function isLeaseEligibleForRenewal($lease): bool
    {
        // Check if lease is active
        if ($lease->status !== 'active') {
            return false;
        }

        // Check if lease expires within 60 days
        if ($lease->end_date->diffInDays(Carbon::now()) > 60) {
            return false;
        }

        // Check if tenant has good payment history
        $overdueInvoices = Invoice::whereHas('lease', function($query) use ($lease) {
            $query->where('tenant_id', $lease->tenant_id);
        })->where('status', 'pending')
        ->where('due_date', '<', Carbon::now())
        ->count();

        return $overdueInvoices === 0;
    }

    /**
     * Create renewal offer
     */
    private function createRenewalOffer($lease): array
    {
        $currentRent = $lease->rent;
        $marketRate = $this->getMarketRate($lease->property_id);
        
        // Calculate proposed rent (5% increase or market rate, whichever is lower)
        $proposedRent = min($currentRent * 1.05, $marketRate);
        
        return [
            'lease_id' => $lease->lease_id,
            'current_rent' => $currentRent,
            'proposed_rent' => $proposedRent,
            'start_date' => $lease->end_date->addDay(),
            'end_date' => $lease->end_date->addYear(),
            'expiry_date' => Carbon::now()->addDays(14),
            'terms' => $this->getStandardRenewalTerms(),
        ];
    }

    /**
     * Get reminder level based on days overdue
     */
    private function getReminderLevel($daysOverdue): string
    {
        return match(true) {
            $daysOverdue <= 3 => 'gentle',
            $daysOverdue <= 7 => 'firm',
            $daysOverdue <= 14 => 'urgent',
            default => 'final',
        };
    }

    /**
     * Calculate late fee
     */
    private function calculateLateFee($invoice, $daysOverdue): float
    {
        $baseAmount = $invoice->amount + $invoice->bills_amount;
        $lateFeeRate = 0.05; // 5% per month
        $monthlyLateFee = $baseAmount * $lateFeeRate;
        
        return round(($monthlyLateFee / 30) * $daysOverdue, 2);
    }

    /**
     * Escalate overdue payment
     */
    private function escalateOverduePayment($invoice): void
    {
        // Notify landlord
        $this->communicationService->sendNotification(
            $invoice->lease->property->landlord_id,
            'overdue_payment_escalation',
            [
                'landlord_name' => $invoice->lease->property->landlord->name,
                'tenant_name' => $invoice->lease->tenant->name,
                'property_name' => $invoice->lease->property->name,
                'amount' => $invoice->amount + $invoice->bills_amount,
                'days_overdue' => Carbon::now()->diffInDays($invoice->due_date),
                'invoice_reference' => $invoice->reference,
            ],
            ['email', 'sms']
        );

        // Log escalation
        ActivityLog::create([
            'log_name' => 'payment_escalation',
            'description' => "Payment escalation triggered for invoice {$invoice->reference}",
            'subject_type' => Invoice::class,
            'subject_id' => $invoice->id,
        ]);
    }

    /**
     * Auto-assign vendor
     */
    private function autoAssignVendor($request)
    {
        // Get vendors by category and availability
        $vendors = \App\Models\Vendor::where('category', $request->category)
            ->where('is_active', true)
            ->where('rating', '>=', 4.0)
            ->get();

        if ($vendors->isEmpty()) {
            return null;
        }

        // Select vendor with least current workload
        return $vendors->sortBy(function($vendor) {
            return $vendor->maintenanceRequests()
                ->where('status', 'in_progress')
                ->count();
        })->first();
    }

    /**
     * Calculate optimal schedule date
     */
    private function calculateOptimalScheduleDate($request): Carbon
    {
        $baseDate = Carbon::now();
        
        return match($request->priority) {
            'urgent' => $baseDate->addHours(4),
            'high' => $baseDate->addDay(),
            'medium' => $baseDate->addDays(3),
            'low' => $baseDate->addWeek(),
            default => $baseDate->addDays(2),
        };
    }

    /**
     * Get market rate for property
     */
    private function getMarketRate($propertyId): float
    {
        $property = Property::find($propertyId);
        
        // Get similar properties in the same area
        $similarProperties = Property::whereHas('address', function($query) use ($property) {
            $query->where('city', $property->address->city)
                  ->where('state', $property->address->state);
        })
        ->where('id', '!=', $propertyId)
        ->where('status', 'active')
        ->get();

        return $similarProperties->avg('rent') ?? $property->rent;
    }

    /**
     * Get standard renewal terms
     */
    private function getStandardRenewalTerms(): array
    {
        return [
            'lease_duration' => '12 months',
            'deposit_required' => 'One month rent',
            'notice_period' => '30 days',
            'pet_policy' => 'Subject to approval',
            'maintenance_responsibility' => 'Landlord',
        ];
    }

    /**
     * Generate property financial report
     */
    private function generatePropertyFinancialReport($property): array
    {
        $monthStart = Carbon::now()->subMonth()->startOfMonth();
        $monthEnd = Carbon::now()->subMonth()->endOfMonth();

        $monthlyRevenue = Lease::where('property_id', $property->id)
            ->whereBetween('start_date', [$monthStart, $monthEnd])
            ->sum('rent');

        $monthlyExpenses = MaintenanceRequest::where('property_id', $property->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('cost');

        return [
            'summary' => [
                'monthly_revenue' => $monthlyRevenue,
                'monthly_expenses' => $monthlyExpenses,
                'net_income' => $monthlyRevenue - $monthlyExpenses,
                'occupancy_rate' => $this->calculateOccupancyRate($property),
            ],
        ];
    }

    /**
     * Generate property maintenance report
     */
    private function generatePropertyMaintenanceReport($property): array
    {
        $monthStart = Carbon::now()->subMonth()->startOfMonth();
        $monthEnd = Carbon::now()->subMonth()->endOfMonth();

        $maintenanceRequests = MaintenanceRequest::where('property_id', $property->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get();

        return [
            'summary' => [
                'total_requests' => $maintenanceRequests->count(),
                'completed_requests' => $maintenanceRequests->where('status', 'completed')->count(),
                'total_cost' => $maintenanceRequests->sum('cost'),
                'average_cost' => $maintenanceRequests->avg('cost'),
            ],
        ];
    }

    /**
     * Generate property occupancy report
     */
    private function generatePropertyOccupancyReport($property): array
    {
        $activeLeases = Lease::where('property_id', $property->id)
            ->where('status', 'active')
            ->get();

        return [
            'summary' => [
                'active_leases' => $activeLeases->count(),
                'occupancy_rate' => $this->calculateOccupancyRate($property),
                'average_lease_duration' => $this->calculateAverageLeaseDuration($activeLeases),
                'renewal_rate' => $this->calculateRenewalRate($property),
            ],
        ];
    }

    /**
     * Calculate occupancy rate
     */
    private function calculateOccupancyRate($property): float
    {
        $totalUnits = $property->units()->count();
        $occupiedUnits = $property->activeLeases()->count();
        
        return $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0;
    }

    /**
     * Calculate average lease duration
     */
    private function calculateAverageLeaseDuration($leases): float
    {
        if ($leases->count() === 0) {
            return 0;
        }

        $totalDuration = $leases->sum(function($lease) {
            return $lease->start_date->diffInDays($lease->end_date);
        });

        return round($totalDuration / $leases->count(), 1);
    }

    /**
     * Calculate renewal rate
     */
    private function calculateRenewalRate($property): float
    {
        $totalLeases = Lease::where('property_id', $property->id)->count();
        $renewedLeases = Lease::where('property_id', $property->id)
            ->where('is_renewal', true)
            ->count();

        return $totalLeases > 0 ? ($renewedLeases / $totalLeases) * 100 : 0;
    }
}
