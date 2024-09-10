@extends('layouts.web_layout')

@section('content')
<div class="header-content my-auto pt-5 pb-5 mt-3 mb-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="row align-items-center g-0">
                    <div class="col-md-7">
                        <div class="card card-shadow round-xl bg-indigo is-dark pb-4 pb-md-0">
                            <div class="card-inner card-inner-xl">
                                <div class="text-block">
                                    <h3 class="title">Location</h3>
                                    <ul class="list list-nostyle fs-16px">
                                        <li>Windsor Building, Third Floor</li>
                                        <li>University Way</li>
                                        <li>Nairobi.</li>
                                        <li>P.O. Box 123 - 30100 Nairobi</li>
                                        <li class="note text-warning">+ All are welcome
                                        </li>
                                    </ul>
                                    <ul class="btns-inline">
                                        <li><a href="#" target="_blank"
                                                class="btn btn-xl btn-primary">Call / Whatsapp</a> +254720691181
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card card-shadow round-xl ml-lg-n7 ml-md-n5 mx-4 mr-md-0 mt-md-0 mt-n4">
                            <div class="card-inner card-inner-lg">
                                <div class="form-block">
                                    <div class="section-head section-head-sm">
                                        <h4 class="title">Do you have any other question?</h4>
                                    </div>
                                    <form action="#" class="form-submit">
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <div class="form-group "><label class="form-label" for="name">Your
                                                        Name</label>
                                                    <div class="form-control-wrap"><input type="text"
                                                            class="form-control form-control-lg" id="name"
                                                            placeholder="Your Name"></div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group"><label class="form-label" for="email">Enter Your
                                                        Email</label>
                                                    <div class="form-control-wrap"><input type="text"
                                                            class="form-control form-control-lg" id="email"
                                                            placeholder="Enter Your Email">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group"><label class="form-label" for="question">Your
                                                        Question</label>
                                                    <div class="form-control-wrap"><textarea
                                                            class="form-control no-resize" id="question"
                                                            placeholder="Your Question"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12"><a href="#" class="btn btn-lg btn-primary">Ask
                                                    Question</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection