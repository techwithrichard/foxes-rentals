<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * Find payments by invoice
     */
    public function findByInvoice(string $invoiceId): Collection
    {
        return $this->getQuery()
            ->where('invoice_id', $invoiceId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by tenant
     */
    public function findByTenant(string $tenantId): Collection
    {
        return $this->getQuery()
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by landlord
     */
    public function findByLandlord(string $landlordId): Collection
    {
        return $this->getQuery()
            ->where('landlord_id', $landlordId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by property
     */
    public function findByProperty(string $propertyId): Collection
    {
        return $this->getQuery()
            ->where('property_id', $propertyId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by status
     */
    public function findByStatus(PaymentStatusEnum $status): Collection
    {
        return $this->getQuery()
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by payment method
     */
    public function findByPaymentMethod(string $paymentMethod): Collection
    {
        return $this->getQuery()
            ->where('payment_method', $paymentMethod)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->getQuery()
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    /**
     * Find payments by amount range
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->getQuery()
            ->whereBetween('amount', [$minAmount, $maxAmount])
            ->orderBy('amount', 'desc')
            ->get();
    }

    /**
     * Find verified payments
     */
    public function findVerified(): Collection
    {
        return $this->getQuery()
            ->whereNotNull('verified_at')
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    /**
     * Find unverified payments
     */
    public function findUnverified(): Collection
    {
        return $this->getQuery()
            ->whereNull('verified_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments with receipts
     */
    public function findWithReceipts(): Collection
    {
        return $this->getQuery()
            ->whereNotNull('payment_receipt')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find payments without receipts
     */
    public function findWithoutReceipts(): Collection
    {
        return $this->getQuery()
            ->whereNull('payment_receipt')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(): array
    {
        $totalPayments = $this->getQuery()->count();
        $totalAmount = $this->getQuery()->where('status', PaymentStatusEnum::PAID)->sum('amount');
        
        $statusCounts = $this->getQuery()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $methodCounts = $this->getQuery()
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->where('status', PaymentStatusEnum::PAID)
            ->groupBy('payment_method')
            ->get();

        $monthlyStats = $this->getQuery()
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, COUNT(*) as count, SUM(amount) as total')
            ->where('status', PaymentStatusEnum::PAID)
            ->where('paid_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'total_payments' => $totalPayments,
            'total_amount' => $totalAmount,
            'status_counts' => $statusCounts,
            'method_counts' => $methodCounts,
            'monthly_stats' => $monthlyStats,
            'verified_payments' => $this->findVerified()->count(),
            'unverified_payments' => $this->findUnverified()->count(),
            'with_receipts' => $this->findWithReceipts()->count(),
            'without_receipts' => $this->findWithoutReceipts()->count(),
        ];
    }

    /**
     * Get payments by month
     */
    public function getByMonth(int $year, int $month): Collection
    {
        return $this->getQuery()
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    /**
     * Get payments by year
     */
    public function getByYear(int $year): Collection
    {
        return $this->getQuery()
            ->whereYear('paid_at', $year)
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    /**
     * Get total amount by status
     */
    public function getTotalAmountByStatus(PaymentStatusEnum $status): float
    {
        return $this->getQuery()
            ->where('status', $status)
            ->sum('amount');
    }

    /**
     * Get total amount by payment method
     */
    public function getTotalAmountByMethod(string $paymentMethod): float
    {
        return $this->getQuery()
            ->where('payment_method', $paymentMethod)
            ->where('status', PaymentStatusEnum::PAID)
            ->sum('amount');
    }

    /**
     * Get payment trends
     */
    public function getPaymentTrends(int $months = 12): array
    {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $trends[] = [
                'month' => $month,
                'month_name' => $date->format('F Y'),
                'count' => $this->getQuery()
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->where('status', PaymentStatusEnum::PAID)
                    ->count(),
                'amount' => $this->getQuery()
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->where('status', PaymentStatusEnum::PAID)
                    ->sum('amount'),
            ];
        }

        return $trends;
    }

    /**
     * Search payments with multiple criteria
     */
    public function searchPayments(array $criteria): Collection
    {
        $query = $this->getQuery();

        // Status filter
        if (isset($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }

        // Payment method filter
        if (isset($criteria['payment_method'])) {
            $query->where('payment_method', $criteria['payment_method']);
        }

        // Tenant filter
        if (isset($criteria['tenant_id'])) {
            $query->where('tenant_id', $criteria['tenant_id']);
        }

        // Landlord filter
        if (isset($criteria['landlord_id'])) {
            $query->where('landlord_id', $criteria['landlord_id']);
        }

        // Property filter
        if (isset($criteria['property_id'])) {
            $query->where('property_id', $criteria['property_id']);
        }

        // Invoice filter
        if (isset($criteria['invoice_id'])) {
            $query->where('invoice_id', $criteria['invoice_id']);
        }

        // Date range filter
        if (isset($criteria['start_date']) && isset($criteria['end_date'])) {
            $query->whereBetween('paid_at', [$criteria['start_date'], $criteria['end_date']]);
        }

        // Amount range filter
        if (isset($criteria['min_amount']) && isset($criteria['max_amount'])) {
            $query->whereBetween('amount', [$criteria['min_amount'], $criteria['max_amount']]);
        }

        // Verified filter
        if (isset($criteria['verified'])) {
            if ($criteria['verified']) {
                $query->whereNotNull('verified_at');
            } else {
                $query->whereNull('verified_at');
            }
        }

        // Receipt filter
        if (isset($criteria['has_receipt'])) {
            if ($criteria['has_receipt']) {
                $query->whereNotNull('payment_receipt');
            } else {
                $query->whereNull('payment_receipt');
            }
        }

        // Reference number search
        if (isset($criteria['reference'])) {
            $query->where('reference_number', 'like', '%' . $criteria['reference'] . '%');
        }

        // Notes search
        if (isset($criteria['notes'])) {
            $query->where('notes', 'like', '%' . $criteria['notes'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find duplicate payments
     */
    public function findDuplicates(): Collection
    {
        return $this->getQuery()
            ->select('reference_number', 'amount', 'paid_at', DB::raw('COUNT(*) as count'))
            ->groupBy('reference_number', 'amount', 'paid_at')
            ->having('count', '>', 1)
            ->get();
    }

    /**
     * Find refunds
     */
    public function findRefunds(): Collection
    {
        return $this->getQuery()
            ->where('amount', '<', 0)
            ->orWhere('payment_method', 'like', '%REFUND%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find parent payments (non-refunds)
     */
    public function findParentPayments(): Collection
    {
        return $this->getQuery()
            ->where('amount', '>', 0)
            ->where('payment_method', 'not like', '%REFUND%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get payments with relationships
     */
    public function getWithRelationships(): Collection
    {
        return $this->getQuery()
            ->with(['invoice', 'tenant', 'property', 'house', 'recordedBy', 'verifiedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent payments
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->getQuery()
            ->with(['invoice', 'tenant', 'property'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get payments requiring attention
     */
    public function getRequiringAttention(): Collection
    {
        return $this->getQuery()
            ->where(function ($query) {
                $query->where('status', PaymentStatusEnum::PENDING)
                      ->orWhereNull('verified_at')
                      ->orWhereNull('payment_receipt');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
