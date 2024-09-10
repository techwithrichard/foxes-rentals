<div>
    <div wire:ignore x-data x-init="
        () => {
            pondFile = FilePond.create($refs.{{ $attributes->get('ref') ?? 'input' }});
            pondFile.setOptions({


            labelIdle: '{{ __('Drag & Drop your files or browse') }}',
                labelInvalidField: '{{ __('Field contains invalid files')}}',
                labelFileWaitingForSize: '{{ __('Waiting for size')}}',
                labelFileSizeNotAvailable: '{{ __('Size not available')}}',
                labelFileLoading: '{{ __('Loading')}}',
                labelFileLoadError: '{{ __('Error during load')}}',
                labelFileProcessing: '{{ __('Uploading')}}',
                labelFileProcessingComplete: '{{ __('Upload complete')}}',
                labelFileProcessingAborted: '{{ __('Upload cancelled')}}',
                labelFileProcessingError: '{{ __('Error during upload')}}',
                labelFileProcessingRevertError: '{{ __('Error during revert')}}',
                labelFileRemoveError: '{{ __('Error during remove')}}',
                labelTapToCancel: '{{ __('tap to cancel')}}',
                labelTapToRetry: '{{ __('tap to retry')}}',
                labelTapToUndo: '{{ __('tap to undo')}}',
                labelButtonRemoveItem: '{{ __('Remove')}}',
                labelButtonAbortItemLoad: '{{ __('Abort')}}',
                labelButtonRetryItemLoad: '{{ __('Retry')}}',
                labelButtonAbortItemProcessing: '{{ __('Cancel')}}',
                labelButtonUndoItemProcessing: '{{ __('Undo')}}',
                labelButtonRetryItemProcessing: '{{ __('Retry')}}',
                labelButtonProcessItem: '{{ __('Upload')}}',
                labelMaxFileSizeExceeded: '{{ __('File is too large')}}',
                labelMaxFileSize: '{{ __('Maximum file size is {filesize}')}}',
                labelMaxTotalFileSizeExceeded: '{{ __('Maximum total size exceeded')}}',
                labelMaxTotalFileSize: '{{ __('Maximum total file size is {filesize}')}}',
                labelFileTypeNotAllowed: '{{ __('File of invalid type')}}',
                fileValidateTypeLabelExpectedTypes: '{{ __('Expects {allButLastType} or {lastType}')}}',
                imageValidateSizeLabelFormatError: '{{ __('Image type not supported')}}',
                imageValidateSizeLabelImageSizeTooSmall: '{{ __('Image is too small')}}',
                imageValidateSizeLabelImageSizeTooBig: '{{ __('Image is too big')}}',

                allowMultiple: {{ $attributes->has('multiple') ? 'true' : 'false' }},
                server: {
                    process:(fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                        @this.upload('{{ $attributes->whereStartsWith('wire:model')->first() }}', file, load, error, progress)
                    },
                    revert: (filename, load) => {
                        @this.removeUpload('{{ $attributes->whereStartsWith('wire:model')->first() }}', filename, load)
                    },
                },
                allowImagePreview: {{ $attributes->has('allowImagePreview') ? 'true' : 'false' }},
                imagePreviewMaxHeight: {{ $attributes->has('imagePreviewMaxHeight') ? $attributes->get('imagePreviewMaxHeight') : '256' }},
                allowFileTypeValidation: {{ $attributes->has('allowFileTypeValidation') ? 'true' : 'false' }},
                acceptedFileTypes: {!! $attributes->get('acceptedFileTypes') ?? 'null' !!},
                allowFileSizeValidation: {{ $attributes->has('allowFileSizeValidation') ? 'true' : 'false' }},
                maxFileSize: {!! $attributes->has('maxFileSize') ? "'".$attributes->get(' maxFileSize')."'" : 'null'
        !!} }); } "

         x-on:clear-files.window=" pondFile.removeFiles(); "
    >
        <input type="file" x-ref="{{ $attributes->get('ref') ?? 'input' }}"/>
    </div>


</div>
