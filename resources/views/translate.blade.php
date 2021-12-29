
<?php 
if(!isset($_COOKIE['debug'])){
//	exit("maintenance");

}
?>

@extends('layouts.layout')

@section('content')	
	 <link rel="stylesheet" href="/css/pdf-translate.css"> 
	<script src="https://js.stripe.com/v3/"></script>
	
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
			
			startTimer();


		
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
			.t-block-item .img-wrap::after{
				background-image: none;
			}
			.t-block-item{
				cursor: pointer;
			}
		</style>
		<div class="block_message hidden" style="">
			<span style="padding: 20px; background: white;">
				<?// $x['message'] ?>
			</span>
		</div>
	<?php } ?>

	<script>
		var awsk1 = '{!! \App\Option::option('aws_pub') !!}';
		var awsk2 = '{!! \App\Option::option('aws_priv') !!}';
		var stripe_pub = '{!! \App\Option::option('stripe_pub') !!}'; 
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


		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
		@endif  

    	
    </div>
    <div id="editor"></div>



	<section class="s-translate after_upload hidden">
		<div class="translate-left t-block">
			<div class="translate-page-slider">

				<section class="pdf_files_slider pages_preview_block" id="pages_previews_here">

				</section>

				<div class="credit_card_popup_wrp one">
					<div class="credit_card_popup">
						<div class="popup_header">
						<span class="mobile_close_button">
							<img src="img/close.svg" alt="">
						</span>
							<h3>Credit card info</h3>
							<div class="icons">
								<img src="img/popup_creditcard_header_img_1.png" alt="">
								<img src="img/popup_creditcard_header_img_2.png" alt="">
								<img src="img/popup_creditcard_header_img_3.png" alt="">
							</div>
						</div>
						<div class="popup_content">
							<form id="stripe_form">
								<div class="credit_card_input_row two_col">
								    <div class="col">
								        <label>Full Name:</label>
								        <input type="text" name="name" id="name" class="credit_card_inputs" placeholder="Full name" required>
								    </div>
								    <div class="col">
								        <label>Email address:</label>
								        <input type="email" id="email" name="email" type="email" class="credit_card_inputs" placeholder="Email address" required>
								    </div>
								</div>
								<div class="credit_card_input_row">
								    <label>Credit or debit card:</label>
								    
								    
								    <div class="credit_card_info_inputs_wrp" style="padding: 0; padding-left: 17px;">
									    <div id="card-element" class="field"></div>
								    </div>
								</div>
								<button class="button-green submit_payment_button">Submit Payment</button>
							</form>
						</div>
					</div>
					<div class="credit_card_close_wrp"></div>
				</div>
				
				
				<div class="credit_card_popup_wrp two">
					<div class="credit_card_popup succesfull">
						<div class="popup_content">
							<img src="img/popup_credit_card__checked.svg" alt="">
							<h3>Your payment was successfull</h3>
							<a href="" class="button-green popup_credit_continue">Continue</a>
						</div>
					</div>
				</div>
			</div>
			<div class="translate-left-bottom-panel">
				<div class="translate-upload-pdf hidden">
					<h3>Upload another PDF</h3>
						<div class="btn-wrap">
							<a href="#" class="button-green">Upload file</a>
					</div>
				</div>
				<div class="translate-block-items-wrap">
					<div class="preview_title">Preview</div>
					<div class="translate-block-items" id="thumbs_block">
					</div>
				</div>
			</div>
		</div>
		<div class="translate-right">
			<div class="translate-right-inner">
				<div class="bill-box"><div class="img-wrap" style="min-height: 50px;">
					
					</div></div>
			
				<div class="before_translate translate-action-box show show_if_free_translate">
					<div class="icon"><img src="img/translator-icon.svg" alt=""></div>
					<p ><span class='total_chars'>0</span> symbols in document</p>
					<div class="btn-wrap">
						<a href="#" class="button-green translate_button hidden show_if_free_translate" id="translate_all">Translate</a>
					</div>
				</div>
				
				<div class="before_translate pay-and-translate-action-box payment_translate show_if_paid_translate hidden">
					<div class="icon"><img src="img/translator-icon.svg" alt=""></div>
					<p><span class='total_chars'>0</span> symbols in document</p>
					
					<ul class="blocked_translate hidden">
						<li><h3>You have reached the free trial limit:</h3></li>
						<li>Pay to translate more than one file every 5 hours</li>
						<li>Comeback in 5 hrs and translate for free (max 20000 symbols)</li>
						<li>Translation Price: $<span class="pricing">0</span></li>
						<li>(<span class="price_count_1">0</span>$ per <span class="price_count_2"></span> symbols)</li>
						<li><span id="c_timer"></span></li>
						
					</ul>
					
					<ul class="not_blocked_transalte">
						<li>Exceeds our free tier (<span class="free_count"></span>).</li>
						<li>Translation Price: $<span class="pricing">0</span></li>
						<li>(<span class="price_count_1">0</span>$ per <span class="price_count_2"></span> symbols)</li>
					</ul>
					
					<div class="btn-wrap">
						<a href="#" class="button-green creditcard_pop_open">Pay and translate</a>
					</div>
				</div>


				<div class="translate_proggress translated-doc-box loading-in-process hidden">
					<div class="translated-in-process">
						<div class="img-loading">
							<img src="img/spinner-of-dots.svg" alt="">
						</div>
						<p class="translate_percent_progress"><span class='total_translated'>0</span> of <span class='total_chars'></span> symbols translated</p>
					</div>
				</div>


				<div class="after_translate translated-doc-box hidden download_outer" style="position: relative;">
					<div class="translated-ready">
						<div class="img-ready">
							<img src="img/process-ready-icon.svg" alt="">
						</div>
						<p>Your document has been translated</p>
					</div>
					<div class="btn-wrap">
						<a href="#" class="button-green" id="download_file">Create & Download</a>
					</div>
					
					<div class="ss" style="text-align: center; font-size: 15px; line-height: 22px; margin-top: 30px;">
						Does this site help you save time or money?<br>
						Say thanks by sharing the website :)            
					</div>

				    <ul class="result-socials">
				        <li>
				            <a target="_blank" href="https://www.facebook.com/sharer.php?u=<?= URL::to('/'); ?>">
				                <img src="/img/soc-facebook.svg" alt="Share on Facebook">
				            </a>
				        </li>
				        <li>
				            <a target="_blank" href="https://twitter.com/intent/tweet?text=Easy to use Online PDF editor <?= URL::to('/'); ?> @DeftPDF">
				                <img src="/img/soc-twitter.svg" alt="Share on Twitter">
				            </a>
				        </li>
				        <li>
				            <a target="_blank" href="https://plus.google.com/share?url=<?= URL::to('/'); ?>">
				                <img src="/img/soc-google-plus.svg" alt="Share on Google Plus">
				            </a>
				        </li>
				        <li>
				            <a  href="mailto:?&subject=Easy to use Online PDF editor DeftPDF&body=<?= URL::to('/'); ?>">
				                <img src="/img/soc-email.svg" alt="">
				            </a>
				        </li>
				        <!-- <li>
							<div class="fb-like" data-href="https://facebook.com/deftpdf" data-width="100" data-layout="button" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>                
				        </li> -->
				        <li>
				            <a href="#" onclick="window.open('https://www.linkedin.com/shareArticle?mini=true&url=https%3A//deftpdf.com/&title=Deftpdf&summary=&source=', 'Share', 'width=500,height=300')">
				            <img src="/img/linkedin-button.svg" alt="">
				        </a>
							<!-- <script src="https://platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
							<script type="IN/FollowCompany" data-id="19133308" data-counter="bottom"></script>    -->
				        </li>
				        



				    </ul>

					
				</div>
				<div class="t-right-bottom-panel">
				
					<div class="t-upload-another-pdf">
						<div class="debug_outer hide_after_start hidden">
							<label>
							<input id="debug_text"  type="checkbox">Debug(return original text)
							</label>
						</div>
						<div class="document_render_proccess" style="margin-top: 27px;">
							Processing page <span style="font-weight: bold; color: #7a4cdc; font-size: 18px;" class='current_page'>0</span> out of <span style="font-weight: bold; color: #7a4cdc; font-size: 18px;" class="total_pages">0</span>
						</div>
						
						<div class="select-group-item hide_after_start language_choise hidden">
							<select class="source_language lang_source">
							<option value="af">Afrikaans</option>
							<option value="sq">Albanian</option>
							<option value="am">Amharic</option>
							<option value="hy">Armenian</option>
							<option value="az">Azerbaijani</option>
							<option value="eu">Basque</option>
							<option value="be">Belarusian</option>
							<option value="bn">Bengali</option>
							<option value="bs">Bosnian</option>
							<option value="bg">Bulgarian</option>
							<option value="ca">Catalan</option>
							<option value="ceb">Cebuano</option>
							<option value="ny">Chichewa</option>
							<option value="zh-CN">Chinese (Simplified)</option>
							<option value="zh-TW">Chinese (Traditional)</option>
							<option value="co">Corsican</option>
							<option value="hr">Croatian</option>
							<option value="cs">Czech</option>
							<option value="da">Danish</option>
							<option value="nl">Dutch</option>
							<option value="en" selected>English</option>
							<option value="eo">Esperanto</option>
							<option value="et">Estonian</option>
							<option value="tl">Filipino</option>
							<option value="fi">Finnish</option>
							<option value="fr">French</option>
							<option value="fy">Frisian</option>
							<option value="gl">Galician</option>
							<option value="ka">Georgian</option>
							<option value="de">German</option>
							<option value="el">Greek</option>
							<option value="gu">Gujarati</option>
							<option value="ht">Haitian Creole</option>
							<option value="ha">Hausa</option>
							<option value="haw">Hawaiian</option>
							<option value="iw">Hebrew</option>
							<option value="hi">Hindi</option>
							<option value="hmn">Hmong</option>
							<option value="hu">Hungarian</option>
							<option value="is">Icelandic</option>
							<option value="ig">Igbo</option>
							<option value="id">Indonesian</option>
							<option value="ga">Irish</option>
							<option value="it">Italian</option>
							<option value="ja">Japanese</option>
							<option value="jw">Javanese</option>
							<option value="kn">Kannada</option>
							<option value="kk">Kazakh</option>
							<option value="km">Khmer</option>
							<option value="ko">Korean</option>
							<option value="ku">Kurdish (Kurmanji)</option>
							<option value="ky">Kyrgyz</option>
							<option value="lo">Lao</option>
							<option value="la">Latin</option>
							<option value="lv">Latvian</option>
							<option value="lt">Lithuanian</option>
							<option value="lb">Luxembourgish</option>
							<option value="mk">Macedonian</option>
							<option value="mg">Malagasy</option>
							<option value="ms">Malay</option>
							<option value="ml">Malayalam</option>
							<option value="mt">Maltese</option>
							<option value="mi">Maori</option>
							<option value="mr">Marathi</option>
							<option value="mn">Mongolian</option>
							<option value="my">Myanmar (Burmese)</option>
							<option value="ne">Nepali</option>
							<option value="no">Norwegian</option>
							<option value="ps">Pashto</option>
							<option value="fa">Persian</option>
							<option value="pl">Polish</option>
							<option value="pt">Portuguese</option>
							<option value="pa">Punjabi</option>
							<option value="ro">Romanian</option>
							<option value="ru">Russian</option>
							<option value="sm">Samoan</option>
							<option value="gd">Scots Gaelic</option>
							<option value="sr">Serbian</option>
							<option value="st">Sesotho</option>
							<option value="sn">Shona</option>
							<option value="sd">Sindhi</option>
							<option value="si">Sinhala</option>
							<option value="sk">Slovak</option>
							<option value="sl">Slovenian</option>
							<option value="so">Somali</option>
							<option value="es">Spanish</option>
							<option value="su">Sundanese</option>
							<option value="sw">Swahili</option>
							<option value="sv">Swedish</option>
							<option value="tg">Tajik</option>
							<option value="ta">Tamil</option>
							<option value="te">Telugu</option>
							<option value="th">Thai</option>
							<option value="tr">Turkish</option>
							<option value="uk">Ukrainian</option>
							<option value="ur">Urdu</option>
							<option value="uz">Uzbek</option>
							<option value="vi">Vietnamese</option>
							<option value="cy">Welsh</option>
							<option value="xh">Xhosa</option>
							<option value="yi">Yiddish</option>
							<option value="yo">Yoruba</option>
							<option value="zu">Zulu</option>
							</select>
							
							<span class="language_choise hidden">to</span>
							
							<select class="target_language language_choise hidden">
								<option value="af">Afrikaans</option>
								<option value="sq">Albanian</option>
								<option value="am">Amharic</option>


								<option value="hy">Armenian</option>
								<option value="az">Azerbaijani</option>
								<option value="eu">Basque</option>
								<option value="be">Belarusian</option>
								<option value="bn">Bengali</option>
								<option value="bs">Bosnian</option>
								<option value="bg">Bulgarian</option>
								<option value="ca">Catalan</option>
								<option value="ceb">Cebuano</option>
								<option value="ny">Chichewa</option>
								<option value="zh-CN">Chinese (Simplified)</option>
								<option value="zh-TW">Chinese (Traditional)</option>
								<option value="co">Corsican</option>
								<option value="hr">Croatian</option>
								<option value="cs">Czech</option>
								<option value="da">Danish</option>
								<option value="nl">Dutch</option>
								<option value="en">English</option>
								<option value="eo">Esperanto</option>
								<option value="et">Estonian</option>
								<option value="tl">Filipino</option>
								<option value="fi">Finnish</option>
								<option value="fr">French</option>
								<option value="fy">Frisian</option>
								<option value="gl">Galician</option>
								<option value="ka">Georgian</option>
								<option value="de">German</option>
								<option value="el">Greek</option>
								<option value="gu">Gujarati</option>
								<option value="ht">Haitian Creole</option>
								<option value="ha">Hausa</option>
								<option value="haw">Hawaiian</option>
								<option value="iw">Hebrew</option>
								<option value="hi">Hindi</option>
								<option value="hmn">Hmong</option>
								<option value="hu">Hungarian</option>
								<option value="is">Icelandic</option>
								<option value="ig">Igbo</option>
								<option value="id">Indonesian</option>
								<option value="ga">Irish</option>
								<option value="it">Italian</option>
								<option value="ja">Japanese</option>
								<option value="jw">Javanese</option>
								<option value="kn">Kannada</option>
								<option value="kk">Kazakh</option>
								<option value="km">Khmer</option>
								<option value="ko">Korean</option>
								<option value="ku">Kurdish (Kurmanji)</option>
								<option value="ky">Kyrgyz</option>
								<option value="lo">Lao</option>
								<option value="la">Latin</option>
								<option value="lv">Latvian</option>
								<option value="lt">Lithuanian</option>
								<option value="lb">Luxembourgish</option>
								<option value="mk">Macedonian</option>
								<option value="mg">Malagasy</option>
								<option value="ms">Malay</option>
								<option value="ml">Malayalam</option>
								<option value="mt">Maltese</option>
								<option value="mi">Maori</option>
								<option value="mr">Marathi</option>
								<option value="mn">Mongolian</option>
								<option value="my">Myanmar (Burmese)</option>
								<option value="ne">Nepali</option>
								<option value="no">Norwegian</option>
								<option value="ps">Pashto</option>
								<option value="fa">Persian</option>
								<option value="pl">Polish</option>
								<option value="pt">Portuguese</option>
								<option value="pa">Punjabi</option>
								<option value="ro">Romanian</option>
								<option value="ru" selected>Russian</option>
								<option value="sm">Samoan</option>
								<option value="gd">Scots Gaelic</option>
								<option value="sr">Serbian</option>
								<option value="st">Sesotho</option>
								<option value="sn">Shona</option>
								<option value="sd">Sindhi</option>
								<option value="si">Sinhala</option>
								<option value="sk">Slovak</option>
								<option value="sl">Slovenian</option>
								<option value="so">Somali</option>
								<option value="es">Spanish</option>
								<option value="su">Sundanese</option>
								<option value="sw">Swahili</option>
								<option value="sv">Swedish</option>
								<option value="tg">Tajik</option>
								<option value="ta">Tamil</option>
								<option value="te">Telugu</option>
								<option value="th">Thai</option>
								<option value="tr">Turkish</option>
								<option value="uk">Ukrainian</option>
								<option value="ur">Urdu</option>
								<option value="uz">Uzbek</option>
								<option value="vi">Vietnamese</option>
								<option value="cy">Welsh</option>
								<option value="xh">Xhosa</option>
								<option value="yi">Yiddish</option>
								<option value="yo">Yoruba</option>
								<option value="zu">Zulu</option>
							</select>
						</div>


						<div class="translate-upload-pdf upload_another_pdf hidden">
							<div class="btn-wrap" >
								<a style="margin-top: 30px;" href="/translate"  class="button-green">Start over</a>
							</div>
						</div>


					</div>
				</div>
			</div>
		</div>
	</section>

    @if (count($PageGuides))
        <section class="how-it-works before_upload">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section"><h2>{{ $Guide->title }}</h2></div>
                @endif



				@if($ads && $device_is=='phone')
					@include("ads.adx320x100")
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


    
    <div id="tempo">
    
    </div>
    
    <div id="ocr_section">
    
    </div>
    
    <div class='' style="font-size: 1px; font-family: 'sans-serif'; ">
    	test
    </div>
	<style>
    	#loading1{
			position: absolute;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background:
			#8080807d;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
    	}

		#loading1 .img-loading{
			-webkit-animation: 3s spinnerRotate linear infinite;
			-o-animation: 3s spinnerRotate linear infinite;
			animation: 3s spinnerRotate linear infinite;
			margin-bottom: 43px;
		}


	</style>
    

    
    <script>
    	var pricing = {!! $pricing !!};
    </script>
	@include ('inc.result_block_new')
    
@endsection