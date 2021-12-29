@extends('layouts.layout')
@section('content')
<link rel="stylesheet" href="https://pdf2.cgp.systems/libs/jquery-minicolors/jquery.minicolors.css">
<div class="upload-top-info before_upload">
	<div class="container">
		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Bates numbering of PDF files' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Bates stamp multiple files at once' !!}</p>
			</div>
		</div>
		<div class="app-welcome">
			<form action="#" id="drop_zone">
				<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">
				<div class="upload-img">
					<img src="/img/pdf-img.svg" alt="">
				</div>
                {!! t('<h3>UPLOAD <strong>PDF</strong> FILE</h3>') !!}
				@include('inc.uploadButton')
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

<section class="bates-numbering-pdf after_upload hidden" id="bates_section">
	<div class="container">

		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Bates numbering of PDF files' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Bates stamp multiple files at once' !!}</p>
			</div>
		</div>


		<div class="file-name-pdf">{!! t('Selected:') !!} <span class='file_name_here'>Cut sheet 1.18.pdf</span></div>
		<form action="#" class="fixed-task-form">
			<div class="header-footer-content">
				<div class="hf-location-page">
					<div class="numbering-label"><span class="badge">1</span><span class="header-footer-label">{!! t('Select Style') !!}</span></div>
					<select name="bates_type" class="form-control">
						<option class="bates-select-option" selected="selected" value="bates-with-exhibit">Exhibit 1 Case XYZ 000001</option>
						<option class="bates-select-option" value="full-bates">Case XYZ 000001</option>
						<option disabled="disabled" data-divider="true"></option>
						<option class="bates-select-option" value="bates-with-exhibit-3-digits">Exhibit 1 Case XYZ 001</option>
						<option class="bates-select-option" value="full-bates-3-digits">Case XYZ 001</option>
						<option disabled="disabled" data-divider="true"></option>
						<option class="bates-select-option" value="just-number">000001, 000002, 000003</option>
						<option class="bates-select-option" value="just-number-3-digits">001, 002, 003</option>
						<option disabled="disabled" data-divider="true"></option>
						<option class="bates-select-option" value="bates-custom">{!! t('Custom style (other)') !!}</option>
					</select>
				</div>
				<div class="numbering-label"><span class="badge">2</span><span class="header-footer-label">{!! t('Customize Style') !!}</span></div>
				<div class="bates-dropdown">
					<div data-id="bates-with-exhibit" class="bates-format-customize">
						<div class="input-group">
							<input value="Exhibit" name="bates-with-exhibit_exhibit" class="form-control exhb_input" type="text">
							<div class="input-group-addon">1</div>
							<input value="[FILE_NUMBER]" type="hidden">
							<input value="Case XYZ" name="bates-with-exhibit_case" class="form-control bates_inp" type="text">
							<div class="input-group-addon">000001</div>
							<input value="[BATES_NUMBER]" type="hidden">
						</div>
					</div>
					<div data-id="full-bates" class="bates-format-customize">
						<div class="input-group">
							<input value="Case XYZ" name="full-bates_case" class="form-control bates_inp" type="text">
							<div class="input-group-addon">000001</div>
							<input value="[BATES_NUMBER]" type="hidden">
						</div>
					</div>
					<div data-id="bates-with-exhibit-3-digits" class="bates-format-customize">
						<div class="input-group">
							<input value="Exhibit" name="bates-with-exhibit-3-digits_exhibit" class="form-control exhb_input" type="text">
							<div class="input-group-addon">1</div>
							<input value="[FILE_NUMBER]" type="hidden">
							<input value="Case XYZ" name="bates-with-exhibit-3-digits_case" class="form-control bates_inp" type="text">
							<div class="input-group-addon">001</div>
							<input value="[BATES_NUMBER]" type="hidden">
						</div>
					</div>
					<div data-id="full-bates-3-digits" class="bates-format-customize">
						<div class="input-group">
							<input value="Case XYZ" name="full-bates-3-digits_case" class="form-control bates_inp" type="text">
							<div class="input-group-addon">001</div>
							<input value="[BATES_NUMBER]" type="hidden">
						</div>
					</div>
					<div data-id="just-number" class="bates-format-customize">
						<div class="input-group">
							<input value="[BATES_NUMBER]" type="hidden" >
						</div>
					</div>
					<div data-id="just-number-3-digits" class="bates-format-customize">
						<div class="input-group">
							<input value="[BATES_NUMBER]" type="hidden">
						</div>
					</div>
					<div data-id="bates-custom" class="bates-format-customize">
						<input value="Exhibit [FILE_NUMBER] Case XYZ [BATES_NUMBER]" id="exhb_custom" name="bates-custom_text" class="form-control" type="text">
					</div>
				</div>
				<div class="bates-preview">
					{!! t('Preview:') !!}<br>
					{!! t('File 1, Page 1:') !!} <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 000001<br>
					{!! t('File1, Page 2:') !!} <span class='exhb_val'>Exhibit</span> 1 <span class='bates_val'>Case XYZ</span> 000002<br>
					...<br>
					{!! t('File 10, Page 1:') !!} <span class='exhb_val'>Exhibit</span> 10 <span class='bates_val'>Case XYZ</span> 000123
				</div>
				<div class="hf-location-page">
					<span class="header-footer-label">{!! t('Location on page') !!}</span>
					<select name="pageLocation" class="form-control">
						<option value="hleft">{!! t('Header left') !!}</option>
						<option selected="" value="hcenter">{!! t('Header center') !!}</option>
						<option value="hright">{!! t('Header right') !!}</option>
						<option value="fleft">{!! t('Footer left') !!}</option>
						<option value="fcenter">{!! t('Footer center') !!}</option>
						<option value="fright">{!! t('Footer right') !!}</option>
					</select>
				</div>
				<span class="header-footer-label">{!! t('Numbering (Continue from)') !!}</span>
				<div class="numbering-container">
					<div class="input-numbering">
						<label>{!! t('Bates sequence starts from:') !!}</label>
						<input type="text" name="bates_start_from" placeholder="1">
						<span class="input-help tooltip">
						<span class="tooltiptext">{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Use this to continue a previous Bates stamping. Eg: previous numbering stopped at sequence 1001, continue now with 1002.' !!}</span>
						<i class="far fa-question-circle"></i>
						</span>
					</div>
					<div class="input-numbering">
						<label>{!! t('Files counter starts from:') !!}</label>
						<input type="text" name="file_start_from" placeholder="1">
						<span class="input-help tooltip">
						<span class="tooltiptext">{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Relevant only when the file/exhibit counter is also used, besides the Bates sequence. Use this to continue a previous Bates stamping. Eg: Type the number of files already stamped.' !!}</span>
						<i class="far fa-question-circle"></i>
						</span>
					</div>
				</div>
				<span class="header-footer-label">{!! t('Document Margins') !!}</span>
				<div class="image-radio-item">
					<div class="btns-resolution">
						<label class="resolution-item">
						<input type="radio" name="addMargins" id="format4" checked="checked" value="leave">
						<span class="resolution-item-checkmark">
						{!! t('Leave unchanged') !!}
						</span>
						</label>
						<label class="resolution-item">
						<input type="radio" name="addMargins" id="format5" value="increase">
						<span class="resolution-item-checkmark">
						{!! t('Increase margins') !!}
						<span class="input-help tooltip">
						<span class="tooltiptext">{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Increase document margins to avoid overlapping existing text.' !!}</span>
						<i class="far fa-question-circle"></i>
						</span>
						</span>
						</label>
					</div>
				</div>
				<div class="more-options-box">
					<span class="header-footer-label">{!! t('Font') !!}</span>   
					<div class="image-radio-item">
						<div class="btns-resolution">
							<label class="resolution-item times-new-roman">
								<input type="radio" name="format" id="format1" checked="checked" value="timesnewroman2">
								<span class="resolution-item-checkmark">
									Times New Roman
								</span>
							</label>
							<label class="resolution-item helvetica">
								<input type="radio" name="format" id="format2" value="helvetica2">
								<span class="resolution-item-checkmark">
									Helvetica
								</span>
							</label>
							<label class="resolution-item courier">
								<input type="radio" name="format" id="format3" value="courier2">
								<span class="resolution-item-checkmark">
									Courier
								</span>
							</label>
						</div>
						<div class="fz-mc-group">
							<select name="fontSize" id="font_size" class="select-font-size">
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10" selected="selected">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="18">18</option>
								<option value="20">20</option>
								<option value="25">25</option>
							</select>
							<div class="minicolors">
								<input type="hidden" id="text_color_input" class="minicolors-input" value="#000000" size="7">
							</div>
						</div>
					</div>
					<div class="head-space text-center">
						<label>{!! t('Customize result names') !!}</label>
						<div class="input-group">
							<input name="outputFilenamePattern" type="text" class="form-control" placeholder="[FILENUMBER]-[BASENAME]" value="bates_stamped_">
							<div class="input-group-addon">
								<a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
							</div>
						</div>
					</div>
				</div>
				<p class="help-block text-center">
					{!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'Always keep a backup of your original files.' !!}
					<span class="input-help tooltip">
					<span class="tooltiptext">{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'If you will later need to start over and renumber your files, this can only be done using the originals.' !!}</span>
					<i class="far fa-question-circle"></i>
					</span>
				</p>
				<div class="more-options-btns-wrap">
					<button class="options-btn" type="button" id="start_task">{!! t('Bates stamp') !!}</button>
					<a href="#" class="options-btn-transparent">{!! t('More options') !!}</a>
				</div>
			</div>
		</form>
	</div>
</section>




@if (count($PageGuides))
<section class="how-it-works">
	@foreach ($PageGuides as $Guide)
	@if (!is_null($Guide->title))
	<div class="title-section">
		<h2>{{ $Guide->title }}</h2>
	</div>
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
<section class="how-it-works">
	<div class="contact-btn">
		<a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
	</div>
</section>




	@include ('inc.result_block_new')


@endsection

