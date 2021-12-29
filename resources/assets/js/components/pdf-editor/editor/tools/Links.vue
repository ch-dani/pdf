<template>
    <li>
        <a class="tools-menu-item" data-editor-name="links" href="#">
            <img src="/img/icon-link.svg"
                 alt="Icon Link">
            <span>{{ title }}</span>
        </a>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Links',
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
            this.getTranslatedText('Links', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>