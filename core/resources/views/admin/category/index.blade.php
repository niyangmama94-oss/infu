@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td >{{ $categories->firstItem() + $loop->index }}</td>
                                        <td>{{ __($category->name) }}</td>
                                        <td >
                                         @php

                                             echo $category->statusBadge
                                         @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-sm btn-outline--primary editButton" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-status="{{ $category->status }}" data-image="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($category->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success   confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to enable this category?')" data-btn_class="btn btn--primary btn--md" type="button">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger  confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to disable this category?')" data-btn_class="btn btn--primary btn--md" type="button">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                            </div>

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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="categoryModal" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="createModalLabel"></h4>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <div class="col-sm-12">
                                <input class="form-control" name="name" type="text" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="image1">@lang('Image')</label>
                                    <x-image-uploader name="image" class="w-100" :imagePath="getImage(getFilePath('category'), getFileSize('category'))" :size="getFileSize('category')" :required="true" />

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" id="btn-save" type="submit" value="add">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-colum flex-wrap gap-2 justify-content-end align-items-center">
        <button class="btn btn-sm btn-outline--primary createButton"><i class="las la-plus"></i>@lang('Add New')</button>
        <x-search-form />
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            let modal = $('#categoryModal');
            $('.createButton').on('click', function() {
                let imgPath ="{{ getImage(getFilePath('category'), getFileSize('category')) }}";
                modal.find('.modal-title').text(`@lang('Add New Category')`);
                modal.find('form').attr('action', `{{ route('admin.category.store', '') }}`);
                modal.find('.image-upload-preview').css('background-image', `url(${imgPath})`);
                modal.find('.image1').addClass("required");

                modal.modal('show');
            });

            $('.editButton').on('click', function() {
                let imgPath = $(this).data('image');
                modal.find('.image1').prop('required', false);
                modal.find('form').attr('action', `{{ route('admin.category.store', '') }}/${$(this).data('id')}`);
                modal.find('.modal-title').text(`@lang('Update Category')`);
                modal.find('[name=name]').val($(this).data('name'));
                modal.find('.image-upload-preview').css('background-image', `url(${imgPath})`);
                modal.find('.image-upload-input').prop("required", false);
                modal.find('.image1').removeClass("required");
                modal.modal('show')
            });
            var defautlImage = `{{ getImage(getFilePath('category'), getFileSize('category')) }}`;

            modal.on('hidden.bs.modal', function() {
                modal.find('.profilePicPreview').attr('style', `background-image: url(${defautlImage})`);
                $('#categoryModal form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
