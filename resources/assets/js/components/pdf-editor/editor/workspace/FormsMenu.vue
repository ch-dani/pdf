<template>
    <div class="forms-editable-menu element_editor ff-editable-menu" data-element-id="false">
        <a href="#" class="close_editor close"
           onclick="jQuery('.forms-editable-menu').hide(); return false;"><span>×</span></a>
        <div class="field_row field_name_row form-group">
            <label>
                Field name
                <input type="text" id="field_name">
            </label>
        </div>
        <div class="field_row  show_if_select field_options_row form-group field-options-group">
            <label>
                Options (one per line)
                <textarea id="field_options"></textarea>
            </label>
        </div>
        <div class="field_row  show_if_select field_allow_multiple_row form-group checkbox">
            <label>
                <input type="checkbox" id="field_allow_multiple">
                Allow multiple selections
            </label>
        </div>
        <div class="form-group bottom-btns">
            <div class="btn-group">
                <button class="button editable-btn delete_form_element">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>
