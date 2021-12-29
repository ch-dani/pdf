<template>
    <li>
        <a class="tools-menu-item" data-editor-name="forms" href="#">
            <img src="/img/icon-form.svg"
                 alt="Icon Form">
            <span>{{ title }}</span>
            <img
                    src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu forms-opts">
            <li class="tools-default">
                <a form_element_type="text"
                   onclick="$('[data-editor-name=\'text\']').click(); return false;" href="#">
                    <img
                            src="/img/icon-t.svg" alt="T">
                </a>
                <a form_element_type="cross" class=" image_form_item" href="#">
                    <img
                            src="/img/icon-cross.svg" alt="Cross">
                </a>
                <a form_element_type="check" class=" image_form_item" href="#">
                    <img
                            src="/img/icon-check.svg" alt="Check">
                </a>
                <a form_element_type="elipse" class=" image_form_item" href="#">
                    <img
                            src="/img/icon-elipse.svg" alt="Elipse">
                </a>
            </li>
            <li class="divider"></li>
            <li class="upload-link">{{ create_text }}</li>
            <li class="tools-default change-form-item-tools-wrap">
                <a form_element_type="input" class="change_form_item" href="#">
                    <img
                            src="/img/form-textfield.svg" alt="input">
                </a>
                <a form_element_type="textarea" class="change_form_item" href="#">
                    <img
                            src="/img/form-textarea.svg" alt="textarea">
                </a>
                <a form_element_type="radio" class="change_form_item" href="#">
                    <img
                            src="/img/icon-elipse.svg" alt="radio">
                </a>
                <a form_element_type="checkbox" class="change_form_item" href="#">
                    <img
                            style="width: 30px;" src="/img/form-checkbox.svg" alt="Elipse">
                </a>
                <a form_element_type="dropdown" class="change_form_item" href="#">
                    <img
                            src="/img/form-dropdown.svg" alt="Elipse">
                </a>
            </li>
        </ul>
    </li>

</template>

<script>
    export default {
        data() {
            return {
                title: 'Forms',
                create_text: 'Create new form fields',
            }
        },
        methods: {
            getTranslatedText(text, property) {
                axios
                    .post('/translate-phrase', {
                        text: text,
                        active_language: this.parentProps.activeLanguage
                    })
                    .then((response) => {
                        this[property] = response.data.data;
                    });
            },
        },
        mounted() {
            this.getTranslatedText('Forms', 'title');
            this.getTranslatedText('Create new form fields', 'create_text');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>