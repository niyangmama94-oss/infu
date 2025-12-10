@extends($activeTemplate . 'layouts.master')
@section('content')
    <table class="table table--responsive--lg">
        <thead>
            <tr>
                <th>@lang('Subject')</th>
                <th>@lang('Status')</th>
                <th>@lang('Priority')</th>
                <th>@lang('Last Reply')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($supports as $key => $support)
                <tr>
                    <td>
                        <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold">
                            [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}</a>
                    </td>
                    <td>
                        @php echo $support->statusBadge; @endphp
                    </td>
                    <td>
                        @if ($support->priority == Status::PRIORITY_LOW)
                            <span class="badge badge--dark">@lang('Low')</span>
                        @elseif($support->priority == Status::PRIORITY_MEDIUM )
                            <span class="badge badge--success">@lang('Medium')</span>
                        @elseif($support->priority == Status::PRIORITY_HIGH)
                            <span class="badge badge--primary">@lang('High')</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}
                    </td>

                    <td>
                        <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--sm btn--outline-base">
                            <i class="la la-desktop"></i> @lang('View')
                        </a>
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
    @if ($supports->hasPages())
    <div class=" py-4">
        {{ paginateLinks($supports) }}
    </div>
    @endif
@endsection
