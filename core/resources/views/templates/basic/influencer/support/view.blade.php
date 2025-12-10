@extends($activeTemplate . 'layouts.' . $layout)
@section('content')

    @if ($layout == 'frontend')
        <div class="pt-80 pb-80">
            <div class="container">
    @endif

    <div class="card custom--card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="card-title m-0">
                @php echo $myTicket->statusBadge; @endphp [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
            </h5>
            @if ($myTicket->status != 3 && $myTicket->influencer)
                <button class="btn btn--danger btn--sm confirmationBtn" type="button" data-question="@lang('Are you sure to close this ticket?')"
                    data-action="{{ route('influencer.ticket.close', $myTicket->id) }}"
                    data-btn_class="btn btn--base btn--md"><i class="la la-lg la-times-circle"></i>
                </button>
            @endif
        </div>

        <div class="card-body">
            @if ($myTicket->status != 4)
                <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="message" class="form-control form--control shadow-none" id="inputMessage"
                                    placeholder="@lang('Your Reply')" rows="4" cols="10">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn--base btn--sm btn-sm addAttachment my-2"> <i
                                    class="fas fa-plus"></i> @lang('Add Attachment') </button>
                            <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                            <div class="row fileUploadsContainer">
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn--base w-100 h-40">
                                <i class="fa fa-reply"></i> @lang('Reply')
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
    <div class="card custom--card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @foreach ($messages as $message)
                        @if ($message->admin_id == 0)
                            <div
                                class="row border-primary border-radius-3 ticket-reply-user my-sm-3 mx-sm-2 my-2 mx-0 border py-3">
                                <div class="col-md-3 border--right text-right">
                                    <h5 class="text--base my-3">{{ @$message->ticket->name }}</h5>
                                </div>
                                <div class="col-md-9 ps-2">
                                    <p class="text-muted fw-bold">
                                        @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}
                                    </p>
                                    <p>
                                        {{ $message->message }}
                                    </p>
                                    @if ($message->attachments->count() > 0)
                                        <div class="mt-2">
                                            @foreach ($message->attachments as $k => $image)
                                                <a href="{{ route('influencer.ticket.download', encrypt($image->id)) }}"
                                                    class="text--base mr-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                    {{ ++$k }} </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="row border-warning border-radius-3 my-sm-3 mx-sm-2 my-2 mx-0 border py-3">
                                <div class="col-md-3 border--right text-right">
                                    <h5 class="text--base my-3">{{ @$message->admin->name }}</h5>
                                </div>
                                <div class="col-md-9 ps-2">
                                    <p class="text-muted fw-bold">@lang('Posted on')
                                        {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                    <p>{{ $message->message }}</p>
                                    @if ($message->attachments->count() > 0)
                                        <div class="mt-2">
                                            @foreach ($message->attachments as $k => $image)
                                                <a href="{{ route('influencer.ticket.download', encrypt($image->id)) }}"
                                                    class="text--base mr-3"><i class="fa fa-file"></i> @lang('Attachment')
                                                    {{ ++$k }} </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if ($layout == 'frontend')
        </div>
        </div>
    @endif

    <x-confirmation-modal frontendClass="true" />

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
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
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
