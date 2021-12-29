@extends('layouts.layout')

@section('content')	
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
    					<img src="{{ asset('img/pdf-img.svg') }}" alt="">
    				</div>
					<h3>UPLOAD <strong>PDF</strong> FILE</h3>
					@include('inc.uploadButton')
    			</form>
    			<div class="upload-welcom-descr">
    				{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
    			</div>
    		</div>
    	</div>
    </div>
    



    <div class="upload-top-info hidden after_upload">
    	<div class="container">
    		<div class="app-title">
    			<div class="wrapper">
    				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'OCR Recognize Text in PDF Online<sup>BETA</sup>' !!}</h1>
    				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert PDF scans to searchable text and PDFs. Quickly extract text from scans' !!}</p>
    			</div>
    		</div>
    	</div>
    </div>

    <section class="recognize-text-online after_upload hidden" id="ocr_section">
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
						<p>Quick single page mode</p>
						<div class="recognize-btns-block">
							<select class="select language_select lang_select">
								<option value="eng" selected="selected">English</option>					
								<option value="spa">Spanish</option>							
								<option value="ita">Italian</option>											
								<option value="afr">Afrikaans</option>
								<option value="sqi">Albanian</option>
								<option value="grc">Ancient Greek</option>
								<option value="ara">Arabic</option>
								<option value="aze">Azerbaijani</option>
								<option value="eus">Basque</option>
								<option value="bel">Belarusian</option>
								<option value="ben">Bengali</option>
								<option value="bul">Bulgarian</option>
								<option value="cat">Catalan</option>
								<option value="chr">Cherokee</option>
								<option value="chi_sim">Chinese</option>
								<option value="hrv">Croatian</option>
								<option value="ces">Czech</option>
								<option value="dan">Danish</option>
								<option value="nld">Dutch</option>

								<option value="enm">English (Old)</option>
								<option value="epo">Esperanto</option>
								<option value="est">Estonian</option>
								<option value="fin">Finnish</option>
								<option value="frk">Frankish</option>
								<option value="fra">French</option>
								<option value="frm">French (Old)</option>
								<option value="glg">Galician</option>
								<option value="deu">German</option>
								<option value="ell">Greek</option>
								<option value="heb">Hebrew</option>
								<option value="hin">Hindi</option>
								<option value="hun">Hungarian</option>
								<option value="isl">Icelandic</option>
								<option value="ind">Indonesian</option>
								<option value="ita">Italian</option>
								<option value="jpn">Japanese</option>
								<option value="kan">Kannada</option>
								<option value="kor">Korean</option>
								<option value="lav">Latvian</option>
								<option value="lit">Lithuanian</option>
								<option value="mkd">Macedonian</option>
								<option value="msa">Malay</option>
								<option value="mal">Malayalam</option>
								<option value="mlt">Maltese</option>
								<option value="equ">Math</option>
								<option value="nor">Norwegian</option>
								<option value="spa_old">Old Spanish</option>
								<option value="fas">Persian (Farsi)</option>
								<option value="pol">Polish</option>
								<option value="por">Portuguese</option>
								<option value="ron">Romanian</option>
								<option value="rus">Russian</option>
								<option value="srp">Serbian</option>
								<option value="slk">Slovakian</option>
								<option value="slv">Slovenian</option>
								<option value="swa">Swahili</option>
								<option value="swe">Swedish</option>
								<option value="tgl">Tagalog</option>
								<option value="tam">Tamil</option>
								<option value="tel">Telugu</option>
								<option value="tha">Thai</option>
								<option value="chi_tra">Traditional Chinese</option>
								<option value="tur">Turkish</option>
								<option value="ukr">Ukrainian</option>
								<option value="vie">Vietnamese</option>

							</select>
							<a href="#" class="button-green">Recognize text on this page</a>
						</div>
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

	<section class="fixed-bottom-panel hidden after_upload">
        <form class="fixed-task-form">
			<div class="recognize-settings">
				<div class="recognize-settings-block">
					<span class="recognize-settings-name">Language:</span>
					<select class="select lang_select">
								<option value="eng" selected="selected">English</option>					
								<option value="spa">Spanish</option>							
								<option value="ita">Italian</option>											
								<option value="afr">Afrikaans</option>
								<option value="sqi">Albanian</option>
								<option value="grc">Ancient Greek</option>
								<option value="ara">Arabic</option>
								<option value="aze">Azerbaijani</option>
								<option value="eus">Basque</option>
								<option value="bel">Belarusian</option>
								<option value="ben">Bengali</option>
								<option value="bul">Bulgarian</option>
								<option value="cat">Catalan</option>
								<option value="chr">Cherokee</option>
								<option value="chi_sim">Chinese</option>
								<option value="hrv">Croatian</option>
								<option value="ces">Czech</option>
								<option value="dan">Danish</option>
								<option value="nld">Dutch</option>

								<option value="enm">English (Old)</option>
								<option value="epo">Esperanto</option>
								<option value="est">Estonian</option>
								<option value="fin">Finnish</option>
								<option value="frk">Frankish</option>
								<option value="fra">French</option>
								<option value="frm">French (Old)</option>
								<option value="glg">Galician</option>
								<option value="deu">German</option>
								<option value="ell">Greek</option>
								<option value="heb">Hebrew</option>
								<option value="hin">Hindi</option>
								<option value="hun">Hungarian</option>
								<option value="isl">Icelandic</option>
								<option value="ind">Indonesian</option>
								<option value="ita">Italian</option>
								<option value="jpn">Japanese</option>
								<option value="kan">Kannada</option>
								<option value="kor">Korean</option>
								<option value="lav">Latvian</option>
								<option value="lit">Lithuanian</option>
								<option value="mkd">Macedonian</option>
								<option value="msa">Malay</option>
								<option value="mal">Malayalam</option>
								<option value="mlt">Maltese</option>
								<option value="equ">Math</option>
								<option value="nor">Norwegian</option>
								<option value="spa_old">Old Spanish</option>
								<option value="fas">Persian (Farsi)</option>
								<option value="pol">Polish</option>
								<option value="por">Portuguese</option>
								<option value="ron">Romanian</option>
								<option value="rus">Russian</option>
								<option value="srp">Serbian</option>
								<option value="slk">Slovakian</option>
								<option value="slv">Slovenian</option>
								<option value="swa">Swahili</option>
								<option value="swe">Swedish</option>
								<option value="tgl">Tagalog</option>
								<option value="tam">Tamil</option>
								<option value="tel">Telugu</option>
								<option value="tha">Thai</option>
								<option value="chi_tra">Traditional Chinese</option>
								<option value="tur">Turkish</option>
								<option value="ukr">Ukrainian</option>
								<option value="vie">Vietnamese</option>
					</select>
				</div>
				<div class="recognize-settings-block">
					<span class="recognize-settings-name">Output formats:</span>
					<ul class="output-formats-btns">
						<li class="output-btn output-btn-active" data-val="pdf">Searchable PDF</li>
						<li class="output-btn" data-val="text">Text file</li>
					</ul>
				</div>
			</div>
            <div class="more-options-btns-wrap">				
                <button class="options-btn" type="button" id="start_task">Recognize text on all pages</button>
            </div>
        </form>
    </section>
    
    
    	@include ('inc.result_block')
    
@endsection
