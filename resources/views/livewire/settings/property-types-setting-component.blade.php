<div class="card-inner card-inner-lg">
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('Property Types')}}</h5>
                <span>{{ __('Static property types to select during property creation.')}}</span>
            </div><!-- .nk-block-head-content -->

            <div class="nk-block-head-content">
                <ul class="nk-block-tools gx-2">
                    <li>
                        <button data-bs-toggle="modal" data-bs-target="#modalNameEntry"
                                class="btn btn-primary d-none d-sm-inline-flex">
                            <em class="icon ni ni-plus-sm"></em><span>{{ __('Add Entry') }}</span>
                        </button>

                        <button data-bs-toggle="modal" data-bs-target="#modalNameEntry"
                                class="btn btn-icon btn-trigger d-inline-flex d-sm-none">
                            <em class="icon ni ni-plus-sm"></em>
                        </button>
                    </li>
                    <li class="d-lg-none">
                        <a href="#" class="btn btn-icon btn-trigger toggle" data-target="userAside">
                            <em class="icon ni ni-menu-right"></em>
                        </a>
                    </li>
                </ul>
            </div>
            {{--            <div class="nk-block-head-content align-self-start d-lg-none">--}}
            {{--                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"--}}
            {{--                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>--}}
            {{--            </div>--}}
        </div>
    </div><!-- .nk-block-head -->
    <div class="nk-block">

        <div class="card card-bordered card-preview">
            <table class="table table-tranx is-compact">
                <thead>
                <tr class="tb-tnx-head">
                    <th class="tb-tnx-id"><span class="">#</span></th>
                    <th>
                        <span>{{ __('Name')}}</span>
                    </th>
                    <th></th>

                </thead>
                <tbody>

                @foreach($names as $method)
                    <tr class="tb-tnx-item">
                        <td class="tb-tnx-id">
                            {{ $loop->iteration }}
                        </td>
                        <td class="tb-tnx-info">
                            {{ $method->name }}
                        </td>

                        <td class="text-end">
                            <button wire:click="updateMethod('{{ $method->id }}')"
                                    class="btn  btn-sm btn-icon btn-trigger">
                                <em class="icon ni ni-edit-fill text-purple"></em>
                            </button>
                            <button wire:click="deleteMethod('{{ $method->id }}')"
                                    onclick="return confirm('Delete this entry ?')|| event.stopImmediatePropagation();"
                                    class="btn btn-sm btn-icon btn-trigger">
                                <em class="icon ni ni-trash-fill text-danger"></em>
                            </button>
                        </td>
                    </tr>
                @endforeach


                </tbody>
            </table>
        </div><!-- .card-preview -->

    </div><!-- .nk-block-head -->

    <!-- Modal Default -->
    <div class="modal fade" tabindex="-1" id="modalNameEntry" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $is_new ? 'Add':'Update'}} {{ __('Property Type')}}</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="full-name">{{ __('Property Type')}}
                            <x-form.required/>
                        </label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control @error('name') is-invalid @enderror "
                                   wire:model.defer="name"
                                   placeholder="Add item name"
                                   id="full-name">
                            @error('name')
                            <span class="text-danger fs-12px"> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <div class="d-flex justify-content-end mt-3 mb-3">

                        <x-button loading="{{__('Saving ...')}}" wire:click="submit">
                            {{ $is_new ? __('Add Property Type'):__('Update Property Type')}}
                        </x-button>

                    </div>


                </div>


            </div>
        </div>
    </div>


    @push('scripts')

        <script>


            $(document).ready(function () {
                Livewire.on('closeModal', function () {
                    $('#modalNameEntry').modal('hide');
                })

                Livewire.on('showNameModal', function () {
                    $('#modalNameEntry').modal('show');
                })

                $('#modalNameEntry').on("hidden.bs.modal", function () {
                    Livewire.emit('resetFields')
                });
            });
        </script>

    @endpush
</div>
