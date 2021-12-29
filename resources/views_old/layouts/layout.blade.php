<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="keywords" content="{{ isset($SeoGlobal['keywords']) ? $SeoGlobal['keywords'] : '' }}">
    <meta name="description" content="{{ isset($SeoGlobal['description']) ? $SeoGlobal['description'] : '' }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ isset($SeoGlobal['title']) ? $SeoGlobal['title'] : '' }}</title>
	<link rel="stylesheet" type="text/css" href="/libs/fancybox/jquery.fancybox.min.css">
    <link rel="stylesheet" href="{{ asset('assets/select2/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('libs/jquery-ui/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fancybox/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
          integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC|Charmonman|Courgette|Dancing+Script|Dokdo|Gamja+Flower|Gloria+Hallelujah|Indie+Flower|Pacifico|Patrick+Hand|Permanent+Marker|Shadows+Into+Light" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link href="{{ asset('css/StyleSheet.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/media.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/additional-popup-style.css') }}">
    <link href="{{ asset('css/additional-front.css') }}" rel="stylesheet"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('libs/pdfjs-dist/web/pdf_viewer.css') }}">
    <link rel="stylesheet" href="/css/sweetalert2.min.css">
    <!-- STYLESHEETS END -->
    
    
</head>
<body dd='{{url('/') }}'>

	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.3"></script>

	<div class="current_uploads"></div>
    <header>
        <div class="header-top">
            <div class="container">
                <div class="header-wrap">
                    <a href="/" class="header-logo">
                        <?php include('img/logo.svg'); ?>
                    </a>
                    <div class="menu_mob">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="header-menu-block">
                        <ul class="header-menu">
                            <li class="open-menu-btn"><a>All tools</a></li>
                            @if (isset($Menu[0]))
                                @foreach ($Menu[0] as $menu)
                                    <li class="@if($menu->id==2) show_drop_down open-menu-btn2 @endif">
                                    	<a @if($menu->id!=2) href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}" target="{{ $menu->target }}" @endif>
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
                                   		<ul class="drop_down_menu">
                                    	@foreach($menuConv as $mc)
                                    		<li>
                                    			<a href="{{ strpos($mc->url, 'http') === 0 ? $mc->url : url($mc->url) }}" target="{{ $mc->target }}">
						                            @php
						                                $title = json_decode($mc->title, true);
						                                if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
						                                    $title = $title[$ActiveLanguage->id];
						                                elseif (isset($title[1]) and !empty($title[1]))
						                                    $title = $title[1];
						                                else
						                                    $title = '';
						                            @endphp
						                            {{ $title }}
                                    			</a>
                                    		</li>
                                    	@endforeach
                                   		</ul>
                                    @endif
                                    
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                        <ul class="header-lists">
                            @if (\Auth::check())
                                <li>
                                    <a href="{{ route('account') }}">Account</a>&nbsp;&nbsp;&nbsp;
                                    <a href="{{ route('logout') }}">Logout</a>
                                </li>
                            @else
                                <li class="login-btn">
                                    <a href="#">Login <img src="{{ asset('img/user-iccon.svg') }}" alt="Alternate Text"/></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="megaMenu" style="" class="container-fluid mega-menu">
            <div class="mega-menu-inner">
                <button aria-label="Close" class="close" type="button"><span aria-hidden="true">×</span></button>
                <div class="container">
                    <div class="row">

						
						<div class="col col-sm-2 col-xs-6">
							<ul class="popular">
								<li class="header">
									<strong>
										POPULAR
									</strong>
								</li>
								@foreach($popular_menu as $menu)
									<li>
										<a href="{{ strpos($menu['url'], 'http') === 0 ? $menu['url'] : url($menu['url']) }}" target="{{ $menu['target'] }}">
                                            @php
                                                $title = json_decode($menu['title'], true);
                                                if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                                    $title = $title[$ActiveLanguage->id];
                                                elseif (isset($title[1]) and !empty($title[1]))
                                                    $title = $title[1];
                                                else
                                                    $title = '';

                                                $tooltip = json_decode($menu['tooltip'], true);
                                                if (isset($tooltip[$ActiveLanguage->id]) and !empty($tooltip[$ActiveLanguage->id]))
                                                    $tooltip = $tooltip[$ActiveLanguage->id];
                                                elseif (isset($tooltip[1]) and !empty($tooltip[1]))
                                                    $tooltip = $tooltip[1];
                                                else
                                                    $tooltip = '';
                                            @endphp
                                            {{ $title }}
                                            @if (strlen($menu['tooltip']))
                                                <div class="tooltip-block">
                                                    {{ $tooltip }}
                                                </div>
                                            @endif
										</a>
									</li>
								@endforeach
							</ul>
						</div>
						


                        @foreach ($MenuCategories as $category_id => $category)
                            <div class="col col-sm-2 col-xs-6">
                                <ul class="popular">
                                    <li class="header">
                                        <strong>
                                            @php
                                                $title = json_decode($category->title, true);
                                                if (isset($title[$ActiveLanguage->id]) and !empty($title[$ActiveLanguage->id]))
                                                    $title = $title[$ActiveLanguage->id];
                                                elseif (isset($title[1]) and !empty($title[1]))
                                                    $title = $title[1];
                                                else
                                                    $title = '';
                                            @endphp
                                            {{ $title }}
                                        </strong>
                                    </li>
                                    @if (isset($Menu[$category_id]))
                                        @foreach ($Menu[$category_id] as $menu)
                                            <li>
                                                <a href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}" target="{{ $menu->target }}">
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
                                                    @if (strlen($menu->tooltip))
                                                        <div class="tooltip-block">
                                                            {{ $tooltip }}
                                                        </div>
                                                    @endif
                                                </a>
                                                @if (($menu->new))
                                                    <span class="label label-success">New</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endforeach

                        <div class="menu-serch">
                            <input id="search_tool_input" placeholder="Quickly find a tool" type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </header>

    <div class="overley-bg">
    </div>
	<div class="pdf_preloader">
	    <div class="lds-dual-ring"></div>
	</div>
    @yield('content')

    <section class="login-modal">
        <div class="login-modal-wrap">
            <div id="closeModal">&times;</div>
            <div class="lolin-modal-block">
                <img src="{{ asset('img/logo.svg') }}" alt="Alternate Text"/>
                <h3>Sign in to your account</h3>
                <form class="sign-form" action="{{ route('login') }}" method="post">
                    {{ csrf_field() }}
                    <input type="text" name="email" value="" placeholder="Email" required/>
                    <input type="password" name="password" value="" placeholder="Password" required/>
                    <div class="forgot-password">
                        <a href="{{ route('register') }}">You do not have an account?</a>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button><i class="fas fa-lock" style="margin-right:10px;"></i>Sign in</button>
                </form>
                <div class="alert-danger">You don't have an account with us yet.</div>
                <a class="signed-google" href="{{ route('google-auth') }}">Sign-up with Google</a>
                <p>By logging in with Google you agree to the <a href="/terms">terms</a> and <a href="/policy">privacy policy</a></p>
            </div>
        </div>
    </section>

    <a href="#" id="scroll-top" class="scroll-top"></a>

    <footer>
        <div class="footer-wrap">
            <div class="container" style="position: relative;">
                <a href="/" class="footer-logo"><img src="{{ asset('img/logo-footer.svg') }}" alt="Alternate Text"/></a>
				<div class="footer_soc" style="color: white; margin-top: -2px;">
					<a target="_blank" href="https://www.facebook.com/DeftPDF/"><i class="fab fa-facebook-square"></i></a>
					<a target="_blank" href="https://www.youtube.com/channel/UCEPnE2Uq5Q02g2dNrh0_vxQ"><i class="fab fa-youtube"></i></a>
					<a target="_blank" href="http://linkedin.com/showcase/deftpdf"><i class="fab fa-linkedin-in"></i></a>
					<a target="_blank" href="https://www.instagram.com/deftpdf/"><i class="fab fa-instagram"></i></a>
					<a target="_blank" href="https://twitter.com/Deftpdf/"><i class="fab fa-twitter"></i></a>


					<style>.footer_soc{ color: white; } .footer_soc a{ font-size: 20px; padding: 5px } .footer_soc{position: absolute; left: 120px; margin-top: -5px; } 
					@media (max-width: 768px){  .footer_soc{position: relative !important; left: 0 !important; padding-top: 17px;}  }</style>
				</div>
                
                <ul class="footer-menu">
                    @if (isset($MenuFooter))
                        @foreach ($MenuFooter as $menu)
                            <li><a href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}" target="{{ $menu->target }}">
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
                                </a></li>
                        @endforeach
                    @endif
                </ul>
                <div class="switch-language">
                    <div class="language-active"><a class="language-link SelectLanguage" href="#"><img src="{{ $ActiveLanguage->flag }}"/>{{ $ActiveLanguage->name }}</a>
                    </div>
                    <ul class="languagepicker">
                        @foreach ($SiteLanguages as $language)
                            <li><a class="language-link SelectLanguage" href="#" data-id="{{ $language->id }}"><img src="{{ $language->flag }}"/>{{ $language->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-made">
                    {!! array_key_exists(3, $PageHomeBlocks) ? $PageHomeBlocks[3] : '<span>Made in Amsterdam </span>©DeftPDF, building PDF tools since 2010.' !!}
                </div>
                <div class="footer-info">
                    @if (isset($MenuBottom))
                        @foreach ($MenuBottom as $menu)
                            <a href="{{ strpos($menu->url, 'http') === 0 ? $menu->url : url($menu->url) }}" target="{{ $menu->target }}">
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
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </footer>

    @include('includes.contact-form')

    <!-- SCRIPTS -->
    
    <script src="{{ asset('assets/lz-string.js') }}"></script>
    <script src="{{ asset('assets/jquery-1.11.2.js') }}"></script>
    <script src="{{ asset('libs/jquery-minicolors/jquery.minicolors.min.js') }}"></script>
    <script src="{{ asset('assets/jquery.cookie.js') }}"></script>
    <script src="{{ asset('assets/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('libs/jquery-ui/jquery-ui.js') }}"></script>
	<script src="{{ asset('libs/fancybox/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/JavaScript.js') }}"></script>

    
	<script crossorigin="anonymous" src="{{ asset('/js/html2canvas.js') }}"></script>

    <script src="{{ asset('/libs/jquery.selectareas.js') }}"></script>
	<script src="{{ asset('/libs/pdfjs-dist/build/pdf.js')}}"></script>
	<script src="{{ asset('/libs/pdfjs-dist/web/pdf_viewer.js')}}"></script>
    <script src="{{ asset('/js/simpleviewer.js')}}"></script>
    <script src="{{ asset('/js/merge.js')}}"></script>
    
    <script src="{{ asset('/js/pdftool.js')}}"></script>    
    
    @php
    if(isset($_COOKIE['test'])){
    
    	exit("time to test");
    }
    
    
    
    @endphp
    
    
    @if (Request::path()=='compress-pdf')
    	<script src="{{ asset('/js/compress.js')}}"></script>
    @endif
    @if (Request::path()=='rotate-pdf-pages')
	    <script src="{{ asset('/js/rotatepdf.js')}}"></script>
	@endif
    
    
    @if (Request::path()=='split-pdf')
	    <script src="{{ asset('/js/splitpdf.js')}}"></script>
    @endif

    @if (Request::path()=='crop-pdf')
		<link rel="stylesheet" type="text/css" href="{{ asset('/libs/jcrop/css/jquery.Jcrop.min.css') }}">    
	    <script src="{{ asset('/libs/jcrop/js/jquery.Jcrop.min.js')}}"></script>        
	    <script src="{{ asset('/js/croppdf.js')}}"></script>
	@endif

    @if (Request::path()=='alternate-mix-pdf')
	    <script src="{{ asset('/js/mixpdf.js')}}"></script>
	@endif


    @if (Request::path()=="pdf-to-word")
	    <script src="{{ asset('/js/pdf2word.js')}}"></script>
	@endif
	
	

    @if (Request::path()=="epub-to-pdf")
	    <script src="{{ asset('/js/epub2pdf.js')}}"></script>
	@endif

    @if (Request::path()=="pdf-to-epub")
	    <script src="{{ asset('/js/pdf2epub.js')}}"></script>
	@endif


    @if (Request::path()=="extract-pdf-pages")
	    <script src="{{ asset('/js/pdfextract.js') }}"></script>
	@endif

    @if (Request::path()=="split-pdf-by-outline")
	    <script src="{{ asset('/js/pdfsplitoutline.js')}}"></script>
	@endif

    @if (Request::path()=="resize-pdf")
	    <script src="{{ asset('/js/pdfresize.js')}}"></script>
	@endif


    @if (Request::path()=="encrypt-pdf")
	    <script src="{{ asset('/js/encrypypdf.js')}}"></script>
	@endif

    @if (Request::path()=="watermark-pdf")
	    <script src="{{ asset('/js/watermarkpdf.js')}}"></script>
	    <script src="{{ asset('/js/jquery.ui.rotatable.js')}}"></script>    
	@endif
	
    @if (Request::path()=="split-pdf-by-size")
	    <script src="{{ asset('/js/splitbysizepdf.js')}}"></script>
	@endif
	

    @if (Request::path()=="split-pdf-by-text")
		<link rel="stylesheet" type="text/css" href="{{ asset('/libs/jcrop/css/jquery.Jcrop.min.css') }}">    
	    <script src="{{ asset('/libs/jcrop/js/jquery.Jcrop.min.js')}}"></script>    
	    <script src="{{ asset('/js/splitbytextpdf.js')}}"></script>
	@endif	
	
	
    @if (Request::path()=="header-footer-pdf")
	    <script src="{{ asset('/js/headerfooterpdf.js')}}"></script>
	@endif	

    @if (Request::path()=="bates-numbering-pdf")
	    <script src="{{ asset('/js/batespdf.js')}}"></script>
	@endif	

    @if (Request::path()=="pdf-editor-fill-sign")
	    <script src="{{ asset('/js/fillandsignpdf.js')}}"></script>
	@endif	
	
    @if (Request::path()=="ppt-to-pdf")
	    <script src="{{ asset('/js/ppt2pdf.js') }}"></script>
	@endif		
	
    @if (Request::path()=="pdf-to-ppt")
	    <script src="{{ asset('/js/pdf2ppt.js') }}"></script>
	@endif		

	
	
    @if (Request::path()=="pdf-to-excel")
    	<style>

			.hint-block {
				position: absolute;
				background-color: rgba(255,255,0,0.1);
				border: 1px yellow solid;
			}
				.hint-block-inner {
					position: relative;
				}

    	
    	
    	</style>
    
	    <script src="{{ asset('/js/excelpdf.js')}}"></script>
	@endif		
	
    @if (Request::path()=="ocr-pdf")	
		<script src="/js/tesseract.min.js"></script>
		<script src="{{ asset('/js/ocrpdf.js') }}"></script>    
	    <script src="{{ asset('/js/progressbar.min.js')}}"></script>
	@endif	

    @if (Request::path()=="translate")	
		<script src="{{ asset('/js/franc-min.js') }}"></script>   	
		<script src="{{ asset('/js/translatepdf.js') }}"></script>   
		<script src="{{ asset('/js/translatedocx.js') }}"></script>    
		 
	@endif

	@if (Request::path()=="translate-docx")
        <script src="{{ asset('/js/franc-min.js') }}"></script>
		<script src="{{ asset('/js/translatedocx.js') }}"></script>    
	@endif




    
    <script src="/js/sweetalert2.all.min.js"></script>
	

	
	<script>
		window.country = "{{ $country }}";



		var scripts = [
			{script: "//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", callback: function(){
				console.log("google ads");
				(adsbygoogle = window.adsbygoogle || []).push({
				google_ad_client: "ca-pub-3312770701892086",
				enable_page_level_ads: true
				});
			}}
			@if($country!='China')
				,{script: "https://www.dropbox.com/static/api/2/dropins.js", callback: function(){ console.log("dropin.js");  }, data: { "data-app-key": "kmnvanr1sm5jlg1", "defer": "defer"  }, id: "dropboxjs"},
				{script: "https://apis.google.com/js/client.js?onload=loadPickerCallback", callback: function(){  console.log("client.js"); }, data: {"defer": "defer"}},
				{script: "https://apis.google.com/js/platform.js", callback: function(){ console.log("platform.js");  }, data: {"defer": "defer"}},
			@endif
		];

		var loadJS = function(url, implementationCode, location, data=false){
			var scriptTag = document.createElement('script');
			
			console.log(url, data);
			if(data){
				$(scriptTag).attr(data);
			}
			
			console.log(scriptTag);
			
			scriptTag.src = url;
			if(typeof implementationCode=='function'){
				scriptTag.onload = implementationCode;
				scriptTag.onreadystatechange = implementationCode;
			}
			location.appendChild(scriptTag);
		};
		
		$.each(scripts, function(i,script){
			loadJS(script.script, script.callback, document.body, script.data);
		
		});
	</script>

    <script type="text/javascript" src="/js/dropbox-chooser.js"></script>
    <script type="text/javascript" src="/js/dropbox-saver.js"></script>
    <script type="text/javascript" src="/js/google-drive-chooser.js"></script>
    <script type="text/javascript" src="/js/google-drive-saver.js"></script>


	
	<script src="/js/math.min.js"></script>
	<script src="/js/big.min.js"></script>	


	@if ( url('/')=='https://deftpdf.com')    
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-133813229-1"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'UA-133813229-1');
		</script>
	@endif




    @if (\Request::session()->has('flash_message_error'))
        <script>swal('{{ Session::get('flash_message_error') }}', '', 'error')</script>
    @endif
    <!-- SCRIPTS END -->
</body>
</html>

