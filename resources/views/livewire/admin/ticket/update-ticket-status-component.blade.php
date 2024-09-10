<div class="card-tools">
    <div class="drodown">
        <a href="#"
           class="dropdown-toggle dropdown-indicator btn btn-sm btn-outline-light btn-white"
           data-bs-toggle="dropdown" aria-expanded="false">
            {{ \Illuminate\Support\Str::headline($ticket->status) }}
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs" style="">
            <ul class="link-list-opt no-bdr">
                <li>
                    <a href="javascript:void(0);"
                       wire:click.prevent="updateStatus('{{ \App\Enums\TicketStatusEnum::Open }}')">
                        <span>{{ __('OPEN')}}</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);"
                       wire:click.prevent="updateStatus('{{ \App\Enums\TicketStatusEnum::Closed }}')">
                    <span>
                        {{ __('CLOSED')}}
                    </span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0);"
                       wire:click.prevent="updateStatus('{{ \App\Enums\TicketStatusEnum::OnHold }}')">
                    <span>
                        {{ __('ON HOLD')}}
                    </span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0);"
                       wire:click.prevent="updateStatus('{{ \App\Enums\TicketStatusEnum::InProgress }}')">
                    <span>
                        {{ __('IN PROGRESS')}}
                    </span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0);"
                       wire:click.prevent="updateStatus('{{ \App\Enums\TicketStatusEnum::Pending }}')">
                    <span>
                        {{ __('PENDING')}}
                    </span>
                    </a>
                </li>


            </ul>
        </div>
    </div>
</div>
