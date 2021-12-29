@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info before_upload">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Merge PDF Files Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Combine multiple PDFs and images into one' !!}</p>
                    
			        @if(array_key_exists(7, $PageBlocks))
		                <a href="#" class="new-block merge-pdf-info">
		                	{!! $PageBlocks[7] !!}

		                </a>
                    @endif
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        OR
                        <strong>IMAGE</strong>
                        FILE
                    </h3>

                    @php
                        $accept = '.pdf,.png,.gif,.jpg,.jpeg,.tif,.tiff,.bmp';
                    @endphp
                    @include('includes.upload-button-multiple')

                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>
    <div class="merge-pdf after_upload" style="display: none;">
        <section class="s-marge-pdf-files">
            <div class="container">
                <div class="left-half">
                    <ul class="pdf-files-list-sortable ui-sortable" id="merge-pdf">

                    </ul>
                </div>
                <div class="right-half">
                    <div class="upload-btn-wrap">
                        <div class="upload-button">
                            <span>
                                <i class="far fa-copy"></i>Add more files
                            </span>
                            <input type="file" class="upload-file-tool" accept=".pdf,.png,.gif,.jpg,.jpeg,.tif,.tiff,.bmp" multiple>
                        </div>
                        <button class="dropdown-toggle-btn" type="button">
                            <i class="fas fa-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu-upload" style="display: none;">
                            <li>
                                <a class="drpbox-chooser" href="#">
                                    <i class="fab fa-dropbox icon"></i>
                                    Dropbox
                                </a>
                            </li>
                            <li>
                                <a class="gdrive-chooser" href="#">
                                    <img class="icon" src="img/gdrive.png" alt="">
                                    Google Drive
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="data-sort-order-items">
                        <a href="#" data-sort-order="asc" class="sort-btn">Sort A-Z</a>
                        <a href="#" data-sort-order="desc" class="sort-btn">Sort Z-A</a>
                    </div>
                </div>
            </div>
        </section>
    </div>


    @if (count($PageGuides))
        <section class="how-it-works">
            @foreach ($PageGuides as $Guide)
                @if (!is_null($Guide->title))
                    <div class="title-section">
                        <h2>{{ $Guide->title }}</h2>
                    </div>
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


    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to compress your files?' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Combine multiple PDFs and images into one' !!}</p>
			        @if(array_key_exists(7, $PageBlocks))
		                <a href="#" class="new-block merge-pdf-info">
		                	{!! $PageBlocks[7] !!}

		                </a>
                    @endif
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>PDF</strong>
                        OR
                        <strong>IMAGE</strong>
                        FILE
                    </h3>

                    @include('includes.upload-button')

                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="fixed-bottom-panel">
        <form class="fixed-task-form fixed-task-form-hidden">
            <div class="checkbox-items-wrap">
                <span class="btns-resolution-span">Pages</span>
                <div class="checkbox-item-content">
                    <label>
                        <input autocomplete="off" name="onlySpecificRanges" value="true" type="checkbox">
                        <span>Include only specific page ranges</span>
                    </label>
                </div>
                <div class="checkbox-item-content">
                    <label>
                        <input name="blankPageIfOdd" value="true" type="checkbox">
                        <span>Double sided printing</span>
                    </label>
                    <span class="input-help tooltip">
					<span class="tooltiptext">Adds a blank page at the end of documents with odd number of pages.</span>
					<i class="far fa-question-circle tooltip"></i>
				</span>
                </div>
                <div class="checkbox-item-content">
                    <label>
                        <input name="normalizePageSizes" value="true" type="checkbox">
                        <span>Make all pages same size</span>
                    </label>
                    <span class="input-help tooltip">
					<span class="tooltiptext">Merged pages will be resized so they all have the same width, based on the dimensions of the first page.</span>
					<i class="far fa-question-circle"></i>
				</span>
                </div>
            </div>
            <div class="image-radio-item flex-column">
                <span class="btns-resolution-span">Bookmarks (outline)</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="outline" value="keepall" id="outline1" checked="checked">
                        <span class="resolution-item-checkmark">
						Keep all
						<span class="input-help tooltip">
							<span class="tooltiptext"><img src="img/outline-keep-all.png" alt=""></span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="outline" value="discardall" id="outline2">
                        <span class="resolution-item-checkmark">Discard all</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="outline" value="each_doc" id="outline3">
                        <span class="resolution-item-checkmark">
						One entry each doc
						<span class="input-help tooltip">
							<span class="tooltiptext"><img src="img/outline-one-entry-per-doc.png" alt=""></span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="outline" value="keepall2" id="outline4">
                        <span class="resolution-item-checkmark">
						Keep all, under one entry each doc
						<span class="input-help tooltip">
							<span class="tooltiptext"><img src="img/outline-keep-all-under-entry-per-doc.png" alt=""></span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                </div>
            </div>
            <div class="image-radio-item flex-column">
                <span class="btns-resolution-span">Table of Contents</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="tableOfContents" id="tableOfContents1" checked="checked" value="0">
                        <span class="resolution-item-checkmark">None</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="tableOfContents" id="tableOfContents2" value="file_names">
                        <span class="resolution-item-checkmark">Based on file names</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="tableOfContents" id="tableOfContents3" value="document_titles">
                        <span class="resolution-item-checkmark">Based on document titles</span>
                    </label>
                </div>
            </div>
            <div class="checkbox-items-wrap">
                <div class="checkbox-item-content">
                    <label>
                        <input name="firstInputCoverTitle" value="true" type="checkbox">
                        <span>First document is a cover/title</span>
                    </label>
                    <span class="input-help tooltip">
					<span class="tooltiptext">We'll add the Table of Contents after the pages from the first document.</span>
					<i class="far fa-question-circle"></i>
				</span>
                </div>
                <div class="checkbox-item-content">
                    <label>
                        <input name="filenameFooter" value="true" type="checkbox">
                        <span>Add filename to page footer</span>
                    </label>
                    <span class="input-help tooltip">
					<span class="tooltiptext">Merged pages will have the name of the original document they belonged to in the footer.</span>
					<i class="far fa-question-circle"></i>
				</span>
                </div>
            </div>
            <div class="image-radio-item flex-column">
                <span class="btns-resolution-span">Form Fields</span>
                <div class="btns-resolution">
                    <label class="resolution-item">
                        <input type="radio" name="formFields" value="discard" id="formFields1" checked="checked">
                        <span class="resolution-item-checkmark">
						Discard
						<span class="input-help tooltip">
							<span class="tooltiptext">Removes all form fields from the resulting document</span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="formFields" value="merge" id="formFields2">
                        <span class="resolution-item-checkmark">
						Merge
						<span class="input-help tooltip">
							<span class="tooltiptext">Merges all forms found in input documents.</span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="formFields" value="merge" id="formFields3">
                        <span class="resolution-item-checkmark">
						Merge (Rename existing)
						<span class="input-help tooltip">
							<span class="tooltiptext">Merges all forms found in input documents, renaming existing fields.</span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                    <label class="resolution-item">
                        <input type="radio" name="formFields" value="flatten" id="formFields4">
                        <span class="resolution-item-checkmark">
						Flatten
						<span class="input-help tooltip">
							<span class="tooltiptext">Select this option if you want to discard any editable form fields, keeping visible their currently filled in values.</span>
							<i class="far fa-question-circle"></i>
						</span>
					</span>
                    </label>
                </div>
            </div>
        </form>
        <div class="more-options-btns-wrap">
            <button class="options-btn full merge-pdf-save" type="button">Merge PDF files</button>
            <a href="#" class="options-btn-transparent more-options-btn">More options</a>
        </div>
    </section>
    
    <style>
    	.ranges input{
    		width: 100%;
			margin-top: 10px;
			padding-right: 20px;
    	}
    
    </style>
    

    @include ('inc.result_block')
@endsection
