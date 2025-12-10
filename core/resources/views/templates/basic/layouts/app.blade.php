<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName(__(isset($customPageTitle) ? $customPageTitle : $pageTitle)) }}</title>
    @include('partials.seo')
    <link rel="icon" type="image/png" href="{{ siteFavicon() }}" sizes="16x16">

    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lib/animate.css') }}">

    <!-- Plugin Link -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lib/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lib/magnific-popup.css') }}">

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}">

    @stack('style-lib')

    @stack('style')
</head>

@php echo loadExtension('google-analytics') @endphp

<body>
    @stack('fbComment')
    <!-- Overlay -->
    <div class="overlay"></div>

    <div class="preloader">
        <div class="main-loader">
            <div class="loader-inner">
                <div class="loader">
                    <div class="box"></div>
                </div>
            </div>
        </div>
    </div>

    <a href="#0" class="scrollToTop"><i class="las la-chevron-up"></i></a>

    @yield('app')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie->data_values->status == 1 && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }}
                <a href="{{ route('cookie.policy') }}" target="_blank" class="text--base">@lang('learn more')</a>
            </p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base w-100 policy">@lang('Allow')</a>
            </div>
        </div>
    @endif
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        window.my_pusher = {
            'key': "{{ base64_encode(@gs('pusher_configuration')->key) }}",
            'cluster': "{{ base64_encode(@gs('pusher_configuration')->cluster) }}",
            'base_url': "{{ route('home') }}"
        }
        window.allow_decimal = "{{ gs('allow_decimal_after_number') }}";
    </script>

    @stack('script-lib')

    @stack('script')

    @include('partials.notify')

    @include('partials.plugins')

    <!-- Pluglin Link -->
    <script src="{{ asset($activeTemplateTrue . 'js/lib/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/lib/magnific-popup.min.js') }}"></script>

    <!-- Main js -->
    <script src="{{ asset($activeTemplateTrue . 'js/lib/chart.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>

    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    <script>
        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });
        (function($) {
            "use strict";

            $('.select2').select2();

            $.each($('.select2-auto-tokenize'), function() {
                $(this)
                    .wrap(`<div class="position-relative"></div>`)
                    .select2({
                        tags: true,
                        tokenSeparators: [','],
                        dropdownParent: $(this).parent()
                    });
            });

            $(".langSel").on("click", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).data('code');
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {

                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }

            });

            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Sticky Menu
            let logo = `{{ siteLogo() }}`;
            let logoDark = `{{ siteLogo('dark') }}`;
            window.addEventListener("scroll", function() {
                var header = document.querySelector(".header");
                header.classList.toggle("sticky", window.scrollY > 0);

                if ($('.header').hasClass('dash-header')) {
                    $('.header .logo img').attr('src', logoDark);
                } else {

                    if ($('.header').hasClass('sticky')) {
                        $('.header .logo img').attr('src', logoDark)
                    } else {
                        $('.header .logo img').attr('src', logo);
                    }
                }
            });

        })(jQuery);
    </script>

</body>

</html>
