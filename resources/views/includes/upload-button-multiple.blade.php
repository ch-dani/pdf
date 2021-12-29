<div class="upload-btn-wrap">
    <div class="upload-button">
		<span>
			Upload PDF file
		</span>
        <input type="file" class="upload-file-tool" multiple accept="{{ isset($accept) ? $accept : '' }}">
    </div>
    <button class="dropdown-toggle-btn" type="button">
        <i class="fas fa-caret-down"></i>
    </button>
    <ul class="dropdown-menu-upload">
        <li><a class="drpbox-chooser" href="#" id="drpbox-chooser"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
        <li><a id="gdrive-chooser" class="gdrive-chooser" href="#"><img class="icon" src="img/gdrive.png" alt=""> Google Drive</a></li>
        <li><a class="weburl-chooser" href="#"><i class="fas fa-link icon"></i> Web Address (URL)</a></li>
    </ul>
</div>
