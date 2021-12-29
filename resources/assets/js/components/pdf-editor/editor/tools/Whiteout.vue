<template>
    <li>
        <a class="tools-menu-item" data-editor-name="whiteout" href="#">
            <img
                    src="/img/icon-eraser.svg"
                    alt="Icon Eraser">
            <span>{{ title }}</span>
        </a>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Whiteout',
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
            this.getTranslatedText('Whiteout', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>