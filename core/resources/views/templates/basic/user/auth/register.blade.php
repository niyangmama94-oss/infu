@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $policyPages = getContent('policy_pages.element', false, null, true);
        $register = getContent('user_register.content', true);
        $registerInfluencer = getContent('influencer_register.content', true);

    @endphp

@if (gs('registration'))
    <div class="account-section pt-80 pb-80">
        <div class="container">
            <div class="account-wrapper">
                <div class="row gy-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="account-thumb-wrapper">
                            <img class="mw-100 h-100" src="{{ getImage('assets/images/frontend/user_register/' . @$register->data_values->image, '660x450') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="account-content">
                            <div class="d-flex justify-content-between flex-wrap gap-3 pb-5">
                                <div class="account-content-left">
                                    <h3 class="this-page-title">{{ __(@$register->data_values->title) }}</h3>
                                </div>
                                <div class="account-content-right">
                                    <button class="btn btn--md btn--outline-base actionBtn active" data-type="client" data-influencer="false" type="button">@lang('Client')</button>
                                    <button class="btn btn--md btn--outline-base actionBtn" data-type="influencer" data-influencer="true" type="button">@lang('Influencer')</button>
                                </div>
                            </div>
                            <form class="form verify-gcaptcha" action="{{ route('user.register') }}" method="POST">
                                @csrf
                                <div class="row">

                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('First Name')</label>
                                        <input type="text" class="form-control form--control" name="firstname" value="{{old("firstname")}}" required>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('Last Name')</label>
                                        <input type="text" class="form-control form--control" name="lastname" value="{{old("lastname")}}" required>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="email">@lang('Email Address')</label>
                                            <input class="form-control form--control checkUser" id="email" name="email" type="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="password">@lang('Password')</label>
                                            <input class="form-control form--control @if (gs("secure_password")) secure-password @endif" id="password" name="password" type="password" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="password_confirm">@lang('Confirm Password')</label>
                                            <input class="form-control form--control" id="password_confirm" name="password_confirmation" type="password" required>
                                        </div>
                                    </div>

                                    <x-captcha />

                                    @if (gs("agree"))
                                        <div class="form-group">
                                            <div class="form--check">
                                                <input class="form-check-input" id="agree" type="checkbox" @checked(old('agree')) name="agree" required>
                                                <div class="form-check-label">
                                                    <label class="" for="agree"> @lang('I agree with')</label>
                                                    @foreach ($policyPages as $policy)
                                                        <a class="text--base" href="{{ route('policy.pages', [slug($policy->data_values->title), $policy->id]) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn--base w-100" id="recaptcha" type="submit">@lang('Submit')</button>
                            </form>
                            <div class="text-center">
                                <p class="mt-3">@lang('Have an account? ')
                                    <a class="text--base login-url" href="{{ route('user.login') }}">@lang('Login here')</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @include($activeTemplate.'partials.registration_disabled')
@endif

    <div class="modal fade" id="existModalCenter" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login')</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    <a class="btn btn--base btn--sm" href="{{ route('user.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="existInfuModalCenter" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    <a class="btn btn--base btn--sm" href="{{ route('influencer.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@push('script')
    <script>
        "use strict";
        (function($) {

            @if (gs("secure_password"))
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });

                $('[name=password]').focus(function() {
                    $(this).closest('.form-group').addClass('hover-input-popup');
                });

                $('[name=password]').focusout(function() {
                    $(this).closest('.form-group').removeClass('hover-input-popup');
                });
            @endif


            $('.checkUser').on('focusout', function(e) {

                var isInfluencerActive = $('.active').data('influencer');
                if (isInfluencerActive) {
                    var url = '{{ route('influencer.checkUser') }}'
                } else {
                    var url = '{{ route('user.checkUser') }}';
                }

                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false && response.type == 'email') {
                        const userType = localStorage.getItem('userType');
                        if(userType =='influencer'  ){
                            $('#existInfuModalCenter').modal('show');
                        }

                        if(userType =='client' || !isInfluencerActive){
                            $('#existModalCenter').modal('show');
                        }
                    } else if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });

            $('.actionBtn').on('click', function() {
                let action;
                let loginUrl;
                let pageTitle;
                let userType;

                if ($(this).data('type') == 'client') {
                    userType = 'client';
                    action = `{{ route('user.register') }}`;
                    loginUrl = `{{ route('user.login') }}`;
                    pageTitle = `{{ __(@$register->data_values->title) }}`;
                } else {
                    userType = 'influencer';
                    action = `{{ route('influencer.register') }}`;
                    loginUrl = `{{ route('influencer.login') }}`;
                    pageTitle = `{{ __(@$registerInfluencer->data_values->title) }}`;
                }

                localStorage.setItem('userType', userType);

                $('form')[0].action = action;

                $(this).addClass('active');
                $('.login-url').attr('href', loginUrl);
                $('.this-page-title').text(pageTitle);
                $('.actionBtn').not($(this)).removeClass('active');
            });


    $(document).ready(function() {

    const userType = localStorage.getItem('userType');
    if (userType === 'client') {

        $('form')[0].action = `{{ route('user.register') }}`;
        $('.actionBtn[data-type="influencer"]').removeClass('active');
        $('.actionBtn[data-type="client"]').addClass('active');
        $('.login-url').attr('href', `{{ route('user.login') }}`);
        $('.this-page-title').text(`{{ __(@$register->data_values->title) }}`);
    } else if(userType === 'influencer'){
        $('form')[0].action = `{{ route('influencer.register') }}`;
        $('.actionBtn[data-type="client"]').removeClass('active');
        $('.actionBtn[data-type="influencer"]').addClass('active');
        $('.login-url').attr('href', `{{ route('influencer.login') }}`);
        $('.this-page-title').text(`{{ __(@$registerInfluencer->data_values->title) }}`);
    }
});


        })(jQuery);
    </script>
@endpush
