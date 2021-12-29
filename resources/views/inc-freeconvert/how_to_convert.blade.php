@php
    /*$PageGuidesContent = json_decode(App\Guide::find(2)->content, true)[1];*/
@endphp

<section class="module__how-convert module bg-white">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h2-title title_main">
                {{ isset($PageGuides[0]->title) ? $PageGuides[0]->title : t('How to convert JPG to PDF?') }}
            </h2>
        </div>
        <div class="row">
            @if(isset($PageGuides[0]->content))
                @php htmlspecialchars($PageGuides[0]->content) @endphp
            @endif
            {{--            {!!html_entity_decode($PageGuidesContent)!!}--}}

            @if(!Auth::id())
                <div class="contact-us">
                    <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{ t("Sign Up") }}</a>
                </div>
            @endif
        </div>
    </div>
</section>

{{--<section class="module__how-convert module bg-white">--}}
{{--    <div class="container">--}}
{{--        <div class="title-wrapper">--}}
{{--            <h2 class="h2-title title_main">How to convert Word to PDF?</h2>--}}

{{--        </div>--}}
{{--        <div class="row">--}}
{{--            <div class="col-sm-4">--}}
{{--                <div class="convert">--}}
{{--                    <div class="convert__step">1</div>--}}
{{--                    <h4 class="convert__title">--}}
{{--                        Upload your files--}}
{{--                    </h4>--}}
{{--                    <p class="convert__p">--}}
{{--                        To upload your files from your computer, click “Upload DOC File” and select the files you want--}}
{{--                        to edit or drag and drop the files to the page.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-sm-4">--}}
{{--                <div class="convert ">--}}
{{--                    <div class="convert__step">2</div>--}}
{{--                    <h4 class="convert__title">--}}
{{--                        Convert Word to PDF--}}
{{--                    </h4>--}}
{{--                    <div class="convert-bg"></div>--}}
{{--                    <p class="convert__p">--}}
{{--                        Convert your Word documents into PDF files by simply clicking “Convert to PDF” and wait for it to be processed.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-sm-4">--}}
{{--                <div class="convert">--}}
{{--                    <div class="convert__step">3</div>--}}
{{--                    <h4 class="convert__title">--}}
{{--                        Download Your PDF Document--}}
{{--                    </h4>--}}
{{--                    <p class="convert__p">--}}
{{--                        Download your file to save it on your computer. You may also save it in your online accounts such as--}}
{{--                        Dropbox or Google Drive, share it via email, print the new document, rename or even continue editing--}}
{{--                        with a new task.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="contact-us">--}}
{{--                <a class="contact-us__button" href="#">Sign Up</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
