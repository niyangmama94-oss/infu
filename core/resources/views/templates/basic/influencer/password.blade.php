@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-sm-4">
                    <form action="" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="current_password" class="form-label">@lang('Current Password')</label>
                            <input class="form-control form--control bg--body" name="current_password" id="current_password"
                                   type="password" placeholder="@lang('Enter Current Password')" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="form-label">@lang('New Password')</label>
                            <input class="form-control @if(gs("secure_password")) secure-password @endif form--control bg--body" name="password" id="new_password"
                                   type="password" placeholder="@lang('Enter New Password')" autocomplete="off" required>

                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">@lang('Confirm Password')</label>
                            <input class="form-control form--control bg--body" name="password_confirmation"
                                   id="confirm_password" type="password" placeholder="@lang('Enter Confirm Password')" required>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn--base w-100 mt-2">@lang('Change Password')</button>
                        </div>
                    </form>
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
        (function($) {
            "use strict";
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
        })(jQuery);
    </script>
@endpush
