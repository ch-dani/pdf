<template>
    <span>
        <div class="pop-up" id="find-replace-modal" style="display: none;">
            <div class="modal-header">
                <div class="modal-title">Find &amp; Replace</div>
                <div class="close-replace-modal">×</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="form-group">
                        <input value="" id="find_text_input" name="findText" placeholder="Find" class="form-control"
                               type="text">
                    </div>
                    <div class="form-group">
                        <input value="" id="replace_text_input" name="replaceText" placeholder="Replace"
                               class="form-control" type="text">
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="find_match_case" name="replaceMatchCase" type="checkbox"> Match case
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            Found <span style="margin: 0 5px;" class='found_matches'>0</span> matches
                        </label>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button class="btn btn-replace" type="button" id="start_replace">Replace</button>
                <button class="btn btn-replace-find-next" id="start_replace_and_find_text"
                        type="button">Replace &amp; Find</button>
                <button class="btn btn-find-next" id="start_find_text" type="button">Find</button>
            </div>
        </div>

        <div class="pop-up" id="find-replace-modal" style="display: none;">
            <div class="modal-header">
                <div class="modal-title">Find & Replace</div>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="form-group">
                        <div class="half-column">
                            <input value="" name="findText" placeholder="Find" class="form-control" type="text">
                        </div>
                        <div class="half-column">
                            <input value="" name="replaceText" placeholder="Replace" class="form-control" type="text">
                        </div>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input name="replaceMatchCase" type="checkbox"> Match case
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-replace" type="button" disabled="disabled">Replace</button>
                <button class="btn btn-replace-find-next" type="button" disabled="disabled">Replace &amp; Find</button>
                <button class="btn btn-find-next" type="button">Find</button>
            </div>
        </div>
    </span>
</template>