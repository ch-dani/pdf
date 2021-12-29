<section class="section_top before_upload r_upload_section tool_section">
    <div class="container">
        <div class="title-wrapper">
            <h2 class="h30-title title_main">{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'PDF TOOL' !!}</h2>
            <h3 class="sub-title">{!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : 'Page subtitle' !!}</h3>
        </div>
		@include('page_parts.file_uploader_pdf')
</section>
