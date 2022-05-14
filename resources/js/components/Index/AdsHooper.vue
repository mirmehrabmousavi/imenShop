<template>
    <div class="homeTopAdvertise width">
        <div class="advertiseSlider">
            <hooper :settings="hooperSettingsAd">
                <slide v-for="(item , index) in JSON.parse(data.post)" :key="index">
                    <div class="over"></div>
                    <a :href="item.address">
                        <img :src="item.image" :alt="item.address">
                    </a>
                </slide>
                <hooper-pagination slot="hooper-addons"></hooper-pagination>
                <hooper-navigation slot="hooper-addons"></hooper-navigation>
            </hooper>
        </div>
        <div class="advertiseItems">
            <div class="advertiseItem" v-for="item in JSON.parse(data.title)">
                <a :href="item.address">
                    <img :src="item.image" :alt="item.image">
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import {
    Hooper,
    Slide,
    Navigation as HooperNavigation,
    Pagination as HooperPagination,
} from 'hooper';
export default {
    name: "AdsHooper",
    props: ['data'],
    components:{
        Hooper,
        HooperNavigation,
        Slide,
        HooperPagination,
    },
    data() {
        return {
            hooperSettingsAd: {
                wheelControl:false,
                centerMode: false,
                rtl: false,
                vertical: true,
                transition: 300,
                itemsToShow: 1,
                autoPlay:true,
                playSpeed : 5000
            },
        };
    },
}
</script>

<style scoped>

</style>
