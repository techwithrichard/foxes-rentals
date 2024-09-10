<!DOCTYPE html>
<html lang="zxx" class="js">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <link rel="shortcut icon" href="{{ asset('website_assets/images/favicon.png')}}">
    <title>Home | Foxes Rental Systems</title>
    <link rel="stylesheet" href="{{ asset('website_assets/css/dashlite.css')}}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('website_assets/css/theme.css')}}">

</head>

<body class="nk-body bg-white npc-landing ">
    <div class="nk-app-root">
        <div class="nk-main ">
            <header class="header has-header-main-s1 {{ request()->is('/')? 'bg-dark':'' }} " id="home">
                @include('web.navbar')

                @yield('content')




                <!-- Start footer  -->

                <footer class="footer bg-lighter" id="footer">
                    <div class="container">
                        <div class="row g-3 align-items-center justify-content-md-between py-4 py-md-5">
                            <div class="col-md-3">
                                <div class="footer-logo"><a href="index.html" class="logo-link"><img
                                            class="logo-light logo-img" src="images/logo.png"
                                            srcset="/landing/images/logo2x.png 2x" alt="logo"><img
                                            class="logo-dark logo-img" src="images/logo-dark.png"
                                            srcset="/landing/images/logo-dark2x.png 2x" alt="logo-dark"></a></div>
                            </div>
                            <div class="col-md-9 d-flex justify-content-md-end">
                                <ul class="link-inline gx-4">
                                    <li><a href="#">Terms & Conditions</a></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                        <hr class="hr border-light mb-0 mt-n1">
                        <div class="row g-3 align-items-center justify-content-md-between py-4">
                            <div class="col-md-8">
                                <div class="text-base">Copyright © {{ now()->year }} Foxes Rental Systems.</div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-md-end">
                                <ul class="social">
                                    <li><a href="#"><em class="icon ni ni-twitter"></em></a></li>
                                    <li><a href="#"><em class="icon ni ni-facebook-f"></em></a></li>
                                    <li><a href="#"><em class="icon ni ni-instagram"></em></a></li>
                                    <li><a href="#"><em class="icon ni ni-pinterest"></em></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>

                <!-- End footer  -->

        </div>
    </div>

    <script src="{{ asset('website_assets/js/bundle.js')}}"></script>
    <script src="{{ asset('website_assets/js/scripts.js')}}"></script>
{{--    <script src="{{ asset('web/assets/js/demo-settingsd315.js')}}"></script>--}}
</body>


</html>
