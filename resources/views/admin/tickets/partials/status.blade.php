@switch($ticket->status)
    @case(\App\Enums\TicketStatusEnum::Open->value)
    <span class="badge bg-primary">{{ __('Open')}}</span>
    @break
    @case(\App\Enums\TicketStatusEnum::Closed->value)
    <span class="badge bg-light">{{ __('Closed')}}</span>
    @break

    @case(\App\Enums\TicketStatusEnum::Pending->value)
    <span class="badge bg-warning">{{ __('Pending')}}</span>
    @break

    @case(\App\Enums\TicketStatusEnum::InProgress->value)
    <span class="badge bg-info">{{ __('In Progress')}}</span>
    @break

    @case(\App\Enums\TicketStatusEnum::OnHold->value)
    <span class="badge bg-danger">{{ __('On Hold')}}</span>
    @break


    @default

@endswitch
