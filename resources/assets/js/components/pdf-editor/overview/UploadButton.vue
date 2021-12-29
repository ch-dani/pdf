<template>
    <div class="upload-btn-wrap">
        <div class="upload-button">
		<span>
			Upload PDF file
		</span>
            <!--<input class="user_pdf" type="file" accept="application/pdf"  @if (Request::path()=="bates-numbering-pdf") multiple @endif >-->
            <input class="user_pdf" type="file" accept="application/pdf" :multiple="isMultiple">
        </div>
        <button class="dropdown-toggle-btn" type="button">
            <i class="fas fa-caret-down"></i>
        </button>
        <ul class="dropdown-menu-upload">
            <li><a class="drpbox-chooser" href="#" id="drpbox-chooser"><i class="fab fa-dropbox icon"></i> Dropbox</a></li>
            <li><a class="gdrive-chooser" href="#" id="gdrive-chooser"><img class="icon" src="/img/gdrive.png" alt=""> Google Drive</a></li>
            <li><a class="weburl-chooser" href="#"><i class="fas fa-link icon"></i> Web Address (URL)</a></li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: ['path'],
        computed: {
            isMultiple() {
                return this.path == 'bates-numbering-pdf';
            }
        }
    }
</script>