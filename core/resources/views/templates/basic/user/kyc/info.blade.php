@extends($activeTemplate . 'layouts.master')
@section('content')
    @php
        $kycContent = getContent('client_kyc.content', true);
    @endphp
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body">
                    @if ($user->kyc_data)
                        <h5 class="my-3 text-center "> @lang('KYC Documents')</h5>
                        <ul class="list-group-flush">
                            @foreach ($user->kyc_data as $val)
                                @continue(!$val->value)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">
                                        {{ __($val->name) }}
                                    </span>
                                    <span>
                                        @if ($val->type == 'checkbox')
                                            {{ implode(',', $val->value) }}
                                        @elseif($val->type == 'file')
                                            <a href="{{ route('user.attachment.download', encrypt(getFilePath('verify') . '/' . $val->value)) }}" class="text--base">
                                                <i class="fa fa-file"></i> @lang('Attachment')
                                            </a>
                                        @else
                                            <p>{{ __($val->value) }}</p>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <h5 class="text-center">@lang('KYC data not found')</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
