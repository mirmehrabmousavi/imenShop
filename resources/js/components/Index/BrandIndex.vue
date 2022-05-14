<template>
    <div class="containerBrands width">
        <div class="containerBrandsTitle">
            <label v-if="$i18n.locale == 'fa'">{{ data.title }}</label>
            <label class="en" v-if="$i18n.locale == 'en'">{{ data.titleEn }}</label>
            <div class="moreProduct">
            </div>
        </div>
        <hooper :settings="hooperSettings">
            <slide v-for="(item , index) in data.post" :key="index" :title="item.name">
                <inertia-link :href="'/archive/brand/' + item.slug">
                    <div class="pic">
                        <img :src="item.image" :alt="item.name">
                    </div>
                    <span v-if="$i18n.locale == 'fa'">
                        {{item.post_count}}
                        {{ $t('product') }}
                    </span>
                    <span class="en" v-if="$i18n.locale == 'en'">
                        {{item.post_count}}
                        {{ $t('product') }}
                    </span>
                </inertia-link>
            </slide>
            <hooper-navigation slot="hooper-addons"></hooper-navigation>
        </hooper>
    </div>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Pagination as HooperPagination, Slide} from "hooper";
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "BrandIndex",
    props:['data'],
    components:{
        Hooper,
        HooperNavigation,
        HooperPagination,
        Slide,
        SvgIcon,
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                transition: 700,
                breakpoints: {
                    700: {
                        itemsToShow: 2,
                        itemsToSlide: 2,
                    },
                    1000: {
                        itemsToShow: 3,
                        itemsToSlide: 3,
                    },
                    1200: {
                        itemsToShow: 4,
                        itemsToSlide: 4,
                    },
                    1600: {
                        itemsToShow: 5,
                        itemsToSlide: 5,
                    },
                }
            },
        };
    },
}
</script>

<style scoped>

</style>
