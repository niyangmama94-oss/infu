@php
$kycContent = getContent('client_kyc.content', true);
@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')

@if (auth()->user()->kv == Status::KYC_UNVERIFIED)
<div class="alert alert-info mb-4" role="alert">
    <h4 class="alert-heading">@lang('KYC Verification required')</h4>
    <hr>
    <p class="mb-0">{{ __($kycContent->data_values->verification_content) }}<a href="{{ route('user.kyc.form') }}" class="text--base"> &nbsp;@lang('Click Here to Verify')</a></p>
</div>
@elseif(auth()->user()->kv == Status::KYC_PENDING)
<div class="alert alert-warning mb-4" role="alert">
    <h4 class="alert-heading">@lang('KYC Verification pending')</h4>
    <hr>
    <p class="mb-0"> {{ __($kycContent->data_values->pending_content) }} <a href="{{ route('user.kyc.data') }}" class="text--base">&nbsp; @lang('See KYC Data')</a></p>
</div>
@endif

<div class="row justify-content-center gy-4">
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--base">
            <div class="dashboard-widget__icon">
                <i class="las la-money-check"></i>
            </div>
            <div class="dashboard-widget__content">
                <p>@lang('Current Balance')</p>
                <h4 class="title">{{ showAmount($data['current_balance']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--primary">
            <div class="dashboard-widget__icon">
                <i class="las la-wallet"></i>
            </div>
            <div class="dashboard-widget__content">
               <a href="{{ route('user.deposit.history') }}"><p>@lang('Total Deposited')</p></a>
                <h4 class="title">{{ showAmount($data['deposit_amount']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--secondary">
            <div class="dashboard-widget__icon">
                <i class="las la-exchange-alt"></i>
            </div>
            <div class="dashboard-widget__content">
                <a href="{{ route('user.transactions') }}"> <p>@lang('Total Transactions')</p></a>
                <h4 class="title">{{ $data['total_transaction'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--info">
            <div class="dashboard-widget__icon">
                <i class="las la-list"></i>
            </div>
            <div class="dashboard-widget__content">
               <a href="{{ route('user.order.all') }}"><p>@lang('Total Order')</p></a>
                <h4 class="title">{{ $data['total_order'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--success">
            <div class="dashboard-widget__icon">
                <i class="lar la-list-alt"></i>
            </div>
            <div class="dashboard-widget__content">
               <a href="{{ route('user.order.complete') }}"> <p>@lang('Completed Order')</p></a>
                <h4 class="title">{{ $data['complete_order'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-md-6 col-sm-10">
        <div class="dashboard-widget widget--danger">
            <div class="dashboard-widget__icon">
                <i class="las la-times"></i>
            </div>
            <div class="dashboard-widget__content">
              <a href="{{ route('user.order.incomplete') }}"> <p>@lang('Incompleted Order')</p></a>
                <h4 class="title">{{ $data['incomplete_order'] }}</h4>
            </div>
        </div>
    </div>
</div>
<div class="mt-4">
    <h5 class="mb-3">@lang('Latest Transactions')</h5>
    <table class="table table--responsive--lg">
        <thead>
            <tr>
                <th>@lang('Trx')</th>
                <th>@lang('Transacted')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Post Balance')</th>
                <th>@lang('Detail | Transacted')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td >
                    {{ $trx->trx }}
                </td>

                <td >
                    <span>{{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}</span>
                </td>


                <td class="budget">
                    <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                    </span>
                </td>

                <td class="budget">
                    {{ showAmount($trx->post_balance) }}
                </td>

                <td >
                    {{ __($trx->details) }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="justify-content-center text-center" colspan="100%">
                    <i class="la la-4x la-frown"></i>
                    <br>
                    {{ __($emptyMessage) }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
