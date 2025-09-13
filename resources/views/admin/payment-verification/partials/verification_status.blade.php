@if($payment->verified_at)
    <span class="badge bg-success">
        <em class="icon ni ni-check"></em>
        Verified
    </span>
    <small class="d-block text-muted">
        By: {{ $payment->verifiedBy?->name ?? 'System' }}<br>
        {{ $payment->verified_at->format('d M Y H:i') }}
    </small>
@elseif($payment->invoice_id)
    <span class="badge bg-warning">
        <em class="icon ni ni-alert"></em>
        Needs Verification
    </span>
    <small class="d-block text-muted">
        Has invoice but not verified
    </small>
@else
    <span class="badge bg-danger">
        <em class="icon ni ni-cross"></em>
        Unverified
    </span>
    <small class="d-block text-muted">
        No invoice assigned
    </small>
@endif

