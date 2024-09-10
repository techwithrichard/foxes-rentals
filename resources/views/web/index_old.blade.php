@extends('web.web_layout')

@section('content')
    <div class="header-content my-auto py-5 is-dark">
        <div class="container">
            <div class="row flex-row-reverse align-items-center justify-content-between g-gs">
                <div class="col-lg-6 mb-n3 mb-lg-0">
                    <div class="header-image header-image-s2"><img
                            src="{{ asset('website_assets/images/gfx/gfx-c.png')}}"
                            alt="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="header-caption">
                        <div class="header-badge">
                            <div class="badge-pro">
                                <span class="badge bg-danger rounded-pill">NEW</span>
                                <span class="badge-pro-text">Foxes Rentals Management System</span>
                            </div>
                        </div>
                        <h3 class="header-title pr-1">{{ __('Modernize your rentals with one platform.')}}</h3>
                        <div class="header-text pr-5">
                            <p>{{ __('Feel good about the way you manage your rentals.Finally, apartment
                            management software that gives you the clarity to focus on what matters
                            most. Intuitively designed to delight your tenants.')}}</p>
                        </div>
                        <ul class="header-action btns-inline">
                            <li><a href="{{ route('login') }}" class="btn btn-danger btn-round btn-lg">
                                    <span>{{ __('Let\'s Start')}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </header>
    <section class="section section-service pb-0" id="feature">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-sm-7 col-md-6 col-9">
                    <div class="section-head">
                        <h2 class="title">{{ __('We provide various kind of service for you')}}</h2>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row justify-content-center text-center g-gs">
                    <div class="col-9 col-sm-7 col-md-4">
                        <div class="service service-s2">
                            <div class="service-icon styled-icon styled-icon-s2 bg-primary">
                                <svg x="0px" y="0px"
                                     viewBox="0 0 512 512" style="fill:currentColor" xml:space="preserve">
                                <path
                                    d="M488.4,492h-21.9V173.5c0-14.8-12.1-26.9-26.9-26.9h-49c-14.8,0-26.9,12.1-26.9,26.9V492H308V317.8		c0-14.8-12.1-26.9-26.9-26.9h-49c-14.8,0-26.9,12.1-26.9,26.9V492h-55.7v-90.2c0-14.8-12.1-26.9-26.9-26.9h-49		c-14.8,0-26.9,12.1-26.9,26.9V492H23.6c-5.5,0-10,4.5-10,10s4.5,10,10,10h464.8c5.5,0,10-4.5,10-10S493.9,492,488.4,492L488.4,492z		M129.5,492H66.7v-90.2c0-3.8,3.1-6.9,6.9-6.9h49c3.8,0,6.9,3.1,6.9,6.9L129.5,492z M288,492h-62.8V317.8c0-3.8,3.1-6.9,6.9-6.9h49		c3.8,0,6.9,3.1,6.9,6.9V492z M446.5,492h-62.8V173.5c0-3.8,3.1-6.9,6.9-6.9h49c3.8,0,6.9,3.1,6.9,6.9L446.5,492z"/>
                                    <path
                                        d="M466.4,10.5c0.1-2.7-0.8-5.5-2.9-7.6s-4.9-3-7.6-2.9c-0.2,0-0.3,0-0.5,0H395c-5.5,0-10,4.5-10,10s4.5,10,10,10h37.4		l-98.9,98.9l-37.3-37.3c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2,1.1-7.1,2.9L102.3,261.3c-3.9,3.9-3.9,10.2,0,14.1c2,2,4.5,2.9,7.1,2.9		s5.1-1,7.1-2.9l172.7-172.7l37.3,37.3c3.9,3.9,10.2,3.9,14.1,0L446.5,34.1V68c0,5.5,4.5,10,10,10s10-4.5,10-10V11		C466.5,10.8,466.4,10.7,466.4,10.5L466.4,10.5z"/>
                                    <circle cx="75.6" cy="303.3" r="10"/>
                            </svg>
                            </div>
                            <div class="service-text">
                                <h4 class="title">{{ __('Property Owner')}}</h4>
                                <p>{{ __('We get it - there is no such thing as a slow day in property management')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-9 col-sm-7 col-md-4">
                        <div class="service service-s2">
                            <div class="service-icon styled-icon styled-icon-s2 bg-pink">
                                <svg viewBox="0 0 512 512"
                                     style="fill:currentColor" xml:space="preserve">
                                <g>
                                    <path
                                        d="M462.5,276.9V115c28-4,49.5-28.1,49.5-57.1C512,25.9,486.1,0,454.2,0s-57.8,25.9-57.8,57.8H0v330.3h248.2		c4.3,69,61.6,123.9,131.7,123.9c72.9,0,132.1-59.3,132.1-132.1C512,338.3,492.6,301.1,462.5,276.9z M454.2,16.5		c22.8,0,41.3,18.5,41.3,41.3S477,99.1,454.2,99.1s-41.3-18.5-41.3-41.3S431.4,16.5,454.2,16.5z M16.5,74.3h382.3		c2,6.6,5.1,12.7,9.1,18l-50.5,50.5c-3.2-1.6-6.8-2.5-10.6-2.5s-7.4,0.9-10.6,2.5l-16.6-16.6c1.5-3.2,2.5-6.8,2.5-10.6		c0-13.7-11.1-24.8-24.8-24.8c-13.7,0-24.8,11.1-24.8,24.8c0,3.8,0.9,7.4,2.5,10.6l-41.4,41.4c-3.2-1.5-6.8-2.5-10.6-2.5		c-13.7,0-24.8,11.1-24.8,24.8s11.1,24.8,24.8,24.8s24.8-11.1,24.8-24.8c0-3.8-0.9-7.4-2.5-10.6l41.4-41.4c3.2,1.5,6.8,2.5,10.6,2.5		s7.4-0.9,10.6-2.5l16.6,16.6c-1.5,3.2-2.5,6.8-2.5,10.6c0,13.7,11.1,24.8,24.8,24.8c13.7,0,24.8-11.1,24.8-24.8		c0-3.8-0.9-7.4-2.5-10.6l50.5-50.5c7.6,5.7,16.5,9.5,26.3,10.9v150.7c-19.5-11.3-42-17.9-66.1-17.9c-39.5,0-74.9,17.5-99.1,45V256		h-49.5v99.1h18.9c-1,5.4-1.6,10.9-2,16.5H16.5V74.3z M346.8,156.9c4.6,0,8.3,3.7,8.3,8.3c0,4.6-3.7,8.3-8.3,8.3		c-4.6,0-8.3-3.7-8.3-8.3C338.6,160.6,342.3,156.9,346.8,156.9z M289,115.6c0-4.6,3.7-8.3,8.3-8.3c4.6,0,8.3,3.7,8.3,8.3		c0,4.6-3.7,8.3-8.3,8.3C292.7,123.9,289,120.2,289,115.6z M231.2,189.9c0,4.6-3.7,8.3-8.3,8.3s-8.3-3.7-8.3-8.3s3.7-8.3,8.3-8.3		S231.2,185.4,231.2,189.9z M379.9,445.9c-36.4,0-66.1-29.6-66.1-66.1s29.6-66.1,66.1-66.1s66.1,29.6,66.1,66.1		S416.3,445.9,379.9,445.9z M264.3,316c-4,7.1-7.2,14.7-9.8,22.5h-6.7v-66.1h16.5V316z M371.6,264.7v33		c-41.7,4.2-74.3,39.4-74.3,82.2c0,12.1,2.7,23.5,7.4,33.9L276,430.3c-7.4-15.3-11.7-32.3-11.7-50.4		C264.3,318.9,311.7,268.9,371.6,264.7L371.6,264.7z M379.9,495.5c-39.8,0-74.9-20.2-95.7-50.8l28.8-16.6		c15,20.8,39.4,34.4,66.9,34.4s51.9-13.6,66.9-34.4l28.8,16.6C454.8,475.3,419.6,495.5,379.9,495.5z M483.7,430.3l-28.6-16.5		c4.7-10.4,7.4-21.8,7.4-33.9c0-42.7-32.7-78-74.3-82.2v-33c59.9,4.3,107.4,54.2,107.4,115.2C495.5,398,491.2,415,483.7,430.3		L483.7,430.3z"/>
                                    <path
                                        d="M379.9,330.3c-27.3,0-49.5,22.2-49.5,49.5c0,27.3,22.2,49.5,49.5,49.5s49.5-22.2,49.5-49.5		C429.4,352.5,407.2,330.3,379.9,330.3z M379.9,412.9c-18.2,0-33-14.8-33-33c0-18.2,14.8-33,33-33c18.2,0,33,14.8,33,33		C412.9,398.1,398.1,412.9,379.9,412.9z"/>
                                    <path d="M33,90.8h16.5v16.5H33V90.8z"/>
                                    <path d="M66.1,90.8h115.6v16.5H66.1V90.8z"/>
                                    <path d="M33,123.9h16.5v16.5H33V123.9z"/>
                                    <path d="M66.1,123.9h115.6v16.5H66.1V123.9z"/>
                                    <path d="M33,156.9h16.5v16.5H33V156.9z"/>
                                    <path d="M66.1,156.9h115.6v16.5H66.1V156.9z"/>
                                    <path d="M33,189.9h16.5v16.5H33V189.9z"/>
                                    <path d="M66.1,189.9h115.6v16.5H66.1V189.9z"/>
                                    <path d="M33,355.1h49.5v-82.6H33V355.1z M49.5,289h16.5v49.5H49.5V289z"/>
                                    <path d="M99.1,355.1h49.5V289H99.1V355.1z M115.6,305.5h16.5v33h-16.5V305.5z"/>
                                    <path d="M165.2,355.1h49.5V223h-49.5V355.1z M181.7,239.5h16.5v99.1h-16.5V239.5z"/>
                                </g>
                            </svg>
                            </div>
                            <div class="service-text">
                                <h4 class="title">{{ __('Landlord')}}</h4>
                                <p>{{ __('Take control over your business by deploying an all in one business data monitoring
                                    solution.')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-9 col-sm-7 col-md-4">
                        <div class="service service-s2">
                            <div class="service-icon styled-icon styled-icon-s2 bg-success">
                                <svg x="0px" y="0px"
                                     viewBox="0 0 512 512" style="fill:currentColor" xml:space="preserve">
                                <g>
                                    <g>
                                        <g>
                                            <path
                                                d="M472,236.7c3.7-10.2,5.7-21,5.6-31.9c-0.1-42-28.1-78.9-68.5-90.3C406.5,49.5,352.3-1.3,287.4,0S170.5,54.5,170.5,119.4				c0,3.4,0.2,6.8,0.5,10.2c-27.5-5.3-55.9,1.9-77.5,19.8s-34,44.4-34,72.4c0,3,0.2,6.1,0.5,9.1c-35.7,4.4-61.9,35.9-59.7,71.8				s31.8,64,67.8,64.2H135c11.3,40.7,40.7,100.9,116.6,144c2.6,1.5,5.8,1.5,8.4,0c75.9-43.1,105.3-103.3,116.6-144h66.9				c32.1,0,59.9-22.3,66.7-53.6C517.1,282,501.2,250.1,472,236.7z M366.8,322.7c-0.4,10.9-1.9,21.7-4.6,32.2				c-0.3,0.8-0.5,1.6-0.6,2.4c-9.2,36.5-34.7,94.4-105.8,136.4c-71.1-42-96.6-99.8-105.8-136.4c-0.1-0.8-0.3-1.6-0.6-2.4				c-2.6-10.5-4.2-21.3-4.5-32.2V213.3c0-4.7,3.8-8.5,8.5-8.5h204.8c4.7,0,8.5,3.8,8.5,8.5L366.8,322.7L366.8,322.7z M479.8,334.8				L479.8,334.8c-9.6,9.7-22.6,15.1-36.2,15h-62.9c1.8-9,2.9-18.1,3.1-27.2V213.3c0-14.1-11.5-25.6-25.6-25.6H153.4				c-14.1,0-25.6,11.5-25.6,25.6v109.3c0.2,9.1,1.3,18.2,3.1,27.2H68.1c-27.3,0-49.8-21.4-51.1-48.7c-1.3-27.3,19-50.8,46.2-53.4				c4.6,16.3,13.6,31,26,42.5c2.2,2.2,5.4,3,8.4,2.1c3-0.9,5.3-3.3,5.9-6.3c0.7-3-0.3-6.2-2.7-8.3c-11.6-10.8-19.5-25-22.6-40.6				c-5.5-27,3.9-54.9,24.6-73.1s49.6-23.9,75.6-15c0.2,0,0.3,0.1,0.5,0.1c0.6,0.1,1.1,0.2,1.7,0.2c0.5,0.1,1.1,0.1,1.6,0				c0.2,0,0.4,0,0.5,0c0.4-0.1,0.7-0.3,1-0.4c0.5-0.2,1.1-0.4,1.6-0.6c0.5-0.3,0.9-0.6,1.3-1c0.9-0.7,1.6-1.6,2-2.6				c0.2-0.3,0.4-0.6,0.5-0.9c0.1-0.2,0-0.3,0.1-0.5c0.1-0.6,0.2-1.1,0.2-1.7c0.1-0.6,0.1-1.1,0-1.7c0-0.2,0-0.3,0-0.5				c-1.3-6.6-1.9-13.3-2-20c0.1-36.6,19.7-70.3,51.4-88.5s70.8-18.1,102.4,0.3s51.1,52.2,51,88.8c0,0.4-0.1,0.7-0.1,1.1				c-0.1,15-3.6,29.7-10.2,43.2c-2.1,4.2-0.3,9.4,3.9,11.4c4.2,2.1,9.4,0.3,11.4-3.9c6-12.4,9.9-25.8,11.2-39.6				c31,10.6,51.9,39.8,52,72.6c0,11.1-2.4,22-7,32.1c-0.2,0.3-0.4,0.6-0.5,1c-7,14.9-18.7,27.1-33.2,34.8c-3.4,1.9-5.2,5.8-4.2,9.6				c1,3.8,4.4,6.5,8.3,6.5c1.4,0,2.8-0.3,4-1c15.3-8.2,27.9-20.6,36.6-35.6c15.3,6.9,26.3,20.9,29.4,37.4				C496.9,306,491.6,322.9,479.8,334.8z"/>
                                            <path
                                                d="M332.6,221.8c-4.7,0-8.5,3.8-8.5,8.5s3.8,8.5,8.5,8.5v83.6c-0.1,3.2-2.2,79.5-81.6,133.7c-2.6,1.7-4.1,4.6-3.9,7.7				c0.2,3.1,2.1,5.8,4.9,7.2s6.1,1,8.6-0.8c86.7-59,89-143.9,89.1-147.7v-83.7C349.7,229.5,342,221.8,332.6,221.8z"/>
                                            <path
                                                d="M179,322.7v-83.7h93.9c4.7,0,8.5-3.8,8.5-8.5s-3.8-8.5-8.5-8.5H179c-9.4,0-17.1,7.6-17.1,17.1v83.7				c2.4,34.9,14.8,68.4,35.8,96.4c2.8,3.8,8.1,4.7,11.9,1.9s4.7-8.1,1.9-11.9C192.7,383.9,181.5,354,179,322.7z"/>
                                            <path
                                                d="M313.1,283c-3.3-3.3-8.7-3.3-12.1,0l-63.3,63.3l-10-10c-3.3-3.2-8.7-3.2-12,0.1c-3.3,3.3-3.3,8.6-0.1,12l16,16				c3.3,3.3,8.7,3.3,12.1,0l69.3-69.3C316.4,291.7,316.4,286.3,313.1,283z"/>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            </div>
                            <div class="service-text">
                                <h4 class="title">{{ __('Tenants')}}</h4>
                                <p>{{ __('Your tenants will love this. Online portals provide easy access to any action.')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="section section-feature " id="landlord">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-5">
                    <div class="img-block img-block-s1 left"><img src="{{ asset('website_assets/images/gfx/a.png')}}"
                                                                  alt="Dashlite">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-block pr-xl-5">
                        <h2 class="title">{{ __('Property Owner')}}</h2>
                        <p>{{ __('Your residents expect more than ever before.We help you manage the following')}} </p>
                        <ul class="list list-lg list-success list-checked-circle outlined">
                            <li>{{ __('Create and manage tenants')}}</li>
                            <li>{{ __('Create and manage properties and units')}}</li>
                            <li>{{ __('Consolidate all payments from rent,expenses and bills')}}</li>
                            <li>{{ __('Handle payments and update financials')}}</li>
                            <li>{{ __('Lease applications management')}}</li>
                            <li>{{ __('Resolve maintenance issues')}}</li>
                            <li>{{ __('On-demand printable reports')}}</li>
                        </ul>
                        <ul class="btns-inline">
                            <li><a href="{{ route('login') }}"
                                   class="btn btn-lg btn-primary">{{ __('Explore The Platform')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start tenants section -->
    <section class="section section-feature bg-lighter " id="tenant">
        <div class="container">
            <div class="row flex-row-reverse g-gs justify-content-between align-items-center">
                <div class="col-lg-6">
                    <div class="img-block "><img src="{{ asset('website_assets/images/gfx/f.png')}}" alt="contractors">
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="text-block pr-xl-5">
                        <h2 class="title">{{ __('Tenants Module')}}</h2>
                        <p>{{ __('Tenants portal serves up data and reports to tenants, reducing phone calls and
                            emails.')}}
                        </p>
                        <ul class="list list-lg list-success list-checked-circle outlined">
                            <li>{{ __('Pay rent online or manually')}}</li>
                            <li>{{ __('View due and past payments history')}}</li>
                            <li>{{ __('View rental agreements')}}</li>
                            <li>{{ __('View invoicing history')}}</li>
                            <li>{{ __('Online maintenance requests')}}</li>
                        </ul>
                        <ul class="btns-inline">
                            <li><a href="{{ route('login') }}"
                                   class="btn btn-lg btn-primary">{{ __('Explore The Portal')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End tenants section -->



    <!--Start CTA-->
    <section class="section section-cta is-dark" id="cta">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-9 col-md-10">
                    <div class="text-block is-compact py-3">
                        <h2 class="title">{{ __('All Portfolios. All Unit Counts.')}} </h2>
                        <p>{{ __('Whether you manage five doors or five thousand, you need a simple, unified platform that
                            powers you to be your best.Choose Foxes Foxes Rental Systemss today so you can scale your
                            tomorrow.')}}
                        </p>
                        <ul class="btns-inline justify-center pt-2">
                            <li><a href="#" class="btn btn-xl btn-primary btn-round">{{ __('Call Us Today')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-image bg-overlay after-bg-dark after-opacity-90"><img
                src="{{ asset('web_assets/img/about-video.jpg') }}" alt="">
        </div>
    </section>
    <!--End CTA-->


    <!-- Start Contact -->
    <section class="section section-cta bg-white">
        <div class="container">
            <!-- <div class="row justify-content-center"> -->
                <div class="col-xl-10">
                    <div class="row align-items-center g-0">
                        <div class="col-md-7">
                            <div class="card card-shadow round-xl bg-indigo is-dark pb-4 pb-md-0">
                                <div class="card-inner card-inner-xl">
                                    <div class="text-block">
                                        <h3 class="title">Single User</h3>
                                        <ul class="list list-nostyle fs-16px">
                                            <li>Meta Description Optimization</li>
                                            <li>Baseline Ranking Report</li>
                                            <li>Online 24/7 support</li>
                                            <li>Free Simple Website</li>
                                            <li class="note text-warning">+ All future update releases for Free</li>
                                        </ul>
                                        <ul class="btns-inline">
                                            <li><a href="" target="_blank" class="btn btn-xl btn-primary">Purchase Now
                                                    for $24</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .col -->
                        <div class="col-md-5">
                            <div class="card card-shadow round-xl ms-lg-n7 ms-md-n5 mx-4 me-md-0 mt-md-0 mt-n4">
                                <div class="card-inner card-inner-lg">
                                    <div class="form-block">
                                        <div class="section-head section-head-sm">
                                            <h4 class="title">Do you have any other question?</h4>
                                        </div>
                                        <form action="#" class="form-submit">
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <div class="form-group ">
                                                        <label class="form-label" for="name">Your Name</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="name" placeholder="Your Name">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="email">Enter Your Email</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control form-control-lg"
                                                                   id="email" placeholder="Enter Your Email">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="question">Your Question</label>
                                                        <div class="form-control-wrap">
                                                            <textarea class="form-control no-resize" id="question"
                                                                      placeholder="Your Question"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <a href="#" class="btn btn-lg btn-primary">Ask Question</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .col -->
            </div><!-- .row -->
        </div><!-- .container -->
    </section><!-- .section -->
    <!-- End Contact -->
@endsection
