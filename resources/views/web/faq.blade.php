@extends('layouts.web_layout')

@section('content')

<div class="header-content my-auto py-5 mt-n1">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="section-head">
                    <h2 class="title">Frequently Asked Questions</h2>

                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion accordion-s1 card card-shadow border-0 round-xl" id="accordion">
                    <div class="accordion-item"><a href="#" class="accordion-head" data-toggle="collapse"
                            data-target="#accordion-item-1">
                            <h6 class="title">Is this a secure site to onboard?</h6><span
                                class="accordion-icon"></span>
                        </a>
                        <div class="accordion-body collapse show" id="accordion-item-1" data-parent="#accordion">
                            <div class="accordion-inner">
                                <p>Absolutely! We work with top payment companies which guarantees your
                                    safety and security. All billing information is stored on our payment
                                    processing partner. </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item"><a href="#" class="accordion-head collapsed" data-toggle="collapse"
                            data-target="#accordion-item-2">
                            <h6 class="title">What payment services do you support?</h6><span
                                class="accordion-icon"></span>
                        </a>
                        <div class="accordion-body collapse" id="accordion-item-2" data-parent="#accordion">
                            <div class="accordion-inner">
                                <p>Mpesa</p>
                                <p>Cheques</p>
                                <p>Electronic Funds Transfer(EFT)</p>
                                <p>Cash</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item"><a href="#" class="accordion-head collapsed" data-toggle="collapse"
                            data-target="#accordion-item-3">
                            <h6 class="title">How long are your contracts?</h6><span class="accordion-icon"></span>
                        </a>
                        <div class="accordion-body collapse" id="accordion-item-3" data-parent="#accordion">
                            <div class="accordion-inner">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item"><a href="#" class="accordion-head collapsed" data-toggle="collapse"
                            data-target="#accordion-item-4">
                            <h6 class="title">Can I update my card details?</h6><span class="accordion-icon"></span>
                        </a>
                        <div class="accordion-body collapse" id="accordion-item-4" data-parent="#accordion">
                            <div class="accordion-inner">
                                <p>Consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                                    et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item"><a href="#" class="accordion-head collapsed" data-toggle="collapse"
                            data-target="#accordion-item-5">
                            <h6 class="title">Can i use the these theme for my client?</h6><span
                                class="accordion-icon"></span>
                        </a>
                        <div class="accordion-body collapse" id="accordion-item-5" data-parent="#accordion">
                            <div class="accordion-inner">
                                <p>Absolutely! We work with top payment companies which guarantees your
                                    safety and security. All billing information is stored on our payment
                                    processing partner.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection