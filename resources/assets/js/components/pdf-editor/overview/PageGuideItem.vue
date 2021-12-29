<template>
    <span>
    <div class="title-section" v-if="item.title != null">
        <h2>{{ item.title }}</h2>
    </div>

        <!--@if($ads && $device_is=='phone')-->
        <!--@include("ads.adx320x100")-->
        <!--@endif  -->

    <div class="container centered">
        <p class="title-description" v-if="item.subtitle != null">{{ item.subtitle }}</p>
        <div class="post" v-if="item.content != null" v-html="item.content"></div>
    </div>

    </span>

</template>

<script>
    export default {
        props: ['item']
    }
</script>