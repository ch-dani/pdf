<template>
    <div class="annotate-editable-menu element_editor ff-editable-menu" data-element-id="false">
        <a href="#" class="close_editor close"
           onclick="jQuery('.annotate-editable-menu').hide(); return false;"><span>×</span></a>
        <div class="field_row field_name_row form-group">
            <label>
                Title
                <input type="text" id="annotate_title">
            </label>
        </div>
        <div class="field_row  field_options_row form-group field-options-group">
            <label>
                Text
                <textarea id="annotate_content"></textarea>
            </label>
        </div>

        <div class="form-group bottom-btns">
            <div class="btn-group">
                <button class="button editable-btn delete_annotate">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>