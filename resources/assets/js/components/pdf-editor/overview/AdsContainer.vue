<template>
    <section :style="sectionStyles">

    </section>
</template>

<script>
    export default {
        props: ['height', 'width'],
        computed: {
            sectionStyles() {
                let data = {
                    height: this.height+'px',
                    width: this.width+'px',
                    background: 'red',
                    margin: '20px auto',
                };

                if (this.height == 100 && this.width == 320) {
                    data.margin = '10px auto';
                }

                return data;
            }
        }
    }
</script>

<style>
    .upload-welcom-descr{
        margin-bottom: 0;
    }
    .result_outer{
        display: flex;
        justify-content: center;
    }
</style>