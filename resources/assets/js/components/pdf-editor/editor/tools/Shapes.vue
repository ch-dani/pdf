<template>
    <li>
        <a class="tools-menu-item" href="#" data-editor-name="rectangle">
            <img    style=" width: 18px; "
                    src="/img/icon-shapes.svg"
                    alt="Icon Shapes">
            <span>{{ title }}</span>
            <img
                    src="/img/icon-arrow-down-small.svg" alt="Arrow Down">
        </a>
        <ul class="tools-dropdown-menu list-opts shapes_dropdown">
            <li>
                <a class="sub_menu_item" data-editor-name="rectangle" href="#">{{ rectangle }}</a>
                <img
                        class="ml-auto" src="/img/icon-shapes.svg" alt="">
            </li>
            <li>
                <a class="sub_menu_item" data-editor-name="ellipse" href="#">{{ ellipse }}</a>
                <img
                        class="ml-auto" src="/img/icon-shapes.png" style=" width: 18px; " alt="">
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                title: 'Shapes',
                rectangle: 'Rectangle',
                ellipse: 'Ellipse',
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
            this.getTranslatedText('Shapes', 'title');
            this.getTranslatedText('Rectangle', 'rectangle');
            this.getTranslatedText('Ellipse', 'ellipse');
        },
        computed: {
            parentProps: function() {
                return this.$parent.$props; // or whatever you want to access
            }
        }
    }
</script>
