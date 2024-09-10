<div class=" ">
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="nk-tb-list nk-tb-ulist is-compact">
                <div class="nk-tb-item nk-tb-head">
                    <div class="nk-tb-col"><span class="fw-bold">#</span></div>

                    <div class="nk-tb-col"><span
                            class="fw-bold">{{ __('Document Name')}}</span></div>
                    <div class="nk-tb-col"><span
                            class="fw-bold">{{ __('Upload Date')}}</span></div>
                    <div class="nk-tb-col nk-tb-col-tools text-end"></div>
                </div><!-- .nk-tb-item -->

                @forelse($documents as $document)
                    <div class="nk-tb-item">
                        <div class="nk-tb-col">
                            <span>{{ $loop->iteration }}</span>
                        </div>
                        <div class="nk-tb-col">
                            <span>{{ $document->name }}</span>
                        </div>
                        <div class="nk-tb-col">
                            <span>{{ $document->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="nk-tb-col nk-tb-col-tools">
                            <ul class="nk-tb-actions gx-2">
                                <li class="nk-tb-action">
                                    <a href="{{ url($document->path) }}"
                                       class="btn btn-sm btn-icon btn-trigger"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top" title=""
                                       download
                                       target="_blank"
                                       data-bs-original-title="Download">
                                        <em class="icon ni ni-download"></em>
                                    </a>
                                </li>

                                <li class="nk-tb-action">
                                    <a href="javascript:void(0);"
                                       wire:click.prevent="deleteDocument({{ $document->id }})"
                                       onclick="return confirm('{{ __('Are you sure you want to delete this document?')}}')||event.stopImmediatePropagation()"
                                       class="btn btn-sm btn-icon btn-trigger"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top" title=""
                                       data-bs-original-title="Delete">
                                        <em class="icon ni ni-trash"></em>
                                    </a>
                                </li>

                            </ul>

                        </div>
                    </div>

                @empty

                @endforelse

            </div><!-- .nk-tb-list -->
        </div><!-- .card -->
    </div>

    <hr>
    <div class="nk-block">
        <h6 class="title">{{ __('Upload More Documents')}}</h6>
        <div class="form-group">
            <x-form-pond wire:model="identity_documents" multiple="true"/>
            <div class="form-text">
                {{ __('For better file identification,rename the file to the actual identity document name,for example,if the
                document is a passport,then rename the file to passport.jpg')}}

            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif


        <x-button wire:click="submit" loading="{{__('Saving...')}}" class="btn btn-primary">
            {{ __('Upload Documents') }}
        </x-button>


    </div>
</div>
