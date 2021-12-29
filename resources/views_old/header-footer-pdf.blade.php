@extends('layouts.layout')

@section('content')

<link rel="stylesheet" href="https://pdf2.cgp.systems/libs/jquery-minicolors/jquery.minicolors.css">

<div class="upload-top-info before_upload">
	<div class="container">
		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Add PDF Header & Footer' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Apply page numbers or text labels to PDF files' !!}</p>
			</div>
		</div>
		<div class="app-welcome">
			<form action="#" id="drop_zone">
            	<input type="hidden" value="<?php echo csrf_token() ?>" id="editor_csrf">    			    			
				<div class="upload-img">
					<img src="img/pdf-img.svg" alt="">
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


<section class="header-footer-pdf hidden after_upload" id="header_footer_section">
	<div class="container">

		<div class="app-title">
			<div class="wrapper">
				<h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Add PDF Header & Footer' !!}</h1>
				<p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Apply page numbers or text labels to PDF files' !!}</p>
			</div>
		</div>
	
		<div class="file-name-pdf">Selected: <span class="file_name_here"></span></div>
		<form action="#" class="fixed-task-form">
			<div class="header-footer-content">
				<span class="header-footer-label">Customize Style</span>
				<div class="header-footer-dropdown">
					<div data-id="hf-pages-arabic" class="hf-format-customize">
						<div class="input-group">
							<input value="Page" name="header_text" class="form-control" type="text">
							<div class="input-group-addon">1</div>
							<input value="[PAGE_ARABIC]" name="header_text" type="hidden">
						</div>
					</div>
					<div data-id="hf-pages-roman" class="hf-format-customize">
						<div class="input-group">
							<input name="header_text" value="Page" class="form-control" type="text">
							<div class="input-group-addon">VII</div>
							<input value="[PAGE_ROMAN]" name="header_text" type="hidden">
						</div>
					</div>
					<div id="hf-page-of-total" data-id="hf-page-of-total" class="hf-format-customize">
						<div class="input-group">
							<input name="header_text" value="Page" class="form-control" type="text">
							<div class="input-group-addon">2</div>
							<input value="[PAGE_ARABIC]" name="header_text" type="hidden">
							<input name="header_text2" value="of" class="form-control" type="text">
							<div class="input-group-addon">45</div>
							<input value="[TOTAL_PAGES_ARABIC]" name="header_text" type="hidden">
						</div>
					</div>
					<div id="hf-filename" data-id="hf-filename" class="hf-format-customize">
						<div class="input-group">
							<div class="input-group-addon">Filename</div>
							<input value="[BASE_NAME]" name="header_text" type="hidden">
						</div>
					</div>
					<div id="hf-text-only" data-id="hf-text-only" class="hf-format-customize">
						<input value="Draft version" class="form-control" name="header_text" type="text">
					</div>
					<button class="btn-dropdown-toggle">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu-right">
						<li><a class="dropdown-menu-item-customize" data-value="hf-pages-arabic" href="#" >Page 1, Page 2, Page 3</a></li>
						<li><a class="dropdown-menu-item-customize" data-value="hf-pages-roman" href="#" >Page I, Page II, Page III</a></li>
						<li><a class="dropdown-menu-item-customize" data-value="hf-page-of-total" href="#">Page 2 of 45</a></li>
						<li><a class="dropdown-menu-item-customize" data-value="hf-filename" href="#">Filename</a></li>
						<li><a class="dropdown-menu-item-customize" data-value="hf-text-only" href="#">Text only</a></li>
					</ul>
				</div>
				<div class="hf-dropdown-bottom-info">Preview: Page 1, Page 2, Page 3</div>	
				<div class="hf-location-page">
					<span class="header-footer-label">Location on page</span>
					<select name="pageLocation" id="ft_loacation" class="form-control">
						<option value="hleft">Header left</option>
						<option selected="" value="hcenter">Header center</option>
						<option value="hright">Header right</option>
						<option value="fleft">Footer left</option>
						<option value="fcenter">Footer center</option>
						<option value="fright">Footer right</option>
					</select>
				</div>
				<span class="header-footer-label">Font</span>	
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
				<span class="header-footer-label">Document Margins</span>
				<div class="image-radio-item">
					<div class="btns-resolution">
						<label class="resolution-item">
							<input type="radio" name="addMargins" id="format4" checked="checked">
							<span class="resolution-item-checkmark">
								Leave unchanged
							</span>
						</label>
						<label class="resolution-item">
							<input type="radio" name="addMargins" id="format5">
							<span class="resolution-item-checkmark">
								Increase margins
								<span class="input-help tooltip">
									<span class="tooltiptext">Increase document margins to avoid overlapping existing text.</span>
									<i class="far fa-question-circle"></i>
								</span>
							</span>
						</label>
					</div>
				</div>
				<div class="more-options-box">
					<div class="more-options-flex">
						<div class="head-space">
							<label>Apply only to pages</label>
							<div class="input-group">
								<input id="display_on_page" name="display_on_page" type="text" placeholder="Example: 1-10 or 12 or odd or even or 2,4,6,10" >
								<div class="input-group-addon">
									<a target="_blank" href="#"><i class="far fa-question-circle"></i></a>
								</div>
							</div>
						</div>
						<div class="input-numbering">
							<label>Start numbering from</label>
							<input id="start_from_page" type="text" name="start_from_page" placeholder="1">
							<span class="input-help tooltip">
								<span class="tooltiptext">Helpful, for example, when skipping over a 3 pages Introduction that's already numbered with romans.</span>
								<i class="far fa-question-circle"></i>
							</span>
						</div> 
					</div>
				</div>
				<div class="more-options-btns-wrap">
                    <button class="options-btn" type="button" id="start_task">Add header/footer</button>
                    <a href="#" class="options-btn-transparent">More options</a>
                </div>
			</div>
		</form>
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

<section class="how-it-works">

	<div class="contact-btn">
		<a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
	</div>

</section>

	@include ('inc.result_block')


@endsection

