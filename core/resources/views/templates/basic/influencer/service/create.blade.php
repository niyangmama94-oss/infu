@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body">
            <form action="{{ route('influencer.service.store', @$service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-4">
                        <label class="form-label" for="title">@lang('Image')<span class="text--danger">*</span></label>
                        <div class="profile-thumb p-0 text-center shadow-none">
                            <div class="thumb">
                                <img id="upload-img" src="{{ getImage(getFilePath('service') . '/' . @$service->image, getFileSize('service')) }}" alt="userProfile">
                                <label class="badge badge--icon badge--fill-base update-thumb-icon" for="update-photo"><i class="las la-pen"></i></label>
                            </div>
                            <div class="profile__info">
                                <input class="form-control d-none" id="update-photo" name="image" type="file" accept=".png, .jpg, .jpeg" @if (!@$service) required @endif>
                            </div>
                        </div>
                        <small class="text--warning">@lang('Supported files'): @lang('jpeg'), @lang('jpg'), @lang('png'). @lang('| Will be resized to'): {{ getFileSize('service') }}@lang('px').</small>
                    </div>
                    @php
                        if (@$service) {
                            $categoryId = $service->category_id;
                        } else {
                            $categoryId = old('category_id');
                        }
                    @endphp
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label class="form-label" for="category_id">@lang('Category')</label>
                            <select class="form-select form--control select2" id="category_id" name="category_id" required>
                                <option value="" selected disabled>@lang('Select category')</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if ($categoryId == $category->id) selected="selected" @endif>
                                        {{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Title')</label>
                            <input class="form-control form--control" name="title" type="text" value="@if (@$service) {{ @$service->title }}@else{{ old('title') }} @endif" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Price')</label>
                            <div class="input-group">
                                <input class="form-control form--control" name="price" type="number" value="@if (@$service){{getAmount(@$service->price)}}@else{{old('price')}} @endif" step="any" required>
                                <span class="input-group-text">{{ __(gs("cur_text")) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $selectedTag = [];

                    if(@$service->tags){
                        foreach(@$service->tags as $t){
                            array_push($selectedTag,$t->name);
                        }
                    }
                @endphp
                <div class="form-group skill-body">
                    <label class="form-label">@lang('Service Tags')</label>
                    <select class="form-control form--control select2-auto-tokenize" multiple="multiple" name="tags[]" required>
                        @foreach (@$tags as $tag)
                            <option value="{{ @$tag->name }}" {{  in_array($tag->name,$selectedTag) ? 'selected' : '' }}>{{ __(@$tag->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label required">@lang('Description')</label>
                    <textarea class="form-control form--control nicEdit" name="description" rows="4" placeholder="@lang('Write here')">

{{ old('description',@$service->description) }}

</textarea>
                </div>

                <div class="content w-100 ps-0">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <div class="form-label mb-0">
                            <p>@lang('Key Points')<span class="text--danger">*</span></p>
                        </div>
                        <button class="btn btn--outline-custom btn--sm pointBtn" type="button">
                            <i class="las la-plus"></i>@lang('Add More')
                        </button>
                    </div>
                </div>

                @if (@$service->key_points)
                    @foreach (@$service->key_points as $point)
                        <div class="key-point d-flex mb-3 gap-2">
                            <input class="form-control form--control" name="key_points[]" type="text" value="{{ __($point) }}" required>
                            <button class="btn btn--danger remove-button @if ($loop->first) disabled @endif border-0" type="button"><i class="fas fa-times"></i></button>
                        </div>
                    @endforeach
                @else
                    <div class="d-flex mb-3 gap-2">
                        <input class="form-control form--control" name="key_points[]" type="text" required>
                        <button class="btn btn--danger disabled border-0" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
                <div id="more-keyPoint">

                </div>

                <div class="form-group">
                    <label class="form-label">@lang('Images')</label>
                    <div class="input-images"></div>
                </div>


                <button class="btn btn--base w-100 mt-3" type="submit">@lang('Submit')</button>
        </div>
        </form>
    </div>
    </div>
@endsection
@push('style')
    <style>
        .badge.badge--icon {
            border-radius: 5px 0 0 0;
        }
    </style>
@endpush
@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/lib/image-uploader.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/lib/image-uploader.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const inputField = document.querySelector('#update-photo'),
                uploadImg = document.querySelector('#upload-img');
            inputField.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        const result = reader.result;
                        uploadImg.src = result;
                    }
                    reader.readAsDataURL(file);
                }
            });


            @if (isset($images))
                let preloaded = @json($images);
            @else
                let preloaded = [];
            @endif

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'images',
                preloadedInputName: 'old',
                maxSize: 2 * 1024 * 1024,
            });

            $('.pointBtn').on('click', function() {
                var html = `
                <div class="key-point d-flex gap-2 mb-3">
                    <input type="text" class="form-control form--control" name="key_points[]" required>
                    <button class="btn btn--danger remove-button border-0" type="button"><i class="fas fa-times"></i></button>
                </div>`;
                $('#more-keyPoint').append(html);
            });

            $(document).on('click', '.remove-button', function() {
                $(this).closest('.key-point').remove();
            });

        })(jQuery);
    </script>
@endpush
