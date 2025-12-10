@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4">

                <div class="col-xxl-3 col-sm-6">

                    <x-widget
                        style="6"
                        link="{{ route('admin.report.transaction') }}?search={{ $influencer->username }}"
                        icon="las la-money-bill-wave-alt"
                        title="Balance"
                        value="{{ showAmount($influencer->balance) }}"
                        bg="primary"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.service.index') }}?search={{ $influencer->username }}"
                        icon="las la-wallet"
                        title="Services"
                        value="{{ getAmount($totalService) }}"
                        bg="success"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.withdraw.log') }}?search={{ $influencer->username }}"
                        icon="fas fa-wallet"
                        title="Withdrawals"
                        value="{{ showAmount($totalWithdrawals) }}"
                        bg="danger"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.report.transaction') }}?search={{ $influencer->username }}"
                        icon="las la-exchange-alt"
                        title="Transactions"
                        value="{{ $totalTransaction }}"
                        bg="warning"
                    />
                </div>

                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.order.pending') }}?search={{ $influencer->username }}"
                        icon="las la-hourglass-start"
                        title="Pending Order"
                        value="{{ $data['pending_order'] }}"
                        bg="warning"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.order.inprogress') }}?search={{ $influencer->username }}"
                        icon="las la-tasks"
                        title="Inprogress Order"
                        value="{{ $data['inprogress_order'] }}"
                        bg="info"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.order.jobDone') }}?search={{ $influencer->username }}"
                        icon="las la-check"
                        title="Job Done"
                        value="{{ $data['job_done_order'] }}"
                        bg="success"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.order.completed') }}?search={{ $influencer->username }}"
                        icon="las la-check-circle"
                        title="Completed Order"
                        value="{{ $data['completed_order'] }}"
                        bg="primary"
                    />
                </div>


                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.order.reported') }}?search={{ $influencer->username }}"
                        title="Reported Order"
                        icon="las la-gavel"
                        value="{{ $data['reported_order'] }}"
                        bg="success"
                        outline="true"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.order.cancelled') }}?search={{ $influencer->username }}"
                        title="Cancelled Order"
                        icon="las la-times-circle"
                        value="{{ $data['cancelled_order'] }}"
                        bg="warning"
                        outline="true"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.pending') }}?search={{ $influencer->username }}"
                        title="Pending Hiring"
                        icon="las la-spinner"
                        value="{{ $data['pending_hiring'] }}"
                        bg="danger"
                        outline="true"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.inprogress') }}?search={{ $influencer->username }}"
                        title="Inprogress Hiring"
                        icon="las la-tasks"
                        value="{{ $data['inprogress_hiring'] }}"
                        bg="primary"
                        outline="true"
                    />
                </div>


                <div class="col-xxl-3 col-sm-6">

                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.jobDone') }}?search={{ $influencer->username }}"
                        icon="las la-check-double"
                        title="Job Done Hiring"
                        value="{{ $data['job_done_hiring'] }}"
                        bg="indigo"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.completed') }}?search={{ $influencer->username }}"
                        icon="las la-check-square"
                        title="Completed Hiring"
                        value="{{ $data['completed_hiring'] }}"
                        bg="success"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.reported') }}?search={{ $influencer->username }}"
                        icon="las la-hammer"
                        title="Reported Hiring"
                        value="{{ $data['reported_hiring'] }}"
                        bg="black"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="6"
                        link="{{ route('admin.hiring.cancelled') }}?search={{ $influencer->username }}"
                        icon="las la-times"
                        title="Cancelled Hiring"
                        value="{{ $data['cancelled_hiring'] }}"
                        bg="danger"
                    />
                </div>

            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                        <i class="las la-plus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <button data-bs-toggle="modal" data-bs-target="#addSubModal"
                        class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                        <i class="las la-minus-circle"></i> @lang('Balance')
                    </button>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.report.login.history') }}?search={{ $influencer->username }}"
                        class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.influencers.notification.log', $influencer->id) }}"
                        class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>

                @if ($influencer->kyc_data)
                    <div class="flex-fill">
                        <a href="{{ route('admin.influencers.kyc.details', $influencer->id) }}" target="_blank"
                            class="btn btn--dark btn--shadow w-100 btn-lg">
                            <i class="las la-user-check"></i>@lang('KYC Data')
                        </a>
                    </div>
                @endif

                <div class="flex-fill">
                    @if ($influencer->status == 1)
                        <button type="button" class="btn btn--warning btn--gradi btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-ban"></i>@lang('Ban Influencer')
                        </button>
                    @else
                        <button type="button" class="btn btn--success btn--gradi btn--shadow w-100 btn-lg userStatus"
                            data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="las la-undo"></i>@lang('Unban Influencer')
                        </button>
                    @endif
                </div>
            </div>


            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{ $influencer->fullname }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.influencers.update', [$influencer->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required
                                        value="{{ $influencer->firstname }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required
                                        value="{{ $influencer->lastname }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    <input class="form-control" type="email" name="email"
                                        value="{{ $influencer->email }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile"
                                            class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address"
                                        value="{{ @$influencer->address }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city"
                                        value="{{ @$influencer->city }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state"
                                        value="{{ @$influencer->state }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip"
                                        value="{{ @$influencer->zip }}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country')</label>
                                    <select name="country" class="form-control">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $key }}">{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group  col-xl-3 col-md-6 col-12">
                                <label>@lang('Email Verification')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')"
                                    name="ev" @if ($influencer->ev) checked @endif>

                            </div>

                            <div class="form-group  col-xl-3 col-md-6 col-12">
                                <label>@lang('Mobile Verification')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')"
                                    name="sv" @if ($influencer->sv) checked @endif>

                            </div>
                            <div class="form-group col-xl-3 col-md- col-12">
                                <label>@lang('2FA Verification') </label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                    data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')"
                                    data-off="@lang('Disable')" name="ts"
                                    @if ($influencer->ts) checked @endif>
                            </div>
                            <div class="form-group col-xl-3 col-md- col-12">
                                <label>@lang('KYC') </label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                    data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')"
                                    data-off="@lang('Unverified')" name="kv"
                                    @if ($influencer->kv) checked @endif>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>



    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.influencers.add.sub.balance', $influencer->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control"
                                    placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($influencer->status == 1)
                            <span>@lang('Ban Influencer')</span>
                        @else
                            <span>@lang('Unban Influencer')</span>
                        @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.influencers.status', $influencer->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($influencer->status == Status::INFLUENCER_ACTIVE)
                            <h6 class="mb-2">@lang('If you ban this influencer he/she won\'t able to access his/her dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $influencer->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this influencer?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($influencer->status == Status::INFLUENCER_ACTIVE)
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                            <button type="button" class="btn btn--dark"
                                data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.influencers.login', $influencer->id) }}" target="_blank" class="btn btn-sm btn-outline--primary" ><i class="las la-sign-in-alt"></i>@lang('Login as Influencer')</a>
@endpush


@push('script')
    <script>
        (function($) {



            "use strict"
            $('.bal-btn').click(function() {
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });
            let mobileElement = $('.mobile-code');
            $('select[name=country]').change(function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            $('select[name=country]').val('{{ @$influencer->country_code }}');
            let dialCode = $('select[name=country] :selected').data('mobile_code');
            let mobileNumber = `{{ $influencer->mobile }}`;
            mobileNumber = mobileNumber.replace(dialCode, '');
            $('input[name=mobile]').val(mobileNumber);
            mobileElement.text(`+${dialCode}`);

        })(jQuery);
    </script>
@endpush
