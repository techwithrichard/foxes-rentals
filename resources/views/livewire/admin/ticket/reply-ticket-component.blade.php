<div class="nk-block">
    <div class="nk-block mb-3">
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="#" class="form-contact">
                    <div class="row gy-1">

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('Reply the ticket')}}</label>
                                <div class="form-control-wrap">
                                                            <textarea type="text" class="form-control form-control-lg"
                                                                      wire:model.lazy="reply_message"
                                                                      placeholder="{{ __('Write your reply here')}}"></textarea>
                                </div>
                            </div>
                        </div><!-- .col -->

                        <div class="col-12">
                            <div
                                class="form-group"
                                x-data="{ isUploading: false, progress: 1 }"
                                x-on:livewire-upload-start="isUploading = true"
                                x-on:livewire-upload-finish="isUploading = false; progress = 1"
                                x-on:livewire-upload-error="isUploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <input type="file"
                                       wire:model="attachments" multiple class="form-control form-control-file">
                                <div x-cloak x-show="isUploading" class="progress progress-lg mt-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         role="progressbar"
                                         aria-valuenow="0"
                                         aria-valuemin="0"
                                         x-bind:style="`width: ${progress}%`"
                                         aria-valuemax="100">
                                        <span x-text="progress">%</span>

                                    </div>
                                </div>

                                <span x-cloak x-show.transition="isUploading" class="fs-9px ">
                                    {{ __('Please wait for upload to complete before submitting the ticket')}}
                                </span>


                            </div>

                        </div>

                        {{--                        <div class="col-12">--}}
                        {{--                            <div class="form-group">--}}

                        {{--                                <div class="form-control-wrap">--}}
                        {{--                                    <input type="file" class="form-control form-control-file"--}}
                        {{--                                           wire:model="attachments" multiple id="attachments-form">--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div><!-- .col -->--}}

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex flex-row-reverse">
                            <div class="p-2 ">
                                <x-button loading="{{__('Sending...')}}"
                                          wire:click="submit" class="btn btn-primary">
                                    {{__('Reply')}}
                                </x-button>
                            </div>
                            <div class="p-2 ">

                                {{--                                @if($ticket_status===\App\Enums\TicketStatus::Open)--}}
                                {{--                                    <a href="javascript:void(0);" wire:click="closeTicket"--}}
                                {{--                                       class="btn btn-dim btn-outline-light">--}}
                                {{--                                        {{ __('Mark as closed')}}--}}
                                {{--                                    </a>--}}
                                {{--                                @endif--}}

                            </div>

                        </div>

                    </div><!-- .row -->
                </form><!-- .form-contact -->
            </div><!-- .card-inner -->
        </div><!-- .card -->
    </div>
</div>
