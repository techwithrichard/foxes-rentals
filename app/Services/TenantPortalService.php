<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenantPortalService
{
    /**
     * Get comprehensive tenant dashboard data
     */
    public function getTenantDashboard($tenantId): array
    {
        $tenant = User::findOrFail($tenantId);
        
        return [
            'tenant_info' => $this->getTenantInfo($tenant),
            'active_leases' => $this->getActiveLeases($tenantId),
            'payment_history' => $this->getPaymentHistory($tenantId),
            'upcoming_payments' => $this->getUpcomingPayments($tenantId),
            'maintenance_requests' => $this->getMaintenanceRequests($tenantId),
            'documents' => $this->getTenantDocuments($tenantId),
            'notifications' => $this->getNotifications($tenantId),
            'financial_summary' => $this->getFinancialSummary($tenantId),
        ];
    }

    /**
     * Get tenant basic information
     */
    public function getTenantInfo(User $tenant): array
    {
        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'avatar' => $tenant->avatar,
            'created_at' => $tenant->created_at,
            'last_login' => $tenant->last_login_at,
            'status' => $tenant->status ?? 'active',
        ];
    }

    /**
     * Get active leases for tenant
     */
    public function getActiveLeases($tenantId): array
    {
        $leases = Lease::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['property.address', 'property.propertyType', 'house'])
            ->get();

        return $leases->map(function($lease) {
            return [
                'id' => $lease->id,
                'lease_id' => $lease->lease_id,
                'property_name' => $lease->property->name,
                'property_address' => $lease->property->address ? 
                    $lease->property->address->address1 . ', ' . $lease->property->address->city : 'N/A',
                'property_type' => $lease->property->propertyType->name ?? 'N/A',
                'unit_name' => $lease->house->name ?? 'N/A',
                'rent_amount' => $lease->rent,
                'start_date' => $lease->start_date,
                'end_date' => $lease->end_date,
                'rent_cycle' => $lease->rent_cycle,
                'next_billing_date' => $lease->next_billing_date,
                'status' => $lease->status,
                'days_remaining' => $lease->end_date ? 
                    Carbon::now()->diffInDays($lease->end_date, false) : null,
            ];
        })->toArray();
    }

    /**
     * Get payment history for tenant
     */
    public function getPaymentHistory($tenantId, $limit = 10): array
    {
        $payments = Payment::whereHas('invoice.lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->with(['invoice.lease.property', 'paymentMethod'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();

        return $payments->map(function($payment) {
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->paymentMethod->name ?? 'Unknown',
                'payment_date' => $payment->created_at,
                'status' => $payment->status,
                'reference' => $payment->reference,
                'property_name' => $payment->invoice->lease->property->name ?? 'N/A',
                'invoice_id' => $payment->invoice->id,
                'invoice_reference' => $payment->invoice->reference,
            ];
        })->toArray();
    }

    /**
     * Get upcoming payments for tenant
     */
    public function getUpcomingPayments($tenantId): array
    {
        $upcomingInvoices = Invoice::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)
                  ->where('status', 'active');
        })
        ->where('status', 'pending')
        ->where('due_date', '>=', Carbon::now())
        ->with(['lease.property'])
        ->orderBy('due_date')
        ->get();

        return $upcomingInvoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'reference' => $invoice->reference,
                'amount' => $invoice->amount,
                'bills_amount' => $invoice->bills_amount,
                'total_amount' => $invoice->amount + $invoice->bills_amount,
                'paid_amount' => $invoice->paid_amount,
                'balance' => ($invoice->amount + $invoice->bills_amount) - $invoice->paid_amount,
                'due_date' => $invoice->due_date,
                'days_until_due' => Carbon::now()->diffInDays($invoice->due_date, false),
                'property_name' => $invoice->lease->property->name ?? 'N/A',
                'status' => $invoice->status,
                'is_overdue' => $invoice->due_date < Carbon::now(),
            ];
        })->toArray();
    }

    /**
     * Get maintenance requests for tenant
     */
    public function getMaintenanceRequests($tenantId, $limit = 10): array
    {
        $requests = MaintenanceRequest::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->with(['property', 'vendor'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();

        return $requests->map(function($request) {
            return [
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'status' => $request->status,
                'cost' => $request->cost,
                'requested_date' => $request->created_at,
                'scheduled_date' => $request->scheduled_date,
                'completed_date' => $request->completed_date,
                'property_name' => $request->property->name ?? 'N/A',
                'vendor_name' => $request->vendor->name ?? 'Not Assigned',
                'images' => $request->images ?? [],
            ];
        })->toArray();
    }

    /**
     * Get tenant documents
     */
    public function getTenantDocuments($tenantId): array
    {
        // Get lease documents
        $leaseDocuments = DB::table('lease_documents')
            ->whereHas('lease', function($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->get();

        // Get tenant-specific documents (if any)
        $tenantDocuments = DB::table('tenant_documents')
            ->where('tenant_id', $tenantId)
            ->get();

        return [
            'lease_documents' => $leaseDocuments->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'path' => $doc->path,
                    'type' => 'lease_document',
                    'created_at' => $doc->created_at,
                ];
            })->toArray(),
            'tenant_documents' => $tenantDocuments->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'path' => $doc->path,
                    'type' => 'tenant_document',
                    'created_at' => $doc->created_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get notifications for tenant
     */
    public function getNotifications($tenantId, $limit = 10): array
    {
        // This would integrate with a notification system
        // For now, we'll create some basic notifications based on data
        
        $notifications = [];
        
        // Check for overdue payments
        $overdueInvoices = Invoice::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('status', 'pending')
        ->where('due_date', '<', Carbon::now())
        ->count();

        if ($overdueInvoices > 0) {
            $notifications[] = [
                'type' => 'payment_overdue',
                'title' => 'Overdue Payment',
                'message' => "You have {$overdueInvoices} overdue payment(s). Please make payment as soon as possible.",
                'priority' => 'high',
                'created_at' => Carbon::now(),
            ];
        }

        // Check for upcoming payments
        $upcomingPayments = Invoice::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('status', 'pending')
        ->where('due_date', '<=', Carbon::now()->addDays(3))
        ->where('due_date', '>=', Carbon::now())
        ->count();

        if ($upcomingPayments > 0) {
            $notifications[] = [
                'type' => 'payment_reminder',
                'title' => 'Payment Reminder',
                'message' => "You have {$upcomingPayments} payment(s) due within 3 days.",
                'priority' => 'medium',
                'created_at' => Carbon::now(),
            ];
        }

        // Check for maintenance requests
        $pendingMaintenance = MaintenanceRequest::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('status', 'pending')
        ->count();

        if ($pendingMaintenance > 0) {
            $notifications[] = [
                'type' => 'maintenance_pending',
                'title' => 'Maintenance Request',
                'message' => "You have {$pendingMaintenance} pending maintenance request(s).",
                'priority' => 'low',
                'created_at' => Carbon::now(),
            ];
        }

        return array_slice($notifications, 0, $limit);
    }

    /**
     * Get financial summary for tenant
     */
    public function getFinancialSummary($tenantId): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Current month payments
        $currentMonthPayments = Payment::whereHas('invoice.lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('created_at', '>=', $currentMonth)
        ->sum('amount');

        // Last month payments
        $lastMonthPayments = Payment::whereHas('invoice.lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('created_at', '>=', $lastMonth)
        ->where('created_at', '<', $currentMonth)
        ->sum('amount');

        // Total outstanding balance
        $outstandingBalance = Invoice::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->where('status', 'pending')
        ->get()
        ->sum(function($invoice) {
            return ($invoice->amount + $invoice->bills_amount) - $invoice->paid_amount;
        });

        // Payment history (last 6 months)
        $paymentHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlyPayments = Payment::whereHas('invoice.lease', function($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');

            $paymentHistory[] = [
                'month' => $monthStart->format('M Y'),
                'amount' => $monthlyPayments,
            ];
        }

        return [
            'current_month_payments' => $currentMonthPayments,
            'last_month_payments' => $lastMonthPayments,
            'outstanding_balance' => $outstandingBalance,
            'payment_trend' => $currentMonthPayments > $lastMonthPayments ? 'increasing' : 'decreasing',
            'payment_history' => $paymentHistory,
        ];
    }

    /**
     * Submit maintenance request
     */
    public function submitMaintenanceRequest($tenantId, $data): array
    {
        $lease = Lease::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();

        if (!$lease) {
            return ['success' => false, 'message' => 'No active lease found'];
        }

        $request = MaintenanceRequest::create([
            'property_id' => $lease->property_id,
            'lease_id' => $lease->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'] ?? 'general',
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'pending',
            'requested_by' => $tenantId,
            'images' => $data['images'] ?? [],
        ]);

        return [
            'success' => true,
            'message' => 'Maintenance request submitted successfully',
            'request_id' => $request->id,
        ];
    }

    /**
     * Update tenant profile
     */
    public function updateTenantProfile($tenantId, $data): array
    {
        $tenant = User::findOrFail($tenantId);
        
        $allowedFields = ['name', 'phone', 'avatar'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        $tenant->update($updateData);

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'tenant' => $this->getTenantInfo($tenant),
        ];
    }
}
