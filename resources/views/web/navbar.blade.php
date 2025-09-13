<div class="header-main header-main-s1 is-sticky is-transparent on-dark">
    <div class="container header-container">
        <div class="header-wrap">
            <div class="header-logo">
                <a href="{{ url('/') }}" class="logo-link">
                    <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png')}}"
                         alt="logo">
                    <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png')}}" alt="logo-dark">
                </a>
            </div>
            <div class="header-toggle">
                <button class="menu-toggler" data-target="mainNav"><em class="menu-on icon ni ni-menu"></em><em
                        class="menu-off icon ni ni-cross"></em></button>
            </div>
            <nav class="header-menu" data-content="mainNav">
                <ul class="menu-list ml-lg-auto">
                    <li class="menu-item"><a href="#home" class="menu-link nav-link">Home</a></li>
                    <li class="menu-item"><a href="#feature" class="menu-link nav-link">Who We Serve</a>
                    </li>
                    <li class="menu-item"><a href="#landlord" class="menu-link nav-link">Landlords</a></li>
                    <li class="menu-item"><a href="#tenant" class="menu-link nav-link">Tenants</a></li>
                    <li class="menu-item"><a href="#contact" class="menu-link nav-link">Contact</a></li>
                    <li class="menu-item"><a href="#properties" class="menu-link nav-link">Properties</a></li>


                </ul>
                <ul class="menu-btns">

                    @auth
                        <li><a href="{{ route('dashboard')}}" class="btn btn-primary btn-lg">
                                My Account</a></li>

                    @else
                        <li><a href="{{ route('login')}}" class="btn btn-primary btn-lg">
                                Log in</a></li>
                    @endauth

                </ul>
            </nav>
        </div>
    </div>
</div>
