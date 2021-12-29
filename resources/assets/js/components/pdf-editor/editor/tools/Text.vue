<template>
    <li>
        <a class="tools-menu-item" data-editor-name="text" href="#">
            <img src="/img/icon-text.svg" alt="Icon Text">
            <span>{{ title }}</span>
        </a>
    </li>

</template>

<script>
    export default {
        data() {
            return {
                title: 'Text',
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
            this.getTranslatedText('Text', 'title');
            console.log(this.parentProps);
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>