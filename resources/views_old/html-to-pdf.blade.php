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
                        <div class="textarea-name">Web addresses:</div>
                        <textarea id="urls" placeholder="Paste your URLs here, one per line..."></textarea>
                        <p>Add multiple URLs, one per line.</p>
                        <div class="html-to-pdf-more-options">
                            <div class="half-row">
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Page size:</div>
                                        <div class="select-style">
                                            <select class="form-control pageSize" name="pageSize">
                                                <option selected="selected" value="long">One long page</option>
                                                <option disabled="disabled">----------------</option>
                                                <option value="a4">A4</option>
                                                <option value="a3">A3</option>
                                                <option value="a2">A2</option>
                                                <option disabled="disabled">-----------------</option>
                                                <option value="letter">Letter</option>
                                                <option value="legal">Legal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Viewport width:</div>
                                        <label>
                                            <input type="number" class="more-option-number viewportWidth"
                                                   placeholder="auto"
                                                   name="viewportWidth">
                                            px
                                        </label>
                                        <span class="input-help tooltip">
										<span class="tooltiptext">Text is converted to shades of gray.</span>
										<i class="far fa-question-circle"></i>
									</span>
                                    </div>
                                </div>
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Page orientation:</div>
                                        <div class="select-style">
                                            <select class="form-control pageOrientation" name="pageOrientation">
                                                <option selected="selected" value="auto">Auto</option>
                                                <option disabled="disabled">---------</option>
                                                <option value="portrait">Portrait</option>
                                                <option value="landscape">Landscape</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Margin:</div>
                                        <div class="number-select-group">
                                            <label>
                                                <input type="number" class="more-option-number pageMargin"
                                                       placeholder="0"
                                                       name="pageMargin">
                                            </label>
                                            <div class="select-style">
                                                <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                    <option value="px">Pixels</option>
                                                    <option value="in">Inch</option>
                                                    <option value="cm">Centimeter</option>
                                                    <option value="mm">Millimeter</option>
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
                                    Automatically hide cookie notices
                                </label>
                            </div>
                        </div>
                        <div class="pdf-form-btns">
                            <a class="button-green" id="HTMLToPDF" href="#">Convert HTML to PDF</a>
                            <a class="more-options html-pdf-options-btn" href="#">More options</a>
                        </div>
                        <p>{!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Free service for 20 links per task and 3 tasks per hour.' !!}</p>
                    </div>
                </div>
                <div class="tab-block" style="display:block;">
                    <div class="upload-top-info">
                        <div class="app-welcome">
                            <form action="#" id="drop_zone">
                                <div class="upload-img">
                                    <img src="img/pdf-img.svg" alt="">
                                </div>
                                <h3>Convert
                                    <strong>HTML</strong>
                                </h3>
                                <div class="upload-btn-wrap">
                                    <div class="upload-button">
    									<span>
    										Upload HTML file
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
                                                <img class="icon" src="img/gdrive.png"
                                                     alt="">
                                                Google Drive
                                            </a>
                                        </li>
                                        <li>
                                            <a class="weburl-chooser" href="#">
                                                <i class="fas fa-link icon"></i>
                                                Web Address
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
                        <div id="html-file" style="display: none;">
                            <div class="html-to-pdf-more-options">
                                <div class="half-row">
                                    <div class="half-col">
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">Page size:</div>
                                            <div class="select-style">
                                                <select class="form-control pageSize" name="pageSize">
                                                    <option selected="selected" value="long">One long page</option>
                                                    <option disabled="disabled">----------------</option>
                                                    <option value="a4">A4</option>
                                                    <option value="a3">A3</option>
                                                    <option value="a2">A2</option>
                                                    <option disabled="disabled">-----------------</option>
                                                    <option value="letter">Letter</option>
                                                    <option value="legal">Legal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">Viewport width:</div>
                                            <label>
                                                <input type="number" class="more-option-number viewportWidth"
                                                       placeholder="auto"
                                                       name="viewportWidth">
                                                px
                                            </label>
                                            <span class="input-help tooltip">
										<span class="tooltiptext">Text is converted to shades of gray.</span>
										<i class="far fa-question-circle"></i>
									</span>
                                        </div>
                                    </div>
                                    <div class="half-col">
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">Page orientation:</div>
                                            <div class="select-style">
                                                <select class="form-control pageOrientation" name="pageOrientation">
                                                    <option selected="selected" value="auto">Auto</option>
                                                    <option disabled="disabled">---------</option>
                                                    <option value="portrait">Portrait</option>
                                                    <option value="landscape">Landscape</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="more-options-form-group">
                                            <div class="more-options-label">Margin:</div>
                                            <div class="number-select-group">
                                                <label>
                                                    <input type="number" class="more-option-number pageMargin"
                                                           placeholder="0"
                                                           name="pageMargin">
                                                </label>
                                                <div class="select-style">
                                                    <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                        <option value="px">Pixels</option>
                                                        <option value="in">Inch</option>
                                                        <option value="cm">Centimeter</option>
                                                        <option value="mm">Millimeter</option>
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
                                        Automatically hide cookie notices
                                    </label>
                                </div>
                            </div>
                            <div class="pdf-form-btns">
                                <a class="button-green" id="HTMLFileToPDF" href="#">Convert HTML to PDF</a>
                                <a class="more-options html-pdf-options-btn" href="#">More options</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-block">
                    <div class="pdf-form">
                        <div class="textarea-name">HTML code:</div>
                        <textarea id="code" placeholder="Paste your HTML code here..."></textarea>
                        <p>Add multiple URLs, one per line.</p>
                        <div class="html-to-pdf-more-options">
                            <div class="half-row">
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Page size:</div>
                                        <div class="select-style">
                                            <select class="form-control pageSize" name="pageSize">
                                                <option selected="selected" value="long">One long page</option>
                                                <option disabled="disabled">----------------</option>
                                                <option value="a4">A4</option>
                                                <option value="a3">A3</option>
                                                <option value="a2">A2</option>
                                                <option disabled="disabled">-----------------</option>
                                                <option value="letter">Letter</option>
                                                <option value="legal">Legal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Viewport width:</div>
                                        <label>
                                            <input type="number" class="more-option-number viewportWidth"
                                                   placeholder="auto"
                                                   name="viewportWidth">
                                            px
                                        </label>
                                        <span class="input-help tooltip">
										<span class="tooltiptext">Text is converted to shades of gray.</span>
										<i class="far fa-question-circle"></i>
									</span>
                                    </div>
                                </div>
                                <div class="half-col">
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Page orientation:</div>
                                        <div class="select-style">
                                            <select class="form-control pageOrientation" name="pageOrientation">
                                                <option selected="selected" value="auto">Auto</option>
                                                <option disabled="disabled">---------</option>
                                                <option value="portrait">Portrait</option>
                                                <option value="landscape">Landscape</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="more-options-form-group">
                                        <div class="more-options-label">Margin:</div>
                                        <div class="number-select-group">
                                            <label>
                                                <input type="number" class="more-option-number pageMargin"
                                                       placeholder="0"
                                                       name="pageMargin">
                                            </label>
                                            <div class="select-style">
                                                <select class="form-control pageMarginUnits" name="pageMarginUnits">
                                                    <option value="px">Pixels</option>
                                                    <option value="in">Inch</option>
                                                    <option value="cm">Centimeter</option>
                                                    <option value="mm">Millimeter</option>
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
                                    Automatically hide cookie notices
                                </label>
                            </div>
                        </div>
                        <div class="pdf-form-btns">
                            <a class="button-green" id="HTMLCodeToPDF" href="#">Convert HTML to PDF</a>
                            <a class="more-options html-pdf-options-btn" href="#">More options</a>
                        </div>
                        <p>{!! array_key_exists(8, $PageBlocks) ? $PageBlocks[8] : 'Free service for 20 links per task and 3 tasks per hour.' !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="how-it-works">
        <div class="title-section">
            <h2>How To Convert HTML To PDF</h2>
        </div>
        <div class="container centered">
            <p class="title-description">Below we show how to convert web pages to PDF documents</p>
            <div class="post">
                <h3>Step 1: Paste your web page URLs</h3>
                <p>
                    Multiple web pages can be converted at a time. Paste each URL on a separate line.
                </p>

                <h3>Step 2: Save PDF results</h3>
                <p>
                    Click
                    <strong>Convert HTML to PDF</strong>
                    and wait until processing completes. Then press
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
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>Convert
                        <strong>HTML</strong>
                    </h3>
                    <div class="upload-btn-wrap">
                        <div class="upload-button">
    						<span>
    							Convert HTML to PDF
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
                                    <img class="icon" src="img/gdrive.png" alt="">
                                    Google
                                    Drive
                                </a>
                            </li>
                            <li>
                                <a class="weburl-chooser" href="#">
                                    <i class="fas fa-link icon"></i>
                                    Web Address (URL)
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
            <h2>How To Add A 'Save To PDF' Link To Your Web Page</h2>
        </div>
        <div class="container centered">
            <p class="title-description">Let your visitors save web pages to PDF</p>
            <div class="post">
                <h3>Simple as Copy & Paste</h3>
                <p>
                    Copy and paste the HTML code below and add it to your web page:
                </p>
                <div class="copy-and-paste">
                    <textarea><a href="https://deftpdf.com/html-to-pdf?save-link"
                                 target="_blank">Save to PDF</a></textarea>
                    <div class="copy-btns">
                        <a class="button-green" href="#">Copy</a>
                        <a class="button-green click-btn" href="#">Click to try</a>
                    </div>
                </div>
                <a class="read-more-link" href="#">Read more in the docs</a>
                <div class="contact-btn">
                    <a class="button-green contact-btn-popup" href="#contactFormModal">
                        <span>Contact Support</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include ('inc.result_block')
@endsection
