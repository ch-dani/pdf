<a href="" id="scroll-top" class="scroll-top">
    @php include(public_path('freeconvert/img/up-arrow.svg')) @endphp
</a>

<footer>
	<script data-ad-client="ca-pub-4201607577258658" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>    

    <div class="footer_bottom_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-4 footer_logo_block">
                    <div class="footer_top_item">
                        <div class="footer_top_title">
                            {{--@php include(public_path('freeconvert/img/FreeLogoConvertLogo2.svg')) @endphp--}}
                            @php include(public_path('img/FreeLogoConvertLogo2.svg')) @endphp
                        </div>
                        <div class="footer_top_txt">
                        	{{t('Free Convert PDF is a Swiss Army Knife for any user who wants to easily manipulate PDF files online: protect, compress, convert to any other file format and etc.')}}
                        </div>
                    </div>
                </div>
                
           	 	@foreach ($MenuCategories as $category_id => $category)
                
		            <div class="col-sm-6 col-md-3 col-xl-2">
		                <div class="footer_top_item list">
		                    <div class="footer_top_title">
		                        <h4>
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
		                        </h4>
		                    </div>
		                    <ul>
                                @if (isset($Menu[$category_id]))
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
                                @endif
		                    </ul>
		                </div>
		            </div>
                @endforeach
                <div class="col-sm-6 col-md-3 col-xl-2">
                    <div class="footer_top_item list">
                        <div class="footer_top_title">
                            <h4>Other</h4>
                        </div>
                        <ul>
                            <li><a href="{{route('index')}}">{{t('Home')}}</a></li>
                            <li><a href="/pricing">{{t('Pricing')}}</a></li>
                            <li><a href="{{route('blog')}}">{{t('Blog')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="svg_dots svg_dots_1">
        @php include(public_path('freeconvert/img/footer-dots.svg')) @endphp
    </div>
    <div class="svg_dots svg_dots_2">
        @php include(public_path('freeconvert/img/footer-dots.svg')) @endphp
    </div>
    <div class="svg_dots svg_dots_3">
        @php include(public_path('freeconvert/img/footer-dots.svg')) @endphp
    </div>
    <div class="svg_dots svg_dots_4">
        @php include(public_path('freeconvert/img/footer-dots.svg')) @endphp
    </div>

    <div class="footer_bottom_wrapper footer_bottom_nav_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <div class="footer_bottom_item">
                        <span>{{t('© Free Convert PDF')}}, <?= date("Y") ?></span>
                    </div>
                </div>
                
                <div class="col-6 footer_bottom_line_menu">
		            @if (isset($MenuBottom))
		                @foreach ($MenuBottom as $menu)
		                    @php
		                        $nofollow = "";
		                        $pos = strpos($menu->url, "mailto");
		                        if($pos!==false){
		                            $nofollow = 'rel="nofollow"';
		                            $menu->url = ltrim($menu->url, "{$lang_code}/");
		                        }
		                    @endphp
		                    <div class="footer_bottom_item">
				                <a class="footer_bottom_item" {!! $nofollow !!} href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}"
				                   target="{{ $menu->target }}">
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
		                    </div>
		                @endforeach
		            @endif
                </div>
                <div class="col-3">
                    @php
                        $social_facebook = \App\Option::option('social_facebook');
                        $social_twitter = \App\Option::option('social_twitter');
                        $social_google = \App\Option::option('social_google');
                    @endphp
                    @if($social_facebook || $social_twitter || $social_google)
                        <div class="footer_bottom_item socials">
                            @if($social_facebook)
                                <div class="footer_bottom_subitem">
                                    <a target="_blank" href="{{ $social_facebook }}"><i class="fab fa-facebook-square"></i></a>
                                </div>
                            @endif
                            @if($social_twitter)
                                <div class="footer_bottom_subitem">
                                    <a target="_blank" href="{{ $social_twitter }}"><i class="fab fa-twitter-square"></i></a>
                                </div>
                            @endif
                            @if($social_google)
                                <div class="footer_bottom_subitem">
                                    <a target="_blank" href="{{ $social_google }}"><i class="fab fa-google-plus-square"></i></a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://www.paypal.com/sdk/js?client-id={{ \App\Option::option('paypal_client_id') }}"></script>
<script src="https://apis.google.com/js/platform.js"></script>
<script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"
        data-app-key="{{ env('DROPBOX_API_KEY') }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="{{ asset('freeconvert/js/jquery.mask.min.js') }}"></script>
<script src="/js/sweetalert2.all.min.js"></script>
<script src="{{ asset('freeconvert/js/main.js') }}"></script>
<script src="{{ asset('freeconvert/js/main_roman.js') }}"></script>
<script src="{{ asset('freeconvert/js/main_oleg.js') }}"></script>
<script src="{{ asset('freeconvert/js/main_grisyuk.js') }}"></script>
<script>
    var skip_simple_viewer = false;
</script>
<script src="{{ asset('assets/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('js/JavaScript.js') }}"></script>
<script src="{{ asset('js/auth.js') }}"></script>
<script src="{{ asset('js/subscription-purchase.js') }}"></script>
<script src="{{ asset('freeconvert/js/jquery.nice-select.min.js') }}"></script>

@if($current_url == "pdf-editor")

@elseif($current_url == "translate")

@else
    <script src="{{ asset('/js/pdftool.js')}}"></script>
    <script src="{{ asset('/libs/pdfjs-dist/build/pdf.js')}}"></script>
@endif


{{--<script src="{{ asset('/js/save-to.js')}}"></script>--}}

<script src="{{ asset('/libs/pdfjs-dist/web/pdf_viewer.js')}}"></script>


@if ($current_url=='fill-sign-pdf')
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC|Charmonman|Courgette|Dancing+Script|Dokdo|Gamja+Flower|Gloria+Hallelujah|Indie+Flower|Pacifico|Patrick+Hand|Permanent+Marker|Shadows+Into+Light" rel="stylesheet">
    <script src="{{ asset('/js/html2canvas.js')}}"></script>
@endif

@if ($current_url=='pdf-to-excel')

	<link rel="stylesheet" type="text/css" href="/libs/pdfjs-dist/web/pdf_viewer.css">
	<script src="/js/math.min.js"></script>
    <script src="{{ asset('/js/excelpdf.js')}}"></script>
@endif

@if ($current_url=='crop-pdf')
    <link rel="stylesheet" type="text/css" href="{{ asset('/libs/jcrop/css/jquery.Jcrop.min.css') }}">
    <script src="{{ asset('/libs/jcrop/js/jquery.Jcrop.min.js')}}"></script>
    <script src="{{ asset('/js/croppdf.js')}}"></script>
@endif

@if ($current_url=="resize-pdf")
    <script src="{{asset('/js/pdfresize.js')}}"></script>
@endif


@if ($current_url=="pdf-to-epub")
    <script src="{{ asset('/js/pdf2epub.js')}}"></script>
@endif

@yield('js')

<script src="{{ asset('/js/simpleviewer.js')}}"></script>
<script src="{{ asset('/js/new_viewer.js')}}"></script>
<script type="text/javascript" src="/js/dropbox-chooser.js"></script>
<script type="text/javascript" src="/js/dropbox-saver.js"></script>
<script type="text/javascript" src="/js/google-drive-chooser.js"></script>
<script type="text/javascript" src="/js/google-drive-saver.js"></script>
<script>
    async function createFile2(url, filename, accessToken) {
        let response = await fetch(url, {
            headers: new Headers({
                'Authorization': 'Bearer ' + accessToken,
            }),
        });
        let data = await response.blob();
        let metadata = {type: 'application/pdf'};
        return new File([data], filename, metadata);
    }

    var scripts = [
            <?php /*
		{
			script: "//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", callback: function () {
				console.log("google ads");
				(adsbygoogle = window.adsbygoogle || []).push({
					google_ad_client: "ca-pub-3312770701892086",
					enable_page_level_ads: true
				});
			}
		}
        */ ?>
            @if($country!='China')
            <?php /*,*/ ?>
        {
            script: "https://apis.google.com/js/client.js?onload=handleClientLoad", callback: function () {
                console.log('client.js')
            }, data: false
        },
        {
            script: "https://apis.google.com/js/api.js", callback: function () {
                console.log("api.js");
            }, data: false
        },
        {
            script: "https://www.dropbox.com/static/api/2/dropins.js", callback: function () {
                console.log("dropin.js");
                // }, data: {"data-app-key": "kmnvanr1sm5jlg1"}, id: "dropboxjs"
            }, data: {"data-app-key": "777"}, id: "dropboxjs"
        },
        //{script: "https://apis.google.com/js/client.js?onload=loadPickerCallback", callback: function(){  console.log("client.js"); }, data: false},
        {
            script: "https://apis.google.com/js/platform.js", callback: function () {
                console.log("platform.js");
            }, data: false
        },
        @endif
    ];

    var loadJS = function (url, implementationCode, location, data = false) {
        var scriptTag = document.createElement('script');
        if (data) {
            $(scriptTag).attr(data);
        }
        scriptTag.src = url;
        if (typeof implementationCode == 'function') {
            scriptTag.onload = implementationCode;
            scriptTag.onreadystatechange = implementationCode;
        }
        location.appendChild(scriptTag);
    };
    //
    $.each(scripts, function (i, script) {
        loadJS(script.script, script.callback, document.body,);

    });
</script>

@if (isset($js))
    @foreach ($js as $src)
        <script type="text/javascript" src="{{ $src }}"></script>
        @endforeach
        @endif
<script src="{{ asset('freeconvert/js/main_alex.js') }}"></script>
<script src="{{ asset('freeconvert/js/equal-heights.js') }}"></script>
        </body>

        </html>
