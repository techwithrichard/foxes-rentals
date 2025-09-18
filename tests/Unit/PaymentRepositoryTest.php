<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\PropertyConsolidated;
use App\Repositories\PaymentRepository;
use App\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentRepository;
    protected $invoice;
    protected $tenant;
    protected $landlord;
    protected $property;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->paymentRepository = new PaymentRepository(new Payment());
        
        // Create test data
        $this->property = PropertyConsolidated::factory()->create();
        $this->tenant = User::factory()->create();
        $this->landlord = User::factory()->create();
        
        $this->invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'amount' => 50000,
            'status' => PaymentStatusEnum::PENDING
        ]);
    }

    public function test_can_find_payments_by_invoice()
    {
        Payment::factory()->create(['invoice_id' => $this->invoice->id]);
        Payment::factory()->create(['invoice_id' => $this->invoice->id]);
        Payment::factory()->create(); // Different invoice

        $payments = $this->paymentRepository->findByInvoice($this->invoice->id);

        $this->assertCount(2, $payments);
        $this->assertEquals($this->invoice->id, $payments->first()->invoice_id);
    }

    public function test_can_find_payments_by_tenant()
    {
        Payment::factory()->create(['tenant_id' => $this->tenant->id]);
        Payment::factory()->create(['tenant_id' => $this->tenant->id]);
        Payment::factory()->create(); // Different tenant

        $payments = $this->paymentRepository->findByTenant($this->tenant->id);

        $this->assertCount(2, $payments);
        $this->assertEquals($this->tenant->id, $payments->first()->tenant_id);
    }

    public function test_can_find_payments_by_landlord()
    {
        Payment::factory()->create(['landlord_id' => $this->landlord->id]);
        Payment::factory()->create(['landlord_id' => $this->landlord->id]);
        Payment::factory()->create(); // Different landlord

        $payments = $this->paymentRepository->findByLandlord($this->landlord->id);

        $this->assertCount(2, $payments);
        $this->assertEquals($this->landlord->id, $payments->first()->landlord_id);
    }

    public function test_can_find_payments_by_property()
    {
        Payment::factory()->create(['property_id' => $this->property->id]);
        Payment::factory()->create(['property_id' => $this->property->id]);
        Payment::factory()->create(); // Different property

        $payments = $this->paymentRepository->findByProperty($this->property->id);

        $this->assertCount(2, $payments);
        $this->assertEquals($this->property->id, $payments->first()->property_id);
    }

    public function test_can_find_payments_by_status()
    {
        Payment::factory()->create(['status' => PaymentStatusEnum::PAID]);
        Payment::factory()->create(['status' => PaymentStatusEnum::PAID]);
        Payment::factory()->create(['status' => PaymentStatusEnum::PENDING]);

        $payments = $this->paymentRepository->findByStatus(PaymentStatusEnum::PAID);

        $this->assertCount(2, $payments);
        $this->assertEquals(PaymentStatusEnum::PAID, $payments->first()->status);
    }

    public function test_can_find_payments_by_payment_method()
    {
        Payment::factory()->create(['payment_method' => 'CASH']);
        Payment::factory()->create(['payment_method' => 'CASH']);
        Payment::factory()->create(['payment_method' => 'MPESA_STK']);

        $payments = $this->paymentRepository->findByPaymentMethod('CASH');

        $this->assertCount(2, $payments);
        $this->assertEquals('CASH', $payments->first()->payment_method);
    }

    public function test_can_find_payments_by_date_range()
    {
        $startDate = now()->subDays(10)->format('Y-m-d');
        $endDate = now()->subDays(5)->format('Y-m-d');

        Payment::factory()->create(['paid_at' => now()->subDays(8)]);
        Payment::factory()->create(['paid_at' => now()->subDays(6)]);
        Payment::factory()->create(['paid_at' => now()->subDays(2)]); // Outside range

        $payments = $this->paymentRepository->findByDateRange($startDate, $endDate);

        $this->assertCount(2, $payments);
    }

    public function test_can_find_payments_by_amount_range()
    {
        Payment::factory()->create(['amount' => 30000]);
        Payment::factory()->create(['amount' => 50000]);
        Payment::factory()->create(['amount' => 70000]);
        Payment::factory()->create(['amount' => 100000]); // Outside range

        $payments = $this->paymentRepository->findByAmountRange(25000, 75000);

        $this->assertCount(3, $payments);
    }

    public function test_can_find_verified_payments()
    {
        Payment::factory()->create(['verified_at' => now()]);
        Payment::factory()->create(['verified_at' => now()]);
        Payment::factory()->create(['verified_at' => null]);

        $payments = $this->paymentRepository->findVerified();

        $this->assertCount(2, $payments);
        $this->assertNotNull($payments->first()->verified_at);
    }

    public function test_can_find_unverified_payments()
    {
        Payment::factory()->create(['verified_at' => null]);
        Payment::factory()->create(['verified_at' => null]);
        Payment::factory()->create(['verified_at' => now()]);

        $payments = $this->paymentRepository->findUnverified();

        $this->assertCount(2, $payments);
        $this->assertNull($payments->first()->verified_at);
    }

    public function test_can_find_payments_with_receipts()
    {
        Payment::factory()->create(['payment_receipt' => 'receipt1.jpg']);
        Payment::factory()->create(['payment_receipt' => 'receipt2.pdf']);
        Payment::factory()->create(['payment_receipt' => null]);

        $payments = $this->paymentRepository->findWithReceipts();

        $this->assertCount(2, $payments);
        $this->assertNotNull($payments->first()->payment_receipt);
    }

    public function test_can_find_payments_without_receipts()
    {
        Payment::factory()->create(['payment_receipt' => null]);
        Payment::factory()->create(['payment_receipt' => null]);
        Payment::factory()->create(['payment_receipt' => 'receipt.jpg']);

        $payments = $this->paymentRepository->findWithoutReceipts();

        $this->assertCount(2, $payments);
        $this->assertNull($payments->first()->payment_receipt);
    }

    public function test_can_get_payment_statistics()
    {
        // Create test payments
        Payment::factory()->create([
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID,
            'payment_method' => 'CASH',
            'verified_at' => now(),
            'payment_receipt' => 'receipt.jpg'
        ]);

        Payment::factory()->create([
            'amount' => 30000,
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => 'MPESA_STK',
            'verified_at' => null,
            'payment_receipt' => null
        ]);

        Payment::factory()->create([
            'amount' => 20000,
            'status' => PaymentStatusEnum::CANCELLED,
            'payment_method' => 'BANK_TRANSFER'
        ]);

        $statistics = $this->paymentRepository->getStatistics();

        $this->assertEquals(3, $statistics['total_payments']);
        $this->assertEquals(50000, $statistics['total_amount']);
        $this->assertEquals(1, $statistics['verified_payments']);
        $this->assertEquals(2, $statistics['unverified_payments']);
        $this->assertEquals(1, $statistics['with_receipts']);
        $this->assertEquals(2, $statistics['without_receipts']);
        $this->assertArrayHasKey('status_counts', $statistics);
        $this->assertArrayHasKey('method_counts', $statistics);
        $this->assertArrayHasKey('monthly_stats', $statistics);
    }

    public function test_can_get_payments_by_month()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        Payment::factory()->create(['paid_at' => now()]);
        Payment::factory()->create(['paid_at' => now()]);
        Payment::factory()->create(['paid_at' => now()->subMonth()]);

        $payments = $this->paymentRepository->getByMonth($currentYear, $currentMonth);

        $this->assertCount(2, $payments);
    }

    public function test_can_get_payments_by_year()
    {
        $currentYear = now()->year;

        Payment::factory()->create(['paid_at' => now()]);
        Payment::factory()->create(['paid_at' => now()]);
        Payment::factory()->create(['paid_at' => now()->subYear()]);

        $payments = $this->paymentRepository->getByYear($currentYear);

        $this->assertCount(2, $payments);
    }

    public function test_can_get_total_amount_by_status()
    {
        Payment::factory()->create([
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID
        ]);

        Payment::factory()->create([
            'amount' => 30000,
            'status' => PaymentStatusEnum::PAID
        ]);

        Payment::factory()->create([
            'amount' => 20000,
            'status' => PaymentStatusEnum::PENDING
        ]);

        $totalPaid = $this->paymentRepository->getTotalAmountByStatus(PaymentStatusEnum::PAID);
        $totalPending = $this->paymentRepository->getTotalAmountByStatus(PaymentStatusEnum::PENDING);

        $this->assertEquals(80000, $totalPaid);
        $this->assertEquals(20000, $totalPending);
    }

    public function test_can_get_total_amount_by_method()
    {
        Payment::factory()->create([
            'amount' => 50000,
            'payment_method' => 'CASH',
            'status' => PaymentStatusEnum::PAID
        ]);

        Payment::factory()->create([
            'amount' => 30000,
            'payment_method' => 'CASH',
            'status' => PaymentStatusEnum::PAID
        ]);

        Payment::factory()->create([
            'amount' => 20000,
            'payment_method' => 'MPESA_STK',
            'status' => PaymentStatusEnum::PAID
        ]);

        $totalCash = $this->paymentRepository->getTotalAmountByMethod('CASH');
        $totalMpesa = $this->paymentRepository->getTotalAmountByMethod('MPESA_STK');

        $this->assertEquals(80000, $totalCash);
        $this->assertEquals(20000, $totalMpesa);
    }

    public function test_can_get_payment_trends()
    {
        // Create payments for the last 3 months
        Payment::factory()->create([
            'amount' => 50000,
            'status' => PaymentStatusEnum::PAID,
            'paid_at' => now()->subMonths(2)
        ]);

        Payment::factory()->create([
            'amount' => 30000,
            'status' => PaymentStatusEnum::PAID,
            'paid_at' => now()->subMonth()
        ]);

        Payment::factory()->create([
            'amount' => 20000,
            'status' => PaymentStatusEnum::PAID,
            'paid_at' => now()
        ]);

        $trends = $this->paymentRepository->getPaymentTrends(3);

        $this->assertCount(3, $trends);
        $this->assertArrayHasKey('month', $trends[0]);
        $this->assertArrayHasKey('count', $trends[0]);
        $this->assertArrayHasKey('amount', $trends[0]);
    }

    public function test_can_search_payments_with_criteria()
    {
        // Create test payments
        Payment::factory()->create([
            'status' => PaymentStatusEnum::PAID,
            'payment_method' => 'CASH',
            'tenant_id' => $this->tenant->id,
            'amount' => 50000,
            'verified_at' => now(),
            'payment_receipt' => 'receipt.jpg',
            'reference_number' => 'CASH123',
            'notes' => 'Test payment'
        ]);

        Payment::factory()->create([
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => 'MPESA_STK',
            'tenant_id' => $this->tenant->id,
            'amount' => 30000,
            'verified_at' => null,
            'payment_receipt' => null
        ]);

        // Search by status
        $paidPayments = $this->paymentRepository->searchPayments(['status' => PaymentStatusEnum::PAID]);
        $this->assertCount(1, $paidPayments);

        // Search by payment method
        $cashPayments = $this->paymentRepository->searchPayments(['payment_method' => 'CASH']);
        $this->assertCount(1, $cashPayments);

        // Search by tenant
        $tenantPayments = $this->paymentRepository->searchPayments(['tenant_id' => $this->tenant->id]);
        $this->assertCount(2, $tenantPayments);

        // Search by amount range
        $amountRangePayments = $this->paymentRepository->searchPayments([
            'min_amount' => 40000,
            'max_amount' => 60000
        ]);
        $this->assertCount(1, $amountRangePayments);

        // Search by verified status
        $verifiedPayments = $this->paymentRepository->searchPayments(['verified' => true]);
        $this->assertCount(1, $verifiedPayments);

        // Search by receipt status
        $withReceiptPayments = $this->paymentRepository->searchPayments(['has_receipt' => true]);
        $this->assertCount(1, $withReceiptPayments);

        // Search by reference
        $referencePayments = $this->paymentRepository->searchPayments(['reference' => 'CASH123']);
        $this->assertCount(1, $referencePayments);

        // Search by notes
        $notesPayments = $this->paymentRepository->searchPayments(['notes' => 'Test']);
        $this->assertCount(1, $notesPayments);
    }

    public function test_can_find_duplicate_payments()
    {
        Payment::factory()->create([
            'reference_number' => 'DUPLICATE123',
            'amount' => 50000,
            'paid_at' => now()
        ]);

        Payment::factory()->create([
            'reference_number' => 'DUPLICATE123',
            'amount' => 50000,
            'paid_at' => now()
        ]);

        Payment::factory()->create([
            'reference_number' => 'UNIQUE123',
            'amount' => 30000,
            'paid_at' => now()
        ]);

        $duplicates = $this->paymentRepository->findDuplicates();

        $this->assertCount(1, $duplicates);
        $this->assertEquals('DUPLICATE123', $duplicates->first()->reference_number);
    }

    public function test_can_find_refunds()
    {
        Payment::factory()->create(['amount' => -50000]); // Refund
        Payment::factory()->create(['payment_method' => 'CASH_REFUND']); // Refund
        Payment::factory()->create(['amount' => 50000]); // Regular payment

        $refunds = $this->paymentRepository->findRefunds();

        $this->assertCount(2, $refunds);
    }

    public function test_can_find_parent_payments()
    {
        Payment::factory()->create(['amount' => 50000]); // Parent payment
        Payment::factory()->create(['amount' => -50000]); // Refund
        Payment::factory()->create(['payment_method' => 'CASH_REFUND']); // Refund

        $parentPayments = $this->paymentRepository->findParentPayments();

        $this->assertCount(1, $parentPayments);
        $this->assertEquals(50000, $parentPayments->first()->amount);
    }

    public function test_can_get_recent_payments()
    {
        Payment::factory()->create(['created_at' => now()->subDays(1)]);
        Payment::factory()->create(['created_at' => now()->subDays(2)]);
        Payment::factory()->create(['created_at' => now()->subDays(3)]);

        $recentPayments = $this->paymentRepository->getRecent(2);

        $this->assertCount(2, $recentPayments);
    }

    public function test_can_get_payments_requiring_attention()
    {
        Payment::factory()->create(['status' => PaymentStatusEnum::PENDING]);
        Payment::factory()->create(['verified_at' => null]);
        Payment::factory()->create(['payment_receipt' => null]);
        Payment::factory()->create([
            'status' => PaymentStatusEnum::PAID,
            'verified_at' => now(),
            'payment_receipt' => 'receipt.jpg'
        ]);

        $attentionPayments = $this->paymentRepository->getRequiringAttention();

        $this->assertCount(3, $attentionPayments);
    }
}
