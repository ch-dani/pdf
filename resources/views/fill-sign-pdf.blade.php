@extends('layouts.layout')

@php
    $accept = 'application/pdf';
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded step1 fill-sign fill-sign-tool" >
        @include('page_parts.toolheader')

        <section id="watermark_section" class="tool_section crop-section bg-grey bg_grey_patterns after_upload hidden">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Encrypt and Protect PDF online' !!}</h2>
                    <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Protect PDF file with password and permissions. Add restrictions to your PDF file' !!}</h3>
                </div>

                <div id="preview_section" class="crop-section__page">
                    <div class="watermark-pdf-block" id="preview_block">
                        <div id="pages_previews_here">

                        </div>
                    </div>

                    
                    <div class="undo_redo hidden">
                    	<a class="undo" href="#" onclick="return whistory.undo();">
                    		<img src="{{asset('/freeconvert/img/Undo.svg')}}">
                    		<span>Undo</span>
                    	</a>
                    	<span class='seprator'></span>
                    	<a class="redo" href="#" onclick="return whistory.undo(true);">
		                	<img src="{{asset('/freeconvert/img/Redo.svg')}}">
		                	<span>Redo</span>
                    	</a>                    	
                    </div>
                </div>

                <ul class="document_add_element_submenu hidden" data-after-page="">
                    <li><a id="add_text_wattermark" href="">@php include(public_path('freeconvert/img/document_add_element_submenu_text.svg')) @endphp {{ t("Add text") }}</a></li>
                    <li><label>
                            @php include(public_path('freeconvert/img/document_add_element_submenu_image.svg')) @endphp {{ t("Add image") }}
                            <input id="image_upload" type="file" accept="image/*" name="name" value="" />
                        </label>
                    </li>
                    <li><a id="add_sign" href=""><img style="margin-right: 10px;" src="/img/sign_icon.png">{{ t("Add sign") }}</a></li>
                </ul>

                <div class="contact-us">
                    <a id="start_task" class="contact-us__button btn-gradient" href="#"><img src="{{ asset('freeconvert/img/download.svg') }}" width="30" height="30"> {{ t("Download PDF") }}</a>
                </div>

                <div class="link_convert one_item">
                    <div class="link_convert_left">
                        <a href="#" class="link_convert_item remove">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t("Remove") }}
                        </a>
                    </div>
                </div>

                {{--
                <div class="link_download">
                    <ul class="save">
                        <li class="save__li"><a href=""><img src="{{ asset('freeconvert/img/logo_google-drive.svg') }}" width="26" height="23">{{ t("Save to Google Drive") }}</a></li>
                        <li class="save__li"><a href=""><img src="{{ asset('freeconvert/img/logo_dropbox.svg') }}" width="28" height="23">{{ t("Save to Dropbox") }}</a></li>
                    </ul>
                </div>
                --}}
            </div>
        </section>

        <section class="module__how-convert module bg-white pb_5">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h2-title title_main">{{ t("How to Fill & Sign your PDF") }}</h2>
                </div>
                <div class="row">
                    @if (count($PageGuides))
                        @foreach ($PageGuides as $Guide)
                            @if (!is_null($Guide->content))
                                {!! htmlspecialchars_decode($Guide->content) !!}
                            @endif
                        @endforeach
                    @endif

                    @if(!Auth::id())
                        <div class="contact-us">
                            <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @include('inc-freeconvert.banner')
    </main>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('libs/jquery-ui/jquery-ui.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
    <style>
		
		
		.ui-rotatable-handle::before {
			margin-left: -35px;
			margin-top: -22px;
			position: absolute;
		}
    
    	.resizable_helper .ui-icon-gripsmall-diagonal-se{
    		display: block !important;
    		opacity: 0;	
    	}
    	
    	.watermark .text_content{
    		padding: 0 !important;	
    	}
    	#preview_section{
    		
    		margin: 0 !important;
    		padding: 0 !important;
    		background: none;
    	}
    	
    	#preview_section .page_block_p{
    		margin-top: 40px;
    	
    	}
    	
    	#preview_section .canvas_outer{
    		margin: 0 auto !important;
    		padding: 0 !important;
    	}
    	
    	.watermark_draggable_text .rotatable_helper::before{
			cursor: pointer;
			z-index: 99999;
		}
    
        @font-face {
            font-family: 'Courier';
            src: url('/editor_fonts/Courier.ttf')  format('truetype');
        }

        @font-face {
            font-family: 'Helvetica2';
            src: url('/fonts/Helvetica.eot');
            src: local('☺'), url('/editor_fonts/Helvetica.woff') format('woff'), url('/editor_fonts/Helvetica.ttf') format('truetype'), url('/editor_fonts/Helvetica.svg') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        .canvas_outer{
            overflow: hidden;
        }
        .undo_redo{
			background: white;
			padding: 10px;
			border: 1px solid #ebebeb;
			border-radius: 3px;
			position: fixed;
			bottom: 0;
			left: calc(50% - 115px);
			z-index: 9999;
			display: flex;
			justify-content: space-around;		
			width: 230px;
			
        }
        .undo_redo a{
        	color: #0C3E70;
        	font-size: 20px;
        	display: flex;
        }
        .undo_redo a:hover{
        	opacity: 0.7;	
        }
        
        .undo_redo a img{
        	margin-right: 3px;
        }
        
        .undo_redo .undo{
        	margin-right: 5px;
        }
        .undo_redo .redo{
        	margin-left: 5px;
        }
        
        .undo_redo .disabled{
        	color: gray;
        	cursor: not-allowed;
        }
        .undo_redo .seprator{
        	border: 1px solid #ebebeb;
        }

        
    </style>
@endsection

@section('js')
    <script src="{{asset('/js/signature_pad.min.js')}}"></script>
    <script src="{{asset('/js/watermarkpdf.js')}}"></script>
    <script src="{{asset('/js/history.js')}}"></script>

    <script src="{{ asset('libs/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{asset('/js/jquery.ui.rotatable.js')}}"></script>
@endsection
