@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Influencer')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Order')</th>
                                    @if(request()->routeIs('admin.service.index'))
                                    <th>@lang('Status')</th>
                                    @endif
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{@$service->influencer->fullname}}</span>
                                        <br>
                                        <span class="small">
                                        <a href="{{ route('admin.influencers.detail', @$service->influencer->id) }}"><span>@</span>{{ @$service->influencer->username }}</a>
                                        </span>
                                    </td>

                                    <td >
                                        <span class="fw-bold">{{__(@$service->category->name)}}</span>
                                    </td>

                                    <td>
                                        <span>{{ __(strLimit($service->title,40)) }}</span>
                                    </td>

                                    <td >
                                        <span> @lang('Total') : {{ getAmount($service->total_order_count) }}</span><br>
                                        <span> @lang('Done') : {{ getAmount($service->complete_order_count) }}</span><br>
                                    </td>

                                    @if(request()->routeIs('admin.service.index'))
                                    <td >
                                        @php echo $service->statusBadge @endphp
                                    </td>
                                    @endif

                                    <td >
                                        <a href="{{ route('admin.service.detail', $service->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-desktop"></i>@lang('Details')
                                        </a>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($services->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($services) }}
                </div>
                @endif
            </div>
        </div>


    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <x-search-form />
    </div>
@endpush
