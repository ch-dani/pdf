@extends('layouts.layout')

@section('content')	
	<script src="https://js.stripe.com/v3/"></script>
	<script src="https://sdk.amazonaws.com/js/aws-sdk-2.435.0.min.js"></script>

	<script>
		var awsk1 = '{!! \App\Option::option('aws_pub') !!}';
		var awsk2 = '{!! \App\Option::option('aws_priv') !!}';
		var stripe_pub = '{!! \App\Option::option('stripe_pub') !!}'; 
	
		var stripe_form = `
			<form>
				<div class="stripe_errors group hidden">
				</div>
				
				<div class="group">
					<label>
						<span>Name</span>
						<input name="cardholder-name" class="field" placeholder="Jane Doe" />
					</label>
					<label>
						<span>Phone</span>
						<input class="field" placeholder="(123) 456-7890" type="tel" />
					</label>
				</div>
				<div class="group">
					<label>
						<span>Card</span>
						<div id="card-element" class="field"></div>
					</label>
				</div>
				<div class="outcome hidden">
					<div class="error"></div>
					<div class="success">
						Success! Your Stripe token is <span class="token"></span>
					</div>
				</div>
			</form>
		`;

		var stripe_form = `
			<form>
				<div class="stripe_errors group hidden">
				</div>

				<div class="group">
					<label for="email">
						<span>Email</span>
						<input id="email" name="email" type="email" placeholder="jenny.rosen@example.com" class="field" required>
					</label>
				</div>

				<div class="group">
					<label>
						<span>Card</span>
						<div id="card-element" class="field"></div>
					</label>
				</div>
				<div class="outcome hidden">
					<div class="error"></div>
					<div class="success">
						Success! Your Stripe token is <span class="token"></span>
					</div>
				</div>
			</form>
		`;

		
	</script>




	
    <div class="upload-top-info before_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'OCR Recognize Text in PDF Online<sup>BETA</sup>' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert PDF scans to searchable text and PDFs. Quickly extract text from scans' !!}</p>
    			</div>
    		</div>
    		<div class="app-welcome">
    			<form action="#" id="drop_zone">
	            	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
    				<div class="upload-img">
    					<img style="height: 98px;" src="{{ asset('img/docx-img.svg') }}" alt="">
    				</div>
					<h3>UPLOAD <strong>DOCX</strong> FILE</h3>
					@include('inc.uploadButtonDocx')
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>
    <div id="editor"></div>


	<section id="empty_pages" class="hidden">
		
	</section>

    <section class="recognize-text-online after_upload hidden" id="ocr_section" style="position: relative;">
    	<div class="language_not_selected">
    		<div class="fixed_text">
    			<div class="fixed_text_bg">
	    			Please, select source language for translation.
    			</div>
    		</div>
    	</div>
    	<div class="pages_preview_block" id="pages_previews_here">
    		<div class="page_item">
				<div class="recognize-left-pdf">
				</div>
				<div class="recognize-midle">
				    <span>1</span>
				    <img src="img/arow-next.svg" alt="Alternate Text" />
				</div>
				<div class="recognize-right-info">
					<div class="recognize_log">
						
					</div>
					<div class="recognize-info-block">
					</div>
				</div>
			</div>
        </div>
    </section>


    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
                @endif

                <div class="container centered">
                    @if (!is_null($Guide->subtitle))
                    <p class="title-description">{{ $Guide->subtitle }}</p>
                    @endif
                    @if (!is_null($Guide->content))
                    <div class="post">
                        {!! htmlspecialchars_decode($Guide->content) !!}
                    </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

	<section class="fixed-bottom-panel hidden after_upload" >
		<div class="translate_settings">
			<div class="left_part">
				<select autocomplete="off" class="select source_language lang_source">
					<option selected value="auto">Select language</option>
					<option value="en">English</option>
					<option value="ar">Arabic</option>
					<option value="cs">Czech</option>
					<option value="de">German</option>
					<option value="es">Spanish</option>
					<option value="fr">French</option>
					<option value="it">Italian</option>
					<option value="ja">Japanese</option>
					<option value="pt">Portuguese</option>
					<option value="ru">Russian</option>
					<option value="tr">Turkish</option>
					<option value="zh">Chinese</option>
					<option value="zh-TW">Chinese (Traditional)</option>
				</select>
				<span class="span_to">TO</span>
				<select class="select target_language target_lang">
					<option value="en">English</option>
					<option value="ar">Arabic</option>
					<option value="cs">Czech</option>
					<option value="de">German</option>
					<option value="es">Spanish</option>
					<option value="fr">French</option>
					<option value="it">Italian</option>
					<option value="ja">Japanese</option>
					<option value="pt">Portuguese</option>
					<option value="ru">Russian</option>
					<option value="tr">Turkish</option>
					<option selected value="zh">Chinese</option>
					<option value="zh-TW">Chinese (Traditional)</option>
				</select>
			</div>
			<div class="center_part hidden">
				<h3 class="page_num_title">Page <span class="viewed_page_num">1</span></h3>
			</div>
			
			<div class="right_part">
				<div class="buttons_part">
				    <button class="options-btn on_translated_page hidden" type="button" id="save_one_page" data-page_num="1">Save</button>
				    <?php /*<button class="options-btn on_translated_page hidden" type="button" id="edit_one_page" data-page_num="1">Save & Edit page</button>*/ ?>
				    <?php /*<button class="options-btn on_untranslated_page" type="button" id="translate_one_page" data-page_num="1">Translate page <span class="viewed_page_num">1</span></button>*/ ?>
				    <button class="options-btn" type="button" id="translate_all">Translate</button>
				    <button class="options-btn on_all_translated hidden" type="button" id="save_all">Download</button>
				    <?php /*<button class="options-btn on_all_translated hidden" type="button" id="save_and_edit_all">Save & Edit all</button>*/ ?>
		        </div>
			</div>
		</div>
    </section>
    <style>
    	
    	.recognize-right-info{
    		position: relative;
    	}
    	
    	.progress_block{
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: white;
			margin-top: 40px;
    	}
    
    	.translate_settings{
			padding: 15px;
			width: 100%;
			display: flex;
			justify-content: space-between;
			align-items: center;
    	}
    	.translate_settings .left_part{
    		display: flex;
			align-items: center;
    	}
		.fixed-bottom-panel.after_translate .left_part{
			display: none;
		}
		.fixed-bottom-panel.after_translate .right_part{
			text-align: center;
			width: 100%;
		}
    	.page_num_title{
	    	font-size: 30px;
    	}
    	
    	.left_part{
    		width: 33%;
    	}
    	.center_part{
    		width: 20%;
    		text-align: right;
    	}
    	
    	.right_part{
    		text-align: right;
    		width: 47%;
    	}
    	
    	.span_to{
			font-size: 22px;
			margin: 0 10px 0 10px;
    	}
    	.translated_canvas{
    		position: relative;
    	}
    	.translated_items_outer{
    		position: absolute;
    		top: 0;
    		left: 0;
    		width: 100%;
    		height: 100%;
    	}
    	.text_content_element{
			position: absolute;
			white-space: pre;
			cursor: text;
			transform-origin: 0% 0%;
			pointer-events: all !important;
    	}
    	.loader_label{
    		text-align: center;
    	}
		[id^="translated_canvas_"]{
			max-width: 100%;
		}
		.page_translate_settings{
			display: block;
		}
		.page_translate_settings .recognize-settings-block{
			width: 100%;
		}
		
		.permanently_hidden{
			display: none !important;
		}

		.language_not_selected{
			z-index: 9;
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background: rgba(128,128,128,0.301);
		}
		.language_not_selected .fixed_text{
			position: fixed;
			width: 100%;
			text-align: center;
			top: 45%;
			font-size: 32px;			
		}
		
		.fixed_text_bg {
			display: inline;
			width: auto;
			background: #ffffffb3;
			padding: 10px;
			box-shadow: 0 0 10px rgba(0,0,0,0.5);
		}

		.download-result-link{
			background-image: url(/img/docx-img.svg);
		}
    </style>
    
    <div class='' style="font-size: 1px; font-family: 'sans-serif'; ">
    	test
    </div>
    
    <script>
    	var pricing = {!! $pricing !!};
    </script>
	@include ('inc.result_block')
    
@endsection
