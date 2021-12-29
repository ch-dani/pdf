<template>
    <div class="pop-up create-signature-modal" id="draw-modal" style="display: none;">
        <div class="sigtature-block">
            <div class="signature-close">×</div>
            <div class="signature-top">Create signature</div>
            <ul class="signature-btns">
                <li class="signatore-btn-block signatore-btn-active text_sign" data-type='text'>
                    <i class="far fa-keyboard"></i>Type
                </li>
                <li class="signatore-btn-block draw_sign" data-type='draw'>
                    <i class="fas fa-signature"></i>Draw
                </li>
                <li>
                    <i class="far fa-file-image"></i>
                    <label>
                        <input onclick="jQuery('#new_image_uploader').click(); return false; $('#draw-modal').hide(); return false; " class="signature-file" type="file" name="name" value="">
                        <span>image</span>
                    </label>
                </li>
            </ul>

            <div class="create-tab-container">
                <div class="create-tab-block create-tab-active" style="display: block;">
                    <input class="signature-input" id="sign_text_input" autocomplete="off" type="text" name="name" value="John Smith">
                    <div class="signature-wodrds" id="sign_previews">
                        <span style="font-family: 'Gamja Flower'; font-size: 35px; display: inline-block;  " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Indie Flower'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview first">John Smith</span>
                        <span style="font-family: 'Charmonman'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Pacifico'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Gloria Hallelujah'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Amatic SC'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Shadows Into Light'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Dancing Script'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Dokdo'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Permanent Marker'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Patrick Hand'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                        <span style="font-family: 'Courgette'; font-size: 35px; display: inline-block; " class="signaturePreview sign_preview">John Smith</span>
                    </div>
                </div>
                <div class="create-tab-block" style="display: none;">
                    <p class="touchpad-text">Sign your name using your mouse or touchpad.</p>
                    <div style="height: 200px; overflow: hidden; border: 1px solid #EAEBF2; position: relative">
                        <canvas width="850" height="200" id="sign_draw_canvas" style="position: absolute; top: 0; left: 0;"></canvas>
                        <img id="canvasimg" style="display:none;">
                    </div>
                    <button class="u-full-width erase_canvas" id="clear"><span aria-hidden="true">×</span> </button>
                </div>

            </div>
            <div class="signature-btn">
                <a id="save_new_sign" class="button-green save_new_sign" href="#">Save</a>
            </div>


        </div>
    </div>
</template>