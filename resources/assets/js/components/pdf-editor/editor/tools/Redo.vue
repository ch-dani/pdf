<template>
    <li>
        <a class="tools-menu-item open_redo_modal" href="#" title="Redo" data-editor-name="skip_it">
            <img src="/img/icon-undo.svg" alt="Arrow Redo">
            <span>{{ title }}</span>
        </a>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Redo',
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
            this.getTranslatedText('Redo', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>