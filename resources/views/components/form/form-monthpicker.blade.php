<div>

    @push('header-css')
        @once
            <link rel="stylesheet"
                  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css"
                  integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A=="
                  crossorigin="anonymous"/>
        @endonce
    @endpush


    <div class="form-control-wrap">
        <div class="form-icon form-icon-left">
            <em class="icon ni ni-calendar"></em>
        </div>
        <input {{ $attributes }} type="text" class="form-control bg-white"
               data-provide="datepicker"
               data-date-format="yyyy-mm"
               data-date-autoclose="true"
               data-date-clear-btn="true"
               data-date-start-view="months"
               data-date-min-view-mode="months"
               data-date-today-highlight="true"
               placeholder="{{ __('Choose month')}}"
               readonly
               id="{{ uniqid() }}"
               onchange="this.dispatchEvent(new InputEvent('input'))">
    </div>


    @push('footer-scripts')
        @once
            <script
                src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
                integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
                crossorigin="anonymous">
            </script>
        @endonce
    @endpush


</div>
