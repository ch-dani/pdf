<div class="downloader">
    @include("inc-freeconvert.ads")

    <div class="xxz_zz downloader__upload-wrapper {{ !Auth::user() || !Auth::user()->subscription || Auth::user()->subscription->status != 'active' ? '' : 'active-user' }}">
        <form>
            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>" id="editor_csrf">
            <div class="downloader__doshed {{ !Auth::user() || !Auth::user()->subscription || Auth::user()->subscription->status != 'active' ? '' : 'active-user_doshed' }}">
                @php
                    if( //Request::path() == '/' ||
	                    Request::path() == 'pdf-to-jpg' ||
                        Request::path() == 'pdf-to-png' ||
                        Request::path() == 'jpg-to-pdf' ||
                        Request::path() == 'png-to-pdf' ||
                        Request::path() == 'delete-pdf-pages' ||
                        Request::path() == 'merge-pdf' ||
                        Request::path() == 'epub-to-pdf' ||
                        Request::path() == 'excel-to-pdf' ||
                        Request::path() == 'ppt-to-pdf' ||
                        Request::path() == 'pdf-to-word' ||
                        Request::path() == 'word-to-pdf' ||
                        //Request::path() == 'pdf-to-ppt' ||
                        //Request::path() == 'pdf-to-epub' ||
                        Request::path() == 'split-pdf'
                        )
                    {
                        $inputTypeFileClass = 'upload-file-tool';
                    }else{
                        $inputTypeFileClass = 'user_pdf';
                    }
                @endphp
                <input class="{{ $inputTypeFileClass }}" type="file" accept="{{$accept??"application/pdf"}}"
                       @if (Request::path()=="bates-numbering-pdf") multiple @endif >
                @if(Request::path() == 'pdf-to-png')
                    <input type="hidden" name="format" value="png16m">
                @endif
                <div class="downloader__upload">
                    <div class="downloader__icon"><img src="{{ asset('freeconvert/img/doc.svg') }}"></div>
                    <div class="downloader__text">@if (Request::path()=="/") {{ t("Upload PDF file") }} @else {{ t("Upload file") }} @endif</div>
                    <div class="downloader__arrow"
                         id="docSelectBtn">
                        <img src="{{ asset('freeconvert/img/arrow-white-down.svg') }}">
                    </div>
                </div>
                <div class="downloader__sub-text">{{ t("or Drop files here") }}</div>
            </div>
            <div class="select_wrapper {{ auth()->check() && auth()->user()->subscription && auth()->user()->subscription->status === 'active' ? 'select_wrapper__no-aids' : '' }}" id="docSelect">
                <a id="drpbox-chooser" href='#' class="select_item drpbox-chooser">
                    @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                    {{ t("Dropbox") }}
                </a>
                <a id="gdrive-chooser" href='#' class="select_item gdrive-chooser">
                    <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
                    {{ t("Google Drive") }}
                </a>
                <a href='#' class="select_item weburl-chooser">
                    <img src="{{ asset('freeconvert/img/logo-link.png') }}" alt="">
                    {{ t("Web Address (URL)") }}
                </a>
            </div>
        </form>
    </div>

    @include("inc-freeconvert.ads")
</div>
</div>
