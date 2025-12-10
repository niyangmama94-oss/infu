@php
    $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', 0)->get();
    $condition = request()->routeIs('user.*') || request()->routeIs('influencer.*') || request()->routeIs('ticket*');
    $selectedLang = $language->where('code', session('lang'))->first();
@endphp


<div class="header @if ($condition) dash-header @endif">
    <div class="header-bottom">
        <div class="container">
            <div class="header-bottom-area align-items-center">
                <div class="logo"><a href="{{ route('home') }}"><img
                            src="@if (!$condition) {{ siteLogo() }} @else {{ siteLogo('dark') }} @endif "
                            alt="logo"></a></div>
                <ul class="menu">
                    <li class="d-lg-none p-0 border-0 header-close ">
                        <span class="fs--20px text-white"><i class="las la-times"></i></span>
                    </li>

                    <li>
                        <a href="{{ route('home') }}" class="{{ menuActive('home') }}">@lang('Home')</a>
                    </li>

                    @foreach ($pages as $k => $data)
                        <li><a href="{{ route('pages', [$data->slug]) }}"
                                class="{{ menuActive('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                    @endforeach

                    <li>
                        <a href="{{ route('services') }}" class="{{ menuActive('services') }}">@lang('Services')</a>
                    </li>

                    <li>
                        <a href="{{ route('influencers') }}"
                            class="{{ menuActive('influencers') }}">@lang('Influencers')</a>
                    </li>

                    <li>
                        <a href="{{ route('contact') }}" class="{{ menuActive('contact') }}">@lang('Contact')</a>
                    </li>

                    <li class="d-lg-none">
                        @if (!(auth()->id() || authInfluencerId()))
                            <a href="{{ route('user.login') }}"
                                class="btn btn-md btn--base storageClear">@lang('Login')</a>
                        @endif
                        @auth
                            <a href="{{ route('user.home') }}" class="btn btn-md btn--base">@lang('Dashboard')</a>
                        @endauth

                        @auth('influencer')
                            <a href="{{ route('influencer.home') }}" class="btn btn-md btn--base">@lang('Dashboard')</a>
                        @endauth
                    </li>
                </ul>
                <div class="header-trigger-wrapper d-flex align-items-center">
                    <div class="button-wrapper d-flex align-items-center flex-wrap gap-2 gap-lg-3">
                        @if (!(auth()->id() || authInfluencerId()))
                            <ul class="d-flex align-items-center flex-wrap gap-2 gap-lg-3">
                                <li class="me-0">
                                    <a href="{{ route('user.login') }}"
                                        class="login-btn btn btn--md btn--outline-base d-none d-sm-grid text-white storageClear">@lang('Login')</a>
                                </li>
                                <li class="me-0">
                                    <a href="{{ route('user.register') }}"
                                        class="login-btn btn btn--md btn--outline-base d-none d-sm-grid text-white storageClear">@lang('Register')</a>
                                </li>
                            </ul>
                        @endif
                        @auth
                            <ul class="d-flex align-items-center flex-wrap">
                                <li class="me-0">
                                    <a href="{{ route('user.home') }}"
                                        class="login-btn btn btn--md btn--outline-base d-none d-sm-grid text-white">@lang('Dashboard')</a>
                                </li>
                            </ul>
                        @endauth
                        @auth('influencer')
                            <ul class="d-flex align-items-center flex-wrap">
                                <li class="me-0">
                                    <a href="{{ route('influencer.home') }}"
                                        class="login-btn btn--md btn btn--outline-base d-none d-sm-grid text-white">@lang('Dashboard')</a>
                                </li>
                            </ul>
                        @endauth
                        @if (gs('multi_language'))
                            <div class="language dropdown">
                                <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="language-content">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . @$selectedLang->image, getFileSize('language')) }}"
                                                alt="flag">
                                        </div>
                                        <p class="language_text_select">{{ __(@$selectedLang->name) }}</p>
                                    </div>
                                    <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                                </button>
                                <div class="dropdown-menu langList_dropdow py-2" style="">
                                    <ul class="langList">
                                        @foreach ($language as $item)
                                            <li class="language-list langSel" data-code="{{ $item->code }}">
                                                <div class="language_flag">
                                                    <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}"
                                                        alt="flag">
                                                </div>
                                                <p class="language_text">{{ $item->name }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="header-trigger d-lg-none">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>
        $(document).ready(function() {
            const $mainlangList = $(".langList");
            const $langBtn = $(".language-content");
            const $langListItem = $mainlangList.children();

            $langListItem.each(function() {
                const $innerItem = $(this);
                const $languageText = $innerItem.find(".language_text");
                const $languageFlag = $innerItem.find(".language_flag");

                $innerItem.on("click", function(e) {
                    $langBtn.find(".language_text_select").text($languageText.text());
                    $langBtn.find(".language_flag").html($languageFlag.html());
                });
            });
        });
    </script>
@endpush


@push('style')
    <style>
        .language-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 5px 12px;
            border-radius: 4px;
            width: 130px;
            background-color: transparent;
            border: 1px solid rgb(var(--base));
            height: 38px;
            color: hsl(var(--white));
        }

        .header.sticky .language.dropdown .language-wrapper .language_text_select,
        .header.dash-header .language.dropdown .language-wrapper .language_text_select {
            color: red;
            color: rgb(var(--body));
        }

        .language_flag {
            flex-shrink: 0;
            display: flex;
        }

        .language_flag img {
            height: 20px;
            width: 20px;
            object-fit: cover;
            border-radius: 50%;
        }

        .language-wrapper.show .collapse-icon {
            transform: rotate(180deg)
        }

        .collapse-icon {
            font-size: 14px;
            display: flex;
            transition: all linear 0.2s;
            color: rgb(var(--base))
        }

        .language_text_select {
            font-size: 14px;
            font-weight: 400;

        }

        .language-content {
            display: flex;
            align-items: center;
            gap: 6px;
        }


        .language_text {
            color: hsl(var(--body-color));
        }

        .language-list {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .language .dropdown-menu {
            position: absolute;
            -webkit-transition: ease-in-out 0.1s;
            transition: ease-in-out 0.1s;
            opacity: 0;
            visibility: hidden;
            top: 100%;
            display: unset;
            background: #ffffff;
            -webkit-transform: scaleY(1);
            transform: scaleY(1);
            min-width: 150px;
            padding: 7px 0 !important;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0 0.10);
        }

        .language .dropdown-menu.show {
            visibility: visible;
            opacity: 1;
        }
    </style>
@endpush
