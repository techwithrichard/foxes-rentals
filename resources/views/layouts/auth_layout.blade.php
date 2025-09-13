<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="dan mlayah">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('/assets/images/favicon.png')}}">
    <!-- Page Title  -->
    <title>Login | Foxes Foxes Rental Systems</title>
    <!-- StyleSheets  -->
    @if(app()->getLocale()=='ar')
        <link rel="stylesheet" href="{{ asset('assets/css/dashlite.rtl.min.css')}}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/dashlite.min.css')}}">
    @endif
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css')}}">
</head>

<body class="nk-body bg-white npc-general pg-auth {{ app()->getLocale()=='ar'?'has-rtl':'' }}"
      dir="{{ app()->getLocale()=='ar'?'rtl':'ltr' }}"
>
<div class="nk-app-root">
    <!-- main @s -->
    <div class="nk-main ">
        <!-- wrap @s -->
        <div class="nk-wrap nk-wrap-nosidebar">
            <!-- content @s -->
            <div class="nk-content ">
                <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                    <div class="brand-logo pb-4 text-center">
                        <a href="{{ url('/') }}" class="logo-link">
                            <img class="logo-light logo-img logo-img-lg" src="{{ asset('assets/images/logo.png')}}"
                                 srcset="{{ asset('assets/images/logo2x.png')}} 2x" alt="logo">
                            <img class="logo-dark logo-img logo-img-lg" src="{{ asset('assets/images/logo-dark.png')}}"
                                 srcset="{{ asset('assets/images/logo-dark2x.png')}} 2x" alt="logo-dark">
                        </a>
                    </div>
                    @yield('content')
                </div>
                <div class="nk-footer nk-auth-footer-full">
                    <div class="container wide-lg">
                        <div class="row g-3">
                            <div class="col-lg-6 order-lg-last">
                                <ul class="nav nav-sm justify-content-center justify-content-lg-end">

                                    <li class="nav-item dropup">
                                        <a class="dropdown-toggle dropdown-indicator has-indicator nav-link"
                                           data-bs-toggle="dropdown" data-offset="0,10">
                                            <span>
                                                  {{ Config::get('languages')[App::getLocale()]['display'] }}
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                            <ul class="language-list">

                                                @foreach(Config::get('languages') as $lang => $language)
                                                    <li>
                                                        <a href="{{ route('lang.switch', $lang) }}"
                                                           class="language-item">
                                                            <span
                                                                class="language-name">{{ $language['display'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <div class="nk-block-content text-center text-lg-start">
                                    <p class="text-soft">
                                        &copy; {{ now()->year }} Foxes Rental Systems. All Rights Reserved.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- wrap @e -->
        </div>
        <!-- content @e -->
    </div>
    <!-- main @e -->
</div>
<!-- app-root @e -->
<!-- JavaScript -->
<script src="{{ asset('assets/js/bundle.js')}}"></script>
<script src="{{ asset('assets/js/scripts.js')}}"></script>
<!-- select region modal -->
</body>

</html>
