@extends('layouts.layout')

@section('content')	
	<script>
		window.need_payment = false;
	</script>
	<?php 

	$ip = \App\Http\Controllers\TranslatePDF::getUserIpAddr();

	$last = new \App\LastTranslate();
	$last_trans = $last->where(["ip"=>$ip])->first();
	

	

	
	if($x = \App\Http\Controllers\TranslatePDF::translateAv($last_trans)){


	?>
		<script>
		
			window.need_payment = true;
			var countDownDate = new Date(<?= $x['time']*1000 ?>).getTime();
			
			if(false){

			}
			
			
			function startTimer(){
				var x = setInterval(function() {

				  // Get today's date and time
				  var now = new Date().getTime();

				  // Find the distance between now and the count down date
				  var distance = countDownDate - now;

				  // Time calculations for days, hours, minutes and seconds
				  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
				  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
				  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

				  // Display the result in the element with id="demo"
				  document.getElementById("c_timer").innerHTML = hours.pad(2) + "h " + minutes.pad(2) + "m " + seconds.pad(2) + "s ";

				  // If the count down is finished, write some text
				  if (distance < 0) {
					clearInterval(x);
					document.getElementById("demo").innerHTML = "EXPIRED";
				  }
				}, 1000);
			
			}


		
		</script>
	
		<style>
			.block_message{
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background:
				#80808082;
				z-index: 100000;
				font-size: 36px;
				text-align: center;
				display: flex;
				align-items: center;
				justify-content: center;			
			}
		</style>
		<div class="block_message hidden" style="">
			<span style="padding: 20px; background: white;">
				<?// $x['message'] ?>
			</span>
		</div>
	<?php
	}else{
		
	}
	
	?>
	<script>
			window.u_ip = "<?= $ip ?>"
			//alert(u_ip);	
	</script>

	<script src="https://js.stripe.com/v3/"></script>
	<script src="https://sdk.amazonaws.com/js/aws-sdk-2.435.0.min.js"></script>

	<script>
		var awsk1 = '{!! \App\Option::option('aws_pub1') !!}';
		var awsk2 = '{!! \App\Option::option('aws_priv1') !!}';
		var stripe_pub = '{!! \App\Option::option('stripe_pub') !!}'; 
	

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
	<?php if(isset($_COOKIE['maintance'])){ ?>
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
    				<div class="upload-img multiple_formats_upload">
    					<img src="{{ asset('img/pdf-img.svg') }}" alt="pdf">
    					<span class="multi_or">{{ t("OR") }}</span>
     					<img style="height: 98px; margin-left: 5px;" src="{{ asset('img/docx.svg') }}" alt="docx">   					
    				</div>

					<h3>{!! t("UPLOAD <strong>PDF</strong> or <strong>DOCX</strong> FILE") !!}</h3>
					<div class="upload-btn-wrap">
						<div class="upload-button">
							<span>
								{{ t("Upload document") }}
							</span>
							<input class="user_pdf" type="file" accept="application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"  >
						</div>
						<button class="dropdown-toggle-btn" type="button">
							<i class="fas fa-caret-down"></i>
						</button>
						<ul class="dropdown-menu-upload">
							<li><a class="drpbox-chooser" href="#" id="drpbox-chooser"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
							<li><a class="gdrive-chooser" href="#" id="gdrive-chooser"><img class="icon" src="/img/gdrive.png" alt=""> Google Drive</a></li>
							<li><a class="weburl-chooser" href="#"><i class="fas fa-link icon"></i> {{ t("Web Address (URL)") }}</a></li>
						</ul>
					</div>

    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>
    
    <?php }else{ ?>
    	<h1 style="font-size: 50px;
margin-top: 200px;
margin-bottom: 200px;
text-align: center;">Maintenance</h1>
    
    <?php } ?>
    
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
				    <img src="/img/arow-next.svg" alt="Alternate Text" />
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
					<option selected value="auto">{{ t("Select language") }}</option>
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
					<option value="zh">Chinese (Simplified)</option>
					<option value="zh-TW">Chinese (Traditional)</option>
				</select>
				<span class="span_to">{{ t("TO") }}</span>
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
					<option selected value="zh">Chinese (Simplified)</option>
					<option value="zh-TW">Chinese (Traditional)</option>
				</select>
			</div>
			<div class="center_part">
				<h3 class="page_num_title">Page <span class="viewed_page_num">1</span></h3>
				<span class="total_progress" style="color:blue;
font-size: 19px;
margin-right: -7%;
margin-top: 23px;
padding-top: 23px;"></span>
				
			</div>
			
			<div class="right_part">
				<div class="render_proccess hidden" style="font-size: 30px;">
					Page processing 0/0
				</div>
				<div class="buttons_part hidden">
				    <button class="options-btn on_translated_page hidden" type="button" id="save_one_page" data-page_num="1">{{ t("Save page") }}</button>
				    <button class="options-btn on_translated_page hidden" type="button" id="edit_one_page" data-page_num="1">{{ t("Save & Edit page") }}</button>
				    <button class="options-btn on_untranslated_page" type="button" id="translate_one_page" data-page_num="1">{{ t("Translate page") }} <span class="viewed_page_num">1</span></button>
				    <button class="options-btn" type="button" id="translate_all">{{ t("Translate all") }}</button>
				    <button class="options-btn on_all_translated hidden" type="button" id="save_all">{{ t("Save all") }}</button>
				    <button class="options-btn on_all_translated hidden" type="button" id="save_and_edit_all">{{ t("Save & Edit all") }}</button>
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
    </style>
    
    <div class='' style="font-size: 1px; font-family: 'sans-serif'; ">
    	test
    </div>
    
    <script>
    	var pricing = {!! $pricing !!};
    </script>
	@include ('inc.result_block')
    
@endsection
