<template>
    <div class="link-editable-menu element_editor ff-editable-menu" data-element-id="false">
        <button class="close" type="button" onclick="jQuery('.link-editable-menu').hide(); return false;"><span>×</span>
        </button>

        <div class="">
            <div class="form-group">
                Link properties
            </div>
            <div class="form-group" style="display: none;">
                <label for="external_title">Title</label>
                <input type="text" data-target='title' id="external_title" class="external_title" value=""
                       placeholder="title">
            </div>
            <div class="form-group">
                <label>
                    <input type="radio" name="link_type" class="link_type" value="1" checked="checked"
                           autocomplete="off">
                    Link to external URL
                </label>
            </div>

            <div v-if="false" class="form-group">
                <label for="external_title">URL</label>
                <input type="text" data-target='url' class="external_link" value="" placeholder="Link">
            </div>

            <div v-if="false" class="form-group">
                <label>
                    <input checked type="radio" name="link_type" class="link_type" value="2" autocomplete="off">
                    Link to internal page
                </label>
            </div>

            <div class="form-group">
                <label for="internal_link">URL</label>
                <input type="text" id="internal_link" data-target='url' class="internal_link" value=""
                       placeholder="Link">
            </div>

            <div class="form-group bottom-btns">
                <div class="btn-group">
                    <button class="clone" type="button"><i class="far fa-clone"></i></button>
                    <button class="delete" type="button"><i class="far fa-trash-alt"></i></button>
                </div>
            </div>

            <div v-if="false" class="link_edit_outer">
                <div class="link_type">
                    <label>
                        <input checked type="radio" name="link_type" class="link_type" value="1">
                        Link to external URL
                    </label>
                    <input type="text" data-target='title' class="external_title" value="" placeholder="title">
                    <input type="text" data-target='url' class="external_link" value="" placeholder="Link">
                </div>
                <div class="link_type">
                    <label>
                        <input type="radio" name="link_type" value="2" class="link_type">
                        Link to internal page
                    </label>
                    <input type="text" data-target='title' class="internal_title" value="" placeholder="title">
                    <input type="text" data-target='url' class="internal_link" value="" placeholder="Page num">
                </div>
            </div>
            <div v-if="false" class="btn-group">
                <button class="editable-btn delete">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>