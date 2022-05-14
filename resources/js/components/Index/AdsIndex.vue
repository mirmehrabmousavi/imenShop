<template>
    <div class="allAdsIndex width" v-if="data.post != ''">
        <div class="adsItems">
            <div class="adsItem" v-for="item in JSON.parse(data.post)">
                <a :href="item.address">
                    <img :src="item.image" :alt="item.address">
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AdsIndex",
    props:['data']
}
</script>

<style scoped>

</style>
