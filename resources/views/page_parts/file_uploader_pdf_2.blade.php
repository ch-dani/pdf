<div class="convert_doc left_doc">
    <div class="convert_doc_content">
        <form action="#" enctype="multipart/form-data" method="post">
            <input accept="{{ isset($accept) ? $accept : '' }}" title="Upload" multiple="multiple"
                   data-scope="task-file" name="file" type="file" class="fileupload upload-file-tool">
        </form>
        <h4 class="title_convert_doc">{{ t("Choose file") }}</h4>
        <div class="icon_add_doc">
            <img src="{{ asset('freeconvert/img/icon-add-file.png') }}" alt="">
            <div class="icon_add_select" id="docSelectBtn2">
                @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
            </div>
        </div>
        <h5 class="sub_title_convert_doc">{{ t("or drop files here") }}</h5>
    </div>
    <div class="select_wrapper" id="docSelect2">
        <a href="#" class="select_item drpbox-chooser">
            @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
            {{ t("Dropbox") }}
        </a>
        <a href="#" class="select_item gdrive-chooser">
            <img src="{{ asset('freeconvert/img/logos_google-drive.png') }}" alt="">
            {{ t("Google Drive") }}
        </a>
        <a href="#" class="select_item weburl-chooser">
            @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
            {{ t("Web Address (URL)") }}
        </a>
    </div>
</div>