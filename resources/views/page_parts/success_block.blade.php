<div class="section_top download_block hidden">
    <div class="container">
    
        <div class="contact-us">
            <a class="result_link_here contact-us__button btn-gradient" href="#" download>
                <img src="/freeconvert/img/download.svg" width="30" height="30">
                {{t('Download result')}}
            </a>
        </div>

        <ul class="save" style="margin-top: 10px;">
            <li class="save__li">
                <a id="save-gdrive" title="Save to Google Drive" data-src="" data-filename="" class="result_link_here"
                   href="#">
                    <img src="/freeconvert/img/logo_google-drive.svg" width="26" height="23">
                    {{t('Save to Google Drive')}}
                </a>
                <span style="display: none;" class="save-x2" id="savetodrive-div"></span>
            </li>
            <li class="save__li">
                <a id="save-dropbox" title="Save to Dropbox" data-url="" data-file_name="" class="result_link_here"
                   href="#">
                    <img src="/freeconvert/img/logo_dropbox.svg" width="28" height="23">
                    {{t('Save to Dropbox')}}
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
	#savetodrive-div{
		cursor: pointer;
	}
</style>
