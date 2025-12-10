@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <h4 class="card-title m-0">{{ $pageTitle }}</h4>
            <a href="{{ route('ticket') }}" class="btn btn--outline-custom btn--sm">@lang('My Tickets')</a>
        </div>
        <div class="card-body">
            <form class="row gy-3" action="{{ route('ticket.store') }}" method="post" enctype="multipart/form-data" onsubmit="return submitUserForm();">
                @csrf
                <input type="hidden" name="name" value="{{ @$user->fullname }}">
                <input type="hidden" name="email" value="{{ @$user->email }}">

                <div class="form--group col-sm-12">
                    <label for="subject" class="form-label">@lang('Subject')</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                           class="form-control form--control" required>
                </div>
                <div class="form--group col-md-12">
                    <label class="form-label">@lang('Priority')</label>
                    <select name="priority" class="form-control form--control select2" required>
                        <option value="3">@lang('High')</option>
                        <option value="2">@lang('Medium')</option>
                        <option value="1">@lang('Low')</option>
                    </select>
                </div>
                <div class="form--group col-sm-12">
                    <label for="message" class="form-label">@lang('Message')</label>
                    <textarea id="message" name="message" class="form-control form--control" required>{{ old('message') }}</textarea>
                </div>
                <div class="form-group col-md-12">
                    <button type="button" class="btn btn--base btn--sm addAttachment my-2"> <i class="fas fa-plus"></i>
                        @lang('Add Attachment') </button>
                    <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                    <div class="row fileUploadsContainer">
                    </div>
                </div>
                <div class="col-sm-12">
                    <button class="btn btn--base w-100" type="submit" id="recaptcha">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection



@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var fileAdded = 0;
            $('.addAttachment').on('click',function(){
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled',true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click','.removeFile',function(){
                $('.addAttachment').removeAttr('disabled',true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
