<div>
    <div x-data="{textEditor:@entangle($attributes->wire('model')).defer}" x-init="()=>{var element = document.querySelector('trix-editor');
                   element.editor.insertHTML(textEditor);}" wire:ignore>

        <input x-ref="editor" id="editor-x" type="hidden" name="content">

        <trix-editor input="editor-x" x-on:trix-change="textEditor=$refs.editor.value;"></trix-editor>
    </div>

    @push('scripts')
        @once
            <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"
                    integrity="sha512-2RLMQRNr+D47nbLnsbEqtEmgKy67OSCpWJjJM394czt99xj3jJJJBQ43K7lJpfYAYtvekeyzqfZTx2mqoDh7vg=="
                    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <script>
                document.addEventListener("trix-file-accept", event => {
                    event.preventDefault()
                })
            </script>
        @endonce

    @endpush

    @push('css')
        @once
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css"
                  integrity="sha512-5m1IeUDKtuFGvfgz32VVD0Jd/ySGX7xdLxhqemTmThxHdgqlgPdupWoSN8ThtUSLpAGBvA8DY2oO7jJCrGdxoA=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

            <style>
                trix-toolbar [data-trix-button-group="file-tools"] {
                    display: none;
                }
            </style>
        @endonce
    @endpush
</div>
