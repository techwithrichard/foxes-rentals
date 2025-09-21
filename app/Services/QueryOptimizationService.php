<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryOptimizationService
{
    /**
     * Get optimized properties with eager loading
     */
    public function getOptimizedProperties(array $filters = [], int $perPage = 15)
    {
        $query = Property::with([
            'landlord:id,name,email',
            'address:id,addressable_id,addressable_type,city,state',
            'lease.tenant:id,name,email',
            'houses:id,property_id,name,rent,status'
        ])
        ->select('id', 'name', 'description', 'type', 'rent', 'deposit', 'status', 'is_vacant', 'landlord_id', 'created_at')
        ->withCount(['houses', 'leases']);

        // Apply filters efficiently
        $this->applyPropertyFilters($query, $filters);

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get optimized users with relationships
     */
    public function getOptimizedUsers(array $filters = [], int $perPage = 15)
    {
        $query = User::with([
            'roles:id,name',
            'permissions:id,name'
        ])
        ->select('id', 'name', 'email', 'phone', 'is_active', 'last_login_at', 'created_at')
        ->withCount(['properties', 'leases']);

        // Apply filters efficiently
        $this->applyUserFilters($query, $filters);

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get optimized leases with relationships
     */
    public function getOptimizedLeases(array $filters = [], int $perPage = 15)
    {
        $query = Lease::with([
            'tenant:id,name,email',
            'property:id,name,type',
            'house:id,name,property_id',
            'bills:id,lease_id,amount,status'
        ])
        ->select('id', 'lease_id', 'start_date', 'end_date', 'rent', 'rent_cycle', 'status', 'tenant_id', 'property_id', 'house_id', 'created_at')
        ->withSum('bills', 'amount');

        // Apply filters efficiently
        $this->applyLeaseFilters($query, $filters);

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get optimized payments with relationships
     */
    public function getOptimizedPayments(array $filters = [], int $perPage = 15)
    {
        $query = Payment::with([
            'tenant:id,name,email',
            'property:id,name',
            'house:id,name',
            'invoice:id,invoice_id,amount'
        ])
        ->select('id', 'amount', 'payment_method', 'status', 'paid_at', 'tenant_id', 'property_id', 'house_id', 'created_at');

        // Apply filters efficiently
        $this->applyPaymentFilters($query, $filters);

        return $query->latest('paid_at')->paginate($perPage);
    }

    /**
     * Get dashboard statistics with optimized queries
     */
    public function getDashboardStatistics(): array
    {
        // Use raw queries for better performance
        $stats = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM properties WHERE deleted_at IS NULL) as total_properties,
                (SELECT COUNT(*) FROM properties WHERE status = 'active' AND deleted_at IS NULL) as active_properties,
                (SELECT COUNT(*) FROM properties WHERE is_vacant = 1 AND deleted_at IS NULL) as vacant_properties,
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) as total_users,
                (SELECT COUNT(*) FROM users WHERE is_active = 1 AND deleted_at IS NULL) as active_users,
                (SELECT COUNT(*) FROM leases WHERE deleted_at IS NULL) as total_leases,
                (SELECT COUNT(*) FROM leases WHERE status = 'active' AND deleted_at IS NULL) as active_leases,
                (SELECT COUNT(*) FROM payments WHERE deleted_at IS NULL) as total_payments,
                (SELECT COUNT(*) FROM payments WHERE status = 'completed' AND deleted_at IS NULL) as completed_payments,
                (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND deleted_at IS NULL) as total_revenue
        ")[0];

        return (array) $stats;
    }

    /**
     * Get property performance metrics
     */
    public function getPropertyPerformanceMetrics(): array
    {
        return DB::select("
            SELECT 
                p.id,
                p.name,
                p.rent,
                p.status,
                COUNT(DISTINCT l.id) as total_leases,
                COUNT(DISTINCT CASE WHEN l.status = 'active' THEN l.id END) as active_leases,
                COUNT(DISTINCT h.id) as total_houses,
                COUNT(DISTINCT CASE WHEN h.is_vacant = 0 THEN h.id END) as occupied_houses,
                COALESCE(SUM(CASE WHEN pay.status = 'completed' THEN pay.amount ELSE 0 END), 0) as total_revenue,
                COALESCE(AVG(CASE WHEN pay.status = 'completed' THEN pay.amount ELSE NULL END), 0) as avg_payment
            FROM properties p
            LEFT JOIN leases l ON p.id = l.property_id AND l.deleted_at IS NULL
            LEFT JOIN houses h ON p.id = h.property_id AND h.deleted_at IS NULL
            LEFT JOIN payments pay ON p.id = pay.property_id AND pay.deleted_at IS NULL
            WHERE p.deleted_at IS NULL
            GROUP BY p.id, p.name, p.rent, p.status
            ORDER BY total_revenue DESC
        ");
    }

    /**
     * Get tenant payment history with optimization
     */
    public function getTenantPaymentHistory(string $tenantId, int $limit = 10): array
    {
        return DB::select("
            SELECT 
                p.id,
                p.amount,
                p.payment_method,
                p.status,
                p.paid_at,
                prop.name as property_name,
                h.name as house_name
            FROM payments p
            LEFT JOIN properties prop ON p.property_id = prop.id
            LEFT JOIN houses h ON p.house_id = h.id
            WHERE p.tenant_id = ? 
            AND p.deleted_at IS NULL
            ORDER BY p.paid_at DESC
            LIMIT ?
        ", [$tenantId, $limit]);
    }

    /**
     * Get landlord income summary
     */
    public function getLandlordIncomeSummary(string $landlordId, string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?: now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: now()->endOfMonth()->toDateString();

        return DB::select("
            SELECT 
                p.id as property_id,
                p.name as property_name,
                COUNT(DISTINCT pay.id) as payment_count,
                COALESCE(SUM(pay.amount), 0) as total_amount,
                COALESCE(AVG(pay.amount), 0) as avg_payment,
                COALESCE(SUM(pay.amount * (100 - p.commission) / 100), 0) as landlord_amount,
                COALESCE(SUM(pay.amount * p.commission / 100), 0) as commission_amount
            FROM properties p
            LEFT JOIN payments pay ON p.id = pay.property_id 
                AND pay.status = 'completed'
                AND pay.paid_at BETWEEN ? AND ?
                AND pay.deleted_at IS NULL
            WHERE p.landlord_id = ? 
            AND p.deleted_at IS NULL
            GROUP BY p.id, p.name
            ORDER BY total_amount DESC
        ", [$startDate, $endDate, $landlordId]);
    }

    /**
     * Get expiring leases with optimization
     */
    public function getExpiringLeases(int $days = 30): array
    {
        return DB::select("
            SELECT 
                l.id,
                l.lease_id,
                l.end_date,
                l.rent,
                l.status,
                u.name as tenant_name,
                u.email as tenant_email,
                u.phone as tenant_phone,
                p.name as property_name,
                h.name as house_name
            FROM leases l
            JOIN users u ON l.tenant_id = u.id
            JOIN properties p ON l.property_id = p.id
            LEFT JOIN houses h ON l.house_id = h.id
            WHERE l.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
            AND l.status = 'active'
            AND l.deleted_at IS NULL
            ORDER BY l.end_date ASC
        ", [$days]);
    }

    /**
     * Apply property filters efficiently
     */
    private function applyPropertyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['landlord_id'])) {
            $query->where('landlord_id', $filters['landlord_id']);
        }

        if (isset($filters['is_vacant'])) {
            $query->where('is_vacant', $filters['is_vacant']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['rent_min'])) {
            $query->where('rent', '>=', $filters['rent_min']);
        }

        if (isset($filters['rent_max'])) {
            $query->where('rent', '<=', $filters['rent_max']);
        }
    }

    /**
     * Apply user filters efficiently
     */
    private function applyUserFilters(Builder $query, array $filters): void
    {
        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    /**
     * Apply lease filters efficiently
     */
    private function applyLeaseFilters(Builder $query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (isset($filters['expiring'])) {
            $query->where('end_date', '<=', now()->addDays(30))
                  ->where('end_date', '>', now());
        }

        if (isset($filters['expired'])) {
            $query->where('end_date', '<', now());
        }
    }

    /**
     * Apply payment filters efficiently
     */
    private function applyPaymentFilters(Builder $query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('paid_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('paid_at', '<=', $filters['date_to']);
        }

        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }
    }

    /**
     * Optimize query by adding indexes hints
     */
    public function addIndexHints(Builder $query, string $table, array $indexes): Builder
    {
        $indexHint = 'USE INDEX (' . implode(', ', $indexes) . ')';
        return $query->from(DB::raw("{$table} {$indexHint}"));
    }

    /**
     * Get query execution plan
     */
    public function getQueryPlan(Builder $query): array
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        $explainQuery = "EXPLAIN " . $sql;
        return DB::select($explainQuery, $bindings);
    }

    /**
     * Analyze slow queries
     */
    public function analyzeSlowQueries(): array
    {
        return DB::select("
            SELECT 
                query_time,
                lock_time,
                rows_sent,
                rows_examined,
                sql_text
            FROM mysql.slow_log 
            WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY query_time DESC
            LIMIT 10
        ");
    }
}
