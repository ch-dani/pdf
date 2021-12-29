<template>
    <div>
        <text-menu></text-menu>
        <image-menu></image-menu>
        <links-menu></links-menu>
        <whiteout-menu></whiteout-menu>
        <forms-menu></forms-menu>
        <annotate-menu></annotate-menu>
    </div>
</template>

<script>

    import TextMenu from './TextMenu.vue';
    import ImageMenu from './ImageMenu.vue';
    import LinksMenu from './LinksMenu.vue';
    import WhiteoutMenu from './WhiteoutMenu.vue';
    import FormsMenu from './FormsMenu.vue';
    import AnnotateMenu from './AnnotateMenu.vue';

    export default {
        components: {
            TextMenu,
            ImageMenu,
            LinksMenu,
            WhiteoutMenu,
            FormsMenu,
            AnnotateMenu
        }
    }
</script>

