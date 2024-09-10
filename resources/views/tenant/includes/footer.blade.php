<div class="nk-footer">
    <div class="container-fluid">
        <div class="nk-footer-wrap">
            <div class="nk-footer-copyright"> &copy; {{ now()->year }} {{ __('Foxes Rental Systems.')}} {{ __('All Rights Reserved.')}}
            </div>
            <div class="nk-footer-links">
                <ul class="nav nav-sm">
                    <li class="nav-item dropup">
                        <a href="#" class="dropdown-toggle dropdown-indicator has-indicator nav-link text-base"
                           data-bs-toggle="dropdown" data-offset="0,10">
                            <span>
                                {{ Config::get('languages')[App::getLocale()]['display'] }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                            <ul class="language-list">


                                @foreach(Config::get('languages') as $lang => $language)
                                    <li>
                                        <a href="{{ route('lang.switch', $lang) }}" class="dropdown-item">
                                            <span class="language-name">{{ $language['display'] }}</span>
                                        </a>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>
