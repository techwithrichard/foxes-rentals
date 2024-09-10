@extends('layouts.tenant_layout')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">


                    <div class="kyc-app wide-sm m-auto">
                        <div class="nk-block-head nk-block-head-lg wide-xs mx-auto">
                            <div class="nk-block-head-content text-center">
                                <h2 class="nk-block-title fw-normal">
                                    Dear {{ auth()->user()->name }}
                                </h2>
                                <div class="nk-block-des">
                                    <strong>
                                        Shortly you will receive an M-PESA prompt on your phone requesting you to enter
                                        your
                                        M-PESA PIN to complete your payment. Please ensure your phone is on and unlocked
                                        to enable you to complete the process. Thank you.
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="nk-block">
                            <div class="card card-bordered">
                                <div class="card-inner card-inner-lg">
                                    <div class="nk-kyc-app p-sm-2 ">

                                        <div class="nk-kyc-app-text mx-auto">
                                            <p class="lead">
                                                You can also pay using Lipa na MPESA by using the following
                                                instructions:
                                            <ol>
                                                <li>1 Go to the M-PESA menu</li>
                                                <li>2 Select Lipa na M-PESA</li>
                                                <li>3 Select the Paybill option</li>
                                                <li>4 Enter business number <strong>174379</strong></li>
                                                <li>5 Enter your account number <strong>{{ $reference }}</strong></li>
                                                <li>6 Enter the
                                                    amount <strong>{{ number_format(ceil($amount)) }}</strong></li>
                                                <li>7 Enter PIN and press OK to send</li>
                                                <li>8 You will receive a confirmation SMS with your payment reference
                                                    number.
                                                </li>
                                            </ol>
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="text-center pt-4 pb-3"><p>If you have any question, please contact our support
                                    team
                                    <a href="mailto:info@techwithrichard.com">0720 691 181</a></p></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

@endsection

