<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>FOXES  Foxes Rental Systems</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('web_assets/img/favicon.png')}}" rel="icon">
    <link href="{{ asset('web_assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('web_assets/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
    <link href="{{ asset('web_assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('web_assets/css/style.css')}}" rel="stylesheet">

    <!-- =======================================================
    * Template Name: OnePage - v4.10.0
    * Template URL: https://bootstrapmade.com/onepage-multipurpose-bootstrap-template/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center justify-content-between">

        <h1 class="logo"><a href="/">Foxes Rental Systems</a></h1>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

        <nav id="navbar" class="navbar">
            <ul>

                @if (Route::has('login'))
                    @auth
                        <li><a class="getstarted " href="{{ url('/dashboard') }}">{{ __('My Account')}}</a></li>
                    @else
                        <li><a class="nav-link " href="{{ route('login') }}">{{ __('Login')}}</a></li>
                    @endauth
                @endif
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

    </div>
</header><!-- End Header -->

<!-- ======= Hero Section ======= -->
<section id="hero" class="d-flex align-items-center">
    <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-9 text-center">
                <h1>{{ __('Foxes Rental Systems Portal')}}</h1>
                <h2>{{ __('Administer your rentals with ease')}}</h2>
            </div>
        </div>
        <div class="text-center">
            @if (Route::has('login'))
                @auth
                    <a class="btn-get-started" href="{{ url('/dashboard') }}">{{ __('My Account')}}</a>
                @else
                    <a class="btn-get-started" href="{{ route('login') }}">{{ __('Get Started')}}</a>
                @endauth
            @endif
        </div>

        <div class="row icon-boxes">
            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in"
                 data-aos-delay="200">
                <div class="icon-box">
                    <div class="icon"><i class="ri-stack-line"></i></div>
                    <h4 class="title"><a href="">Manage Invoices</a></h4>
                    <p class="description">Voluptatum deleniti atque corrupti quos dolores et quas molestias
                        excepturi</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in"
                 data-aos-delay="300">
                <div class="icon-box">
                    <div class="icon"><i class="ri-palette-line"></i></div>
                    <h4 class="title"><a href="">Manage Leases</a></h4>
                    <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                        dolore</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in"
                 data-aos-delay="400">
                <div class="icon-box">
                    <div class="icon"><i class="ri-command-line"></i></div>
                    <h4 class="title"><a href="">Manage Payments</a></h4>
                    <p class="description">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0" data-aos="zoom-in"
                 data-aos-delay="500">
                <div class="icon-box">
                    <div class="icon"><i class="ri-fingerprint-line"></i></div>
                    <h4 class="title"><a href="">Manage Rentals</a></h4>
                    <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis</p>
                </div>
            </div>

        </div>
    </div>
</section><!-- End Hero -->

<main id="main">


    <!-- ======= Cta Section ======= -->
    <section id="cta" class="cta">
        <div class="container" data-aos="zoom-in">
            <div class="text-center">
                <h3>Contact Us</h3>
                <p>For any requests,errors,querys or assistance,you can contact us via admin@foxes.co.ke </p>
                <a class="cta-btn" href="#">Call Us +254720691181</a>
            </div>
        </div>
    </section><!-- End Cta Section -->


    <!-- ======= Frequently Asked Questions Section ======= -->
    <section id="faq" class="faq section-bg">
        <div class="container" data-aos="fade-up">

            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint
                    consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia
                    fugiat sit
                    in iste officiis commodi quidem hic quas.</p>
            </div>

            <div class="faq-list">
                <ul>
                    <li data-aos="fade-up">
                        <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" class="collapse"
                                                                       data-bs-target="#faq-list-1">How do I register
                            for the service ? <i
                                class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="faq-list-1" class="collapse show" data-bs-parent=".faq-list">
                            <p>
                                Your registration is done by the admin of the system,once you have been registered,you
                                will receive
                                an email with your login credentials.
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="100">
                        <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse"
                                                                       data-bs-target="#faq-list-2"
                                                                       class="collapsed">I have not received an email
                            with my login credentials <i
                                class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="faq-list-2" class="collapse" data-bs-parent=".faq-list">
                            <p>
                                Incase you have not received an email with your login credentials,check your spam folder
                                for the
                                email.
                                If you still have not received the email,contact the admin of the system via email or
                                phone number
                                provided.
                            </p>
                        </div>
                    </li>

                </ul>
            </div>

        </div>
    </section>
    <!-- End Frequently Asked Questions Section -->


</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer">

    <div class="container d-md-flex py-4">

        <div class="me-md-auto text-center text-md-start">
            <div class="copyright">
                &copy; Copyright <strong><span>Foxes Rental Systems</span></strong>. All Rights Reserved
            </div>
            <div class="credits">

                Designed by Richard Kipkoech Murgor
            </div>
        </div>
        <div class="social-links text-center text-md-right pt-3 pt-md-0">
            <a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
            <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
        </div>
    </div>
</footer><!-- End Footer -->

<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<!-- <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script> -->
<script src="{{ asset('web_assets/vendor/aos/aos.js')}}"></script>
<script src="{{ asset('web_assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('web_assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
<script src="{{ asset('web_assets/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
<script src="{{ asset('web_assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
<!-- <script src="assets/vendor/php-email-form/validate.js"></script> -->

<!-- Template Main JS File -->
<script src="{{ asset('web_assets/js/main.js')}}"></script>

</body>

</html>
