<template>
    <div class="container">
        <div class="app-tools">
            <div class="wrapper">
                <ul class="tools-menu">

                    <text-tool></text-tool>

                    <links></links>

                    <forms></forms>

                    <images></images>

                    <sign></sign>

                    <whiteout></whiteout>

                    <annotate></annotate>

                    <shapes></shapes>

                    <undo></undo>

                    <redo></redo>

                    <find></find>
                </ul>
            </div>

        </div>
    </div>
</template>

<script>
    import TextTool from './tools/Text.vue';
    import Links from './tools/Links.vue';
    import Forms from './tools/Forms.vue';
    import Images from './tools/Images.vue';
    import Sign from './tools/Sign.vue';
    import Whiteout from './tools/Whiteout.vue';
    import Annotate from './tools/Annotate.vue';
    import Shapes from './tools/Shapes.vue';
    import Undo from './tools/Undo.vue';
    import Redo from './tools/Redo.vue';
    import Find from './tools/Find.vue';

    export default {
        components: {
            TextTool,
            Links,
            Forms,
            Images,
            Sign,
            Whiteout,
            Annotate,
            Shapes,
            Undo,
            Redo,
            Find
        },
        props: [
            'activeLanguage'
        ]
    }
</script>
