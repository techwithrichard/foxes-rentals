<div>
    <div class="nk-block">
        <div class="nk-body">
            <div class="card card-preview">
                <div class="card-inner">

                    <h6 class="title text-muted mb-3">{{ __('Ticket Details')}}</h6>

                    <div class="row g-gs">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"
                                       for="reportType">{{ __('Associate With A Property Or A Unit')}}
                                    <x-form.required/>
                                </label>
                                <div class="">
                                    <select class="form-select form-control form-control-lg" id="reportType"
                                            wire:model="activeUnit">
                                        <option value="">{{ __('Select')}}</option>
                                        @foreach($leased_properties_and_units as $index=>$item)
                                            <option value="{{ $index }}">{{ $item['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div><!-- .col -->

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="reportType">{{ __('Ticket Subject')}}
                                    <x-form.required/>
                                </label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                           wire:model.defer="subject">
                                </div>

                                @error('is-invalid')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div><!-- .col -->
                        <!--col-->
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">{{ __('Description')}}
                                    <x-form.required/>
                                </label>
                                <div class="form-control-wrap">
                                    <x-form.text-editor wire:model="description"></x-form.text-editor>
                                </div>

                                @error('description')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div
                                class="form-group"
                                x-data="{ isUploading: false, progress: 1 }"
                                x-on:livewire-upload-start="isUploading = true"
                                x-on:livewire-upload-finish="isUploading = false; progress = 1"
                                x-on:livewire-upload-error="isUploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >

                                <label class="form-label">{{ __('Upload Images')}}

                                </label>

                                <x-form-pond wire:model="attachments" multiple="true"></x-form-pond>

                                <input type="file"
                                       wire:model="attachments" multiple
                                       class="form-control form-control-file @error('attachments') is-invalid @enderror">

                                @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror

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


                        @if(session()->has('error'))
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            </div>


                    @endif

                    <!--col-->
                        <div class="col-12">
                            <ul class="align-center justify-content-end flex-wrap flex-sm-nowrap gx-4 gy-2">
                                <li>
                                    <x-button class="btn btn-primary" loading="{{__('Submitting...')}}"
                                              wire:click="submitTicket">
                                        {{ __('Submit Ticket')}}
                                    </x-button>
                                </li>

                            </ul>
                        </div><!-- .col -->
                    </div>
                </div>
                <!--card inner-->
            </div>

            <!--card-->
        </div>
    </div>

</div>
