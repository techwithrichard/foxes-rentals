<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find payments by invoice
     */
    public function findByInvoice(string $invoiceId): Collection;

    /**
     * Find payments by tenant
     */
    public function findByTenant(string $tenantId): Collection;

    /**
     * Find payments by landlord
     */
    public function findByLandlord(string $landlordId): Collection;

    /**
     * Find payments by property
     */
    public function findByProperty(string $propertyId): Collection;

    /**
     * Find payments by status
     */
    public function findByStatus(PaymentStatusEnum $status): Collection;

    /**
     * Find payments by payment method
     */
    public function findByPaymentMethod(string $paymentMethod): Collection;

    /**
     * Find payments by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find payments by amount range
     */
    public function findByAmountRange(float $minAmount, float $maxAmount): Collection;

    /**
     * Find verified payments
     */
    public function findVerified(): Collection;

    /**
     * Find unverified payments
     */
    public function findUnverified(): Collection;

    /**
     * Find payments with receipts
     */
    public function findWithReceipts(): Collection;

    /**
     * Find payments without receipts
     */
    public function findWithoutReceipts(): Collection;

    /**
     * Get payment statistics
     */
    public function getStatistics(): array;

    /**
     * Get payments by month
     */
    public function getByMonth(int $year, int $month): Collection;

    /**
     * Get payments by year
     */
    public function getByYear(int $year): Collection;

    /**
     * Get total amount by status
     */
    public function getTotalAmountByStatus(PaymentStatusEnum $status): float;

    /**
     * Get total amount by payment method
     */
    public function getTotalAmountByMethod(string $paymentMethod): float;

    /**
     * Get payment trends
     */
    public function getPaymentTrends(int $months = 12): array;

    /**
     * Search payments with multiple criteria
     */
    public function searchPayments(array $criteria): Collection;

    /**
     * Find duplicate payments
     */
    public function findDuplicates(): Collection;

    /**
     * Find refunds
     */
    public function findRefunds(): Collection;

    /**
     * Find parent payments
     */
    public function findParentPayments(): Collection;
}
