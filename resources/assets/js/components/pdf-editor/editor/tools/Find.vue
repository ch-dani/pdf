<template>
    <li class="drop_tool_menu">
        <a class="tools-menu-item tools-menu-item-more" href="#">
            <img src="/img/icon-arrow-down.svg"
                 alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu dropdown-menu-right list-opts">
            <li>
                <a class="open_search_modal" href="#find-replace-modal" data-editor-name="skip_it">{{ title }}</a>
                <img
                        class="ml-auto" src="/img/icon-search.svg" alt="">
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Find & Replace',
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
            this.getTranslatedText('Find & Replace', 'title');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>