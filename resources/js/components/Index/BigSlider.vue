<template>
    <div class="bigSlider">
        <hooper :settings="hooperSettings">
            <slide v-for="(item,index) in JSON.parse(data.post)" :key="index">
                <a :href="item.address">
                    <img :src="item.image" :alt="item.image">
                </a>
            </slide>
            <hooper-pagination slot="hooper-addons"></hooper-pagination>
            <hooper-navigation slot="hooper-addons"></hooper-navigation>
        </hooper>
    </div>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Pagination as HooperPagination, Slide} from "hooper";
import 'hooper/dist/hooper.css';

export default {
    name: "BigSlider",
    props:['data'],
    components:{
        Hooper,
        HooperNavigation,
        Slide,
        HooperPagination,
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                rtl: true,
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
