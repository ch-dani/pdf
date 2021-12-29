@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'HTML to PDF<sup>BETA</sup>' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert web pages or HTML files to PDF documents' !!}</p>
                    <div class="new-container">

                        @if(array_key_exists(12, $PageBlocks))
                        <a href="#" class="new-block">
                            <p>{!!  $PageBlocks[12] !!}</p>
                        </a>
                       	@endif
                    
                        @if(array_key_exists(13, $PageBlocks))
                        <a href="#" class="new-block">
                            <p>{!!  $PageBlocks[13] !!}</p>
                        </a>
                       	@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container centered">
        <div class="pdf-tab">
            <div class="tab-btns">
                <div class="tab-btn-block">{!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Convert URL to PDF' !!}</div>
                <div class="tab-btn-block tab-active-btn">{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Convert HTML files' !!}</div>
                <div class="tab-btn-block">{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Convert HTML code' !!}</div>
            </div>
            <div class="tab-container">
                <div class="tab-block">
                    <div class="pdf-form">
                        <div class="textarea-name">{!! t('Web addresses:') !!}</div>
                        <textarea id="urls" placeholder="Paste your URLs here, one per line..."></textarea>
                        <p>{!! array_key_exists(14, $PageBlocks) ? $PageBlocks[14] : 'Add multiple URLs, one per line.' !!}</p>
                        <div class="html-to-pdf-more-options">
                            <div class="half-row">
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Page size:') !!}</div>
                                        <div class="select-style">
                                            <select class="form-control pageSize" name="pageSize">
                                                <option selected="selected" value="long">{!! t('One long page') !!}</option>
                                                <option disabled="disabled">----------------</option>
                                                <option value="a4">A4</option>
                                                <option value="a3">A3</option>
                                                <option value="a2">A2</option>
                                                <option disabled="disabled">-----------------</option>
                                                <option value="letter">{!! t('Letter') !!}</option>
                                                <option value="legal">{!! t('Legal') !!}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Viewport width:') !!}</div>
                                        <label>
                                            <input type="number" class="more-option-number viewportWidth"
                                                   placeholder="auto"
                                                   name="viewportWidth">
                                            px
                                        </label>
                                        <span class="input-help tooltip">
										<span class="tooltiptext">{!! array_key_exists(15, $PageBlocks) ? $PageBlocks[15] : 'Text is converted to shades of gray.' !!}</span>
										<i class="far fa-question-circle"></i>
									</span>
                                    </div>
                                </div>
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Page orientation:') !!}</div>
                                        <div class="select-style">
                                            <select class="form-control pageOrientation" name="pageOrientation">
                                                <option selected="selected" value="auto">{!! t('Auto') !!}</option>
                                                <option disabled="disabled">---------</option>
                                                <option value="portrait">{!! t('Portrait') !!}</option>
                                                <option value="landscape">{!! t('Landscape') !!}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Margin:') !!}</div>
                                        <div class="number-select-group">
                                            <label>
                                                <input type="number" class="more-option-number pageMargin"
                                                       placeholder="0"
                                                       name="pageMargin">
                                            </label>
                                            <div class="select-style">
                                                <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                    <option value="px">{!! t('Pixels') !!}</option>
                                                    <option value="in">{!! t('Inch') !!}</option>
                                                    <option value="cm">{!! t('Centimeter') !!}</option>
                                                    <option value="mm">{!! t('Millimeter') !!}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input value="true" class="hideNotices" name="hideNotices" checked="checked"
                                           type="checkbox">
                                   {!! array_key_exists(16, $PageBlocks) ? $PageBlocks[16] : 'Automatically hide cookie notices' !!}
                                </label>
                            </div>
                        </div>
                        <div class="pdf-form-btns">
                            <a class="button-green" id="HTMLToPDF" href="#">{!! array_key_exists(17, $PageBlocks) ? $PageBlocks[17] : 'Convert HTML to PDF' !!}</a>
                            <a class="more-options html-pdf-options-btn" href="#">{!! t('More options') !!}</a>
                        </div>
                        <p>{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Free service for 20 links per task and 3 tasks per hour.' !!}</p>
                    </div>
                </div>
                <div class="tab-block" style="display:block;">
                    <div class="upload-top-info">

						<div class="welcome_outer">
							@if($ads && $device_is=='computer')
								@include("ads.adx250x250")
							@endif

		                    <div class="app-welcome">
		                        <form action="#" id="drop_zone">
		                            <div class="upload-img">
		                                <img src="/img/pdf-img.svg" alt="">
		                            </div>
		                            <h3>{!! t('Convert') !!}
		                                <strong>{!! t('HTML') !!}</strong>
		                            </h3>
		                            <div class="upload-btn-wrap">
		                                <div class="upload-button">
											<span>
												{!! t('Upload HTML file') !!}
											</span>
		                                    <input type="file" class="upload-file-tool" accept=".html">
		                                </div>
		                                <button class="dropdown-toggle-btn" type="button">
		                                    <i class="fas fa-caret-down"></i>
		                                </button>
		                                <ul class="dropdown-menu-upload">
		                                    <li>
		                                        <a class="drpbox-chooser" href="#">
		                                            <i class="fab fa-dropbox icon"></i>
		                                            Dropbox
		                                        </a>
		                                    </li>
		                                    <li>
		                                        <a id="gdrive-chooser" class="gdrive-chooser" href="#">
		                                            <img class="icon" src="/img/gdrive.png"
		                                                 alt="">
		                                            Google Drive
		                                        </a>
		                                    </li>
		                                    <li>
		                                        <a class="weburl-chooser" href="#">
		                                            <i class="fas fa-link icon"></i>
		                                            {!! t('Web Address') !!}
		                                            (URL)
		                                        </a>
		                                    </li>
		                                </ul>
		                            </div>
		                        </form>
		                        <div class="upload-welcom-descr">
		                            {!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
		                        </div>
		                    </div>
							@if($ads && $device_is=='computer')
								@include("ads.adx250x250")
							@endif		                    
		                    
		                    
		                </div>


						@if($ads && $device_is=='computer')
							@include("ads.adx970x90")
						@endif

						@if($ads && $device_is=='phone')
							@include("ads.adx320x100")
						@endif  

		                    
                        <div id="html-file" style="display: none;">
                            <div class="html-to-pdf-more-options">
                                <div class="half-row">
                                    <div class="half-col">
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">{!! t('Page size:') !!}</div>
                                            <div class="select-style">
                                                <select class="form-control pageSize" name="pageSize">
                                                    <option selected="selected" value="long">{!! t('One long page') !!}</option>
                                                    <option disabled="disabled">----------------</option>
                                                    <option value="a4">A4</option>
                                                    <option value="a3">A3</option>
                                                    <option value="a2">A2</option>
                                                    <option disabled="disabled">-----------------</option>
                                                    <option value="letter">{!! t('Letter') !!}</option>
                                                    <option value="legal">{!! t('Legal') !!}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">{!! t('Viewport width:') !!}</div>
                                            <label>
                                                <input type="number" class="more-option-number viewportWidth"
                                                       placeholder="auto"
                                                       name="viewportWidth">
                                                px
                                            </label>
                                            <span class="input-help tooltip">
										<span class="tooltiptext">{!! array_key_exists(15, $PageBlocks) ? $PageBlocks[15] : 'Text is converted to shades of gray.' !!}</span>
										<i class="far fa-question-circle"></i>
									</span>
                                        </div>
                                    </div>
                                    <div class="half-col">
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">{!! t('Page orientation:') !!}</div>
                                            <div class="select-style">
                                                <select class="form-control pageOrientation" name="pageOrientation">
                                                    <option selected="selected" value="auto">{!! t('Auto') !!}</option>
                                                    <option disabled="disabled">---------</option>
                                                    <option value="portrait">{!! t('Portrait') !!}</option>
                                                    <option value="landscape">{!! t('Landscape') !!}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">{!! t('Margin:') !!}</div>
                                            <div class="number-select-group">
                                                <label>
                                                    <input type="number" class="more-option-number pageMargin"
                                                           placeholder="0"
                                                           name="pageMargin">
                                                </label>
                                                <div class="select-style">
                                                    <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                        <option value="px">{!! t('Pixels') !!}</option>
                                                        <option value="in">{!! t('Inch') !!}</option>
                                                        <option value="cm">{!! t('Centimeter') !!}</option>
                                                        <option value="mm">{!! t('Millimeter') !!}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input value="true" class="hideNotices" name="hideNotices" checked="checked"
                                               type="checkbox">
                                        {!! array_key_exists(16, $PageBlocks) ? $PageBlocks[16] : 'Automatically hide cookie notices' !!}
                                    </label>
                                </div>
                            </div>
                            <div class="pdf-form-btns">
                                <a class="button-green" id="HTMLFileToPDF" href="#">{!! t('Convert HTML to PDF') !!}</a>
                                <a class="more-options html-pdf-options-btn" href="#">{!! t('More options') !!}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-block">
                    <div class="pdf-form">
                        <div class="textarea-name">{!! t('HTML code:') !!}</div>
                        <textarea id="code" placeholder="{!! t('Paste your HTML code here...') !!}"></textarea>
                        <p>{!! array_key_exists(14, $PageBlocks) ? $PageBlocks[14] : 'Add multiple URLs, one per line.' !!}</p>
                        <div class="html-to-pdf-more-options">
                            <div class="half-row">
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Page size:') !!}</div>
                                        <div class="select-style">
                                            <select class="form-control pageSize" name="pageSize">
                                                <option selected="selected" value="long">{!! t('One long page') !!}</option>
                                                <option disabled="disabled">----------------</option>
                                                <option value="a4">A4</option>
                                                <option value="a3">A3</option>
                                                <option value="a2">A2</option>
                                                <option disabled="disabled">-----------------</option>
                                                <option value="letter">{!! t('Letter') !!}</option>
                                                <option value="legal">{!! t('Legal') !!}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Viewport width:') !!}</div>
                                        <label>
                                            <input type="number" class="more-option-number viewportWidth"
                                                   placeholder="auto"
                                                   name="viewportWidth">
                                            px
                                        </label>
                                        <span class="input-help tooltip">
										<span class="tooltiptext">{!! array_key_exists(15, $PageBlocks) ? $PageBlocks[15] : 'Text is converted to shades of gray.' !!}</span>
										<i class="far fa-question-circle"></i>
									</span>
                                    </div>
                                </div>
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Page orientation:') !!}</div>
                                        <div class="select-style">
                                            <select class="form-control pageOrientation" name="pageOrientation">
                                                <option selected="selected" value="auto">{!! t('Auto') !!}</option>
                                                <option disabled="disabled">---------</option>
                                                <option value="portrait">{!! t('Portrait') !!}</option>
                                                <option value="landscape">{!! t('Landscape') !!}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">{!! t('Margin') !!}</div>
                                        <div class="number-select-group">
                                            <label>
                                                <input type="number" class="more-option-number pageMargin"
                                                       placeholder="0"
                                                       name="pageMargin">
                                            </label>
                                            <div class="select-style">
                                                <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                    <option value="px">{!! t('Pixels') !!}</option>
                                                    <option value="in">{!! t('Inch') !!}</option>
                                                    <option value="cm">{!! t('Centimeter') !!}</option>
                                                    <option value="mm">{!! t('Millimeter') !!}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input value="true" class="hideNotices" name="hideNotices" checked="checked"
                                           type="checkbox">
                                    {!! array_key_exists(16, $PageBlocks) ? $PageBlocks[16] : 'Automatically hide cookie notices' !!}
                                </label>
                            </div>
                        </div>
                        <div class="pdf-form-btns">
                            <a class="button-green" id="HTMLCodeToPDF" href="#">{!! t('Convert HTML to PDF') !!}</a>
                            <a class="more-options html-pdf-options-btn" href="#">{!! t('More options') !!}</a>
                        </div>
                        <p>{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'Free service for 20 links per task and 3 tasks per hour.' !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="how-it-works">
        <div class="title-section">
            <h2>{!! array_key_exists(18, $PageBlocks) ? $PageBlocks[18] : 'How To Convert HTML To PDF' !!}</h2>
        </div>
        <div class="container centered">
            <p class="title-description">{!! array_key_exists(19, $PageBlocks) ? $PageBlocks[19] : 'Below we show how to convert web pages to PDF documents' !!}</p>
            <div class="post">
                
				<h3>{!! array_key_exists(20, $PageBlocks) ? $PageBlocks[20] : 'Step 1: Paste your web page URLs' !!}</h3>
                <p>{!! array_key_exists(21, $PageBlocks) ? $PageBlocks[21] : 'Multiple web pages can be converted at a time. Paste each URL on a separate line.' !!}</p>

                <h3>{!! array_key_exists(22, $PageBlocks) ? $PageBlocks[22] : 'Step 2: Save PDF results' !!}</h3>
                <p>Click<strong>Convert HTML to PDF</strong>and wait until processing completes. Then press
                    <strong>Download</strong>
                    and save your PDF documents.
                </p>
            </div>
        </div>
    </section>


    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(9, $PageBlocks) ? $PageBlocks[9] : 'Ready to convert web pages to PDF? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(10, $PageBlocks) ? $PageBlocks[10] : 'Convert web pages or HTML files to PDF documents' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="/img/pdf-img.svg" alt="">
                    </div>
                    <h3>Convert
                        <strong>HTML</strong>
                    </h3>
                    <div class="upload-btn-wrap">
                        <div class="upload-button">
    						<span>
    							{!! array_key_exists(17, $PageBlocks) ? $PageBlocks[17] : 'Convert HTML to PDF' !!}
    						</span>
                            <input type="file">
                        </div>
                        <button class="dropdown-toggle-btn" type="button">
                            <i class="fas fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu-upload">
                            <li>
                                <a class="drpbox-chooser" href="#">
                                    <i class="fab fa-dropbox icon"></i>
                                    Dropbox
                                </a>
                            </li>
                            <li>
                                <a class="gdrive-chooser" href="#">
                                    <img class="icon" src="/img/gdrive.png" alt="">
                                    Google
                                    Drive
                                </a>
                            </li>
                            <li>
                                <a class="weburl-chooser" href="#">
                                    <i class="fas fa-link icon"></i>
                                    {!! t('Web Address') !!} (URL)
                                </a>
                            </li>
                        </ul>
                    </div>
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(11, $PageBlocks) ? $PageBlocks[11] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>
    <section class="how-it-works">
        <div class="title-section">
            <h2>{!! array_key_exists(23, $PageBlocks) ? $PageBlocks[23] : "How To Add A 'Save To PDF' Link To Your Web Page" !!}</h2>
        </div>
        <div class="container centered">
            <p class="title-description">{!! array_key_exists(24, $PageBlocks) ? $PageBlocks[24] : 'Let your visitors save web pages to PDF' !!}</p>
            <div class="post">
                <h3>{!! t('Simple as Copy & Paste') !!}</h3> 
                <p>
                  {!! array_key_exists(25, $PageBlocks) ? $PageBlocks[25] : 'Copy and paste the HTML code below and add it to your web page:' !!}  
                </p>
                <div class="copy-and-paste">
                    <textarea><a href="https://deftpdf.com/html-to-pdf?save-link"
                                 target="_blank">{!! t('Save to PDF') !!}</a></textarea>
                    <div class="copy-btns">
                        <a class="button-green" href="#">{!! t('Copy') !!}</a>
                        <a class="button-green click-btn" href="#">{!! t('Click to try') !!}</a>
                    </div>
                </div>
                <a class="read-more-link" href="#">{!! t('Read more in the docs') !!}</a>
                <div class="contact-btn">
                    <a class="button-green contact-btn-popup" href="#contactFormModal">
                        <span>{!! t('Contact Support') !!}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include ('inc.result_block_new')
@endsection
