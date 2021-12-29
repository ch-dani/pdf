<div class="downloader">
    <div id="start_task" class="downloader__upload">
        <div class="downloader__icon"><img src="{{ asset('freeconvert/img/download_arrow.svg') }}"></div>
        <div class="downloader__text save-images-array save-pdf">{{ t("Process PDF") }}</div>
        <div class="downloader__arrow"></div>
    </div>
</div>

<div class="link_convert one_item">
    <div class="link_convert_left">
        {{--
        <a href="#" class="link_convert_item">
            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
            {{ t("Merge PDF") }}
        </a>
        <a href="#" class="link_convert_item">
            <img src="{{ asset('freeconvert/img/link_conver-2.png') }}" alt="">
            {{ t("Compress") }}
        </a>
        --}}
        <a href="#" class="link_convert_item remove">
            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
            {{ t("Remove") }}
        </a>
    </div>

    {{--
    <div class="link_convert_right">
        <a id="save-gdrive" href="#" class="link_convert_item">
            <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
            {{ t("Save to Google Drive") }}
        </a>
        <a id="save-dropbox" href="#" class="link_convert_item">
            @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
            {{ t("Save to Dropbox") }}
        </a>
    </div>
    --}}
</div>