<template>
    <section v-if="countPageGuides" class="how-it-works">
        <page-guide-item v-for="(item, key) in pageGuides" :item="item" v-bind:key="key"></page-guide-item>
    </section>
</template>

<script>
    import PageGuideItem from './PageGuideItem.vue';

    export default {
        props: [
            'activeLanguage',
            'page'
        ],
        data() {
            return {
                pageGuides: [],
            }
        },
        components: {
            PageGuideItem
        },
        methods: {
            getPageGuides() {
                axios
                    .post('/get-page-guides', {
                        'active_language': this.activeLanguage,
                        'page_id': this.page,
                    })
                    .then((response) => {
                        this.pageGuides = response.data.data;
                    });
            }
        },
        computed: {
            countPageGuides()  {
                return this.pageGuides.length > 0;
            }
        },
        mounted() {
            this.getPageGuides();
        }
    }
</script>