<header>
    <div class="container">
        <div class="row">
            <div class="col-xl-2 col-lg-2 col-md-10 col-sm-10 col-10">
                @php
                    global $lang_code;
                    $home_link = "/";
                    if($lang_code!='en'){
                        $home_link = "/{$lang_code}";
                    }
                @endphp
                <div class="logo">
                    <a href="{{$home_link}}">
                    	@php include(public_path('freeconvert/img/logo.svg')) @endphp
                    </a>
                </div>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-2 col-sm-2 col-2">
                <div class="burger_menu">
                    @php include(public_path('freeconvert/img/open-menu.svg')) @endphp
                </div>
                <nav>
                    <ul class="module__main-menu">
                        <li class="not_empty">
                            <a href="#">{{t('All tools')}}</a>
                            <div class="sub_menu full_width">
                                <div class="container">
                                    <div class="row">
                                        @foreach ($MenuCategories as $category_id => $category)
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                                <div class="wrapper_menu_box">
                                                    <div class="title">
                                                        @php
                                                            $title = json_decode($category->title, true);
                                                            if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                                                $title = $title[$ActiveLanguage->id];
                                                            elseif (isset($title[1]) and !empty($title[1]))
                                                                $title = $title[1];
                                                            else
                                                                $title = 'Other';
                                                        @endphp
                                                        {{ $title }}
                                                    </div>
                                                    @if (isset($Menu[$category_id]))
                                                        <ul class="menu">
                                                            @foreach ($Menu[$category_id] as $menu)
                                                                <li>
                                                                    <a href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}"
                                                                       target="{{ $menu->target }}">
                                                                        @php
                                                                            $title = json_decode($menu->title, true);
                                                                            if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                                                                $title = $title[$ActiveLanguage->id];
                                                                            elseif (isset($title[1]) and !empty($title[1]))
                                                                                $title = $title[1];
                                                                            else
                                                                                $title = '';

                                                                            $tooltip = json_decode($menu->tooltip, true);
                                                                            if (isset($tooltip[$ActiveLanguage->id]) and !empty($tooltip[$ActiveLanguage->id]))
                                                                                $tooltip = $tooltip[$ActiveLanguage->id];
                                                                            elseif (isset($tooltip[1]) and !empty($tooltip[1]))
                                                                                $tooltip = $tooltip[1];
                                                                            else
                                                                                $tooltip = '';
                                                                        @endphp
                                                                        {{ $title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </li>

                        @if (isset($Menu[0]))
                            @foreach ($Menu[0] as $menu)
                                <li class="@if($menu->id==2) not_empty @endif">
                                    <a @if($menu->id!=2) href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}"
                                       target="{{ $menu->target }}" @endif>
                                        @php
                                            $title = json_decode($menu->title, true);
                                            if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                                $title = $title[$ActiveLanguage->id];
                                            elseif (isset($title[1]) and !empty($title[1]))
                                                $title = $title[1];
                                            else
                                                $title = '';
                                        @endphp
                                        {{ $title }}
                                    </a>
                                    @if($menu->id==2)
                                        <div class="sub_menu full_width">
                                            <div class="container">
                                                <div class="row">

                                @foreach($menuConv as $mit=>$mc)

                                    @php
                                        $title = json_decode($mc->title, true);
                                        if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                            $title = $title[$ActiveLanguage->id];
                                        elseif (isset($title[1]) and !empty($title[1]))
                                            $title = $title[1];
                                        else
                                            $title = '';
                                    @endphp
                                    @if(strpos($mc->url, '#separate')!==false)
                                        @if($mit!==0)
                    </ul>
            </div>
        </div>
        @endif
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
            <div class="wrapper_menu_box">
                <ul class="menu">
                    <div class="title">{{ $title }}</div>
                    @else
                        <li>
                            <a href="{{ strpos($mc->url, 'http') === 0 ? $mc->url : url($mc->url) }}"
                               target="{{ $mc->target }}">
                                {{ $title }}
                            </a>
                        </li>
                    @endif
                    @if($loop->last)
                </ul>
            </div>
        </div>
        @endif

        @endforeach
    </div>
    </div>
    </div>
    @endif
    </li>
    @endforeach
    @endif

    <li class="login" style="top: 28%; margin-left: 10px;">
        @if (\Auth::check())
            <a href="{{ route('account') }}">{{ t("Account") }}</a>
        @else
            <a href="#" class="menu-login">{{ t('Login') }}</a>
        @endif
    </li>
    <li class="sign_up">
        <a href="{{ route('contact') }}">{{ t('Contact Support') }}</a>
    </li>

    @include('inc-freeconvert.login-popup')
    @include('inc-freeconvert.sign-up-popup')
    @include('inc-freeconvert.subscription-purchase')
    @include('inc-freeconvert.forgot-password')
    @include('page_parts.languages')

    </ul>


    </nav>
    </div>
    </div>
    </div>
</header>
