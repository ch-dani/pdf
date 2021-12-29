<template>
    <li>
        <a class="tools-menu-item open_undo_modal" href="#" title="Undo" data-editor-name="skip_it">
            <img src="/img/icon-undo.svg" alt="Arrow Undo">
            <span>{{ title }}</span>
        </a>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Undo',
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
            this.getTranslatedText('Undo', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>