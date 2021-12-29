@extends('layouts.layout2')

@section('content')
    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'JPG to PDF Online' !!}</h1>
                    <p>{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Convert Images to PDF' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#" id="drop_zone">
                    <div class="upload-img">
                        <img src="{{ asset('img/pdf-img.svg') }}" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>JPG</strong>
                        FILES
                    </h3>
                    @php
                        $accept = 'image/jpeg,image/png,image/gif';
                    @endphp
                    @include('includes.upload-button-multiple')
                    {{ csrf_field() }}
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(3, $PageBlocks) ? $PageBlocks[3] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section id="pages-pdf" class="s-image-canvas">
		<div class="container">
			<div class="combine-reorder-tools">
				<div class="btn-group upload-btn-group">
					<span class="btn fileinput-button">
						<i class="far fa-file-pdf"></i>Add More Files
						<form action="#" enctype="multipart/form-data" method="post">
							<input accept=".jpg,.png" title="Upload" multiple="multiple" data-scope="task-file" name="file" type="file" class="fileupload upload-file-tool">
						</form>
					</span>
				</div>
			</div>
		</div>



        <ul class="image-canvas-list" id="sortable">
        </ul>
    </section>

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
                    <h1>{!! array_key_exists(4, $PageBlocks) ? $PageBlocks[4] : 'Ready to convert your images? Let\'s go!' !!}</h1>
                    <p>{!! array_key_exists(5, $PageBlocks) ? $PageBlocks[5] : 'Convert Images to PDF' !!}</p>
                </div>
            </div>
            <div class="app-welcome">
                <form action="#">
                    <div class="upload-img">
                        <img src="img/pdf-img.svg" alt="">
                    </div>
                    <h3>UPLOAD
                        <strong>JPG</strong>
                        FILES
                    </h3>
                    <div class="upload-btn-wrap">
                        <div class="upload-button">
    						<span>
    							Upload JPG file
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
                                    Google Drive
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
                    <span class="upload-bottom-text">or start with a <a href="#"
                                                                        class="new-pdf">blank document</a></span>
                </form>
                <div class="upload-welcom-descr">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'Files stay private. Automatically deleted after 5 hours. Free service for documents up to 200 pages or 50 Mb and 3 tasks per hour.' !!}
                </div>
            </div>
        </div>
    </div>

    <section class="fixed-bottom-panel">
        <form class="fixed-task-form">
            <div class="select-items-wrap">
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">Page size:</div>
                    <div class="dropup-select-style">
                        <select id="pageFormat">
                            <option value="auto">Fit to image</option>
                            <option data-h="8.27" data-w="5.83" value="a5">A5 (5.83 x 8.27)</option>
                            <option selected="selected" data-h="11.69" data-w="8.27" value="a4">A4 (8.27 x 11.69)
                            </option>
                            <option data-h="16.54" data-w="11.69" value="a3">A3 (11.69 x 16.54)</option>
                            <option data-h="33.11" data-w="23.39" value="a2">A2 (23.39 x 33.11)</option>
                            <option data-h="46.81" data-w="33.11" value="a1">A1 (33.11 x 46.81)</option>
                            <option data-h="11" data-w="8.5" value="letter">Letter (8.5 x 11)</option>
                            <option data-h="14" data-w="8.5" value="legal">Legal (8.5 x 14)</option>
                            <option data-h="17" data-w="11" value="legder">Ledger (11 x 17)</option>
                            <option data-h="11" data-w="17" value="11x17">Tabloid (17 x 11)</option>
                            <option data-h="10.55" data-w="7.25" value="executive">Executive (7.25 x 10.55)</option>
                        </select>
                    </div>
                </div>
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">Page orientation:</div>
                    <div class="dropup-select-style">
                        <select id="pageOrientation">
                            <option selected="selected" value="auto">Auto</option>
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                </div>
                <div class="select-item-dropup">
                    <div class="select-item-dropup-title">Margin:</div>
                    <div class="dropup-select-style">
                        <select id="pageMargin">
                            <option selected="selected" value="0">None</option>
                            <option value="0.5">Small margin: 0.5"</option>
                            <option value="1">Large margin: 1"</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="select-dropup-bottom-btn-wrap">
                <button class="options-btn save-pdf" type="button">Convert to PDF</button>
            </div>
        </form>
    </section>

    @include ('inc.result_block')
@endsection
