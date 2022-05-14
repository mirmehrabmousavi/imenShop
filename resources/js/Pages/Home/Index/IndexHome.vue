<template>
    <home-layout>
        <div class="allIndexHome">
            <div class="allIndexHomeItems" v-for="item in vidget" :key="item.id">
                <div class="allIndexHomeItem" v-if="item.name == 'اسلایدر بزرگ تبلیغ'">
                    <big-slider :data="item"></big-slider>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'پیشنهاد شگفت انگیز'">
                    <suggest-container :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'محصولات دانلودی'">
                    <file-index :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'خبر ها'">
                    <news-index :data="item" :post="item.post"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'دسته بندی و پیشنهاد لحظه ای'">
                    <special-index :moment="moment" :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'پست افقی'">
                    <horizontal-product :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'پست های خارج پس زمینه'">
                    <back-product :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'تبلیغات اسلایدری'">
                    <ads-hooper :data="item"></ads-hooper>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'دسته بندی رنگی'">
                    <professional-category :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'ویژگی ها'">
                    <site-property :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'پست ویژه با تصویر'">
                    <image-widget :data="item"></image-widget>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'تبلیغات ساده'">
                    <ads-index :data="item"/>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'جستجو'">
                    <search-index :maxPrice="maxPrice" :minPrice="minPrice"/>
                </div>
                <div class="mainCategory" v-if="item.name == 'دسته بندی خاص'">
                    <home-hoopers :data="item"></home-hoopers>
                </div>
                <div class="allIndexHomeItem" v-if="item.name == 'برند ویژه'">
                    <brand-index :data="item"/>
                </div>
            </div>
            <show-fast></show-fast>
            <all-compare></all-compare>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import HomeLayout from "../../../components/layout/HomeLayout";
import AdsHooper from "../../../components/Index/AdsHooper";
import HomeHoopers from "../../../components/Index/HomeHoopers";
import FlipCountdown from 'vue2-flip-countdown'
import SiteProperty from "../../../components/Index/SiteProperty";
import SuggestContainer from "../../../components/Index/SuggestContainer";
import AdsIndex from "../../../components/Index/AdsIndex";
import ProfessionalCategory from "../../../components/Index/ProfessionalCategory";
import BrandIndex from "../../../components/Index/BrandIndex";
import SpecialIndex from "../../../components/Index/SpecialIndex";
import SearchIndex from "../../../components/Index/SearchIndex";
import BackProduct from "../../../components/Index/BackProduct";
import BigSlider from "../../../components/Index/BigSlider";
import HorizontalProduct from "../../../components/Index/HorizontalProduct";
import ImageWidget from "../../../components/Index/imageWidget";
import ShowFast from "../ShowFast";
import AllCompare from "../AllCompare";
import NewsIndex from "../../../components/Index/NewsIndex";
import FileIndex from "../../../components/Index/FileIndex";
export default {
    name: "IndexHome",
    props: ['vidget','title','robots','moment','minPrice','maxPrice'],
    data(){
        return{
            i: 0
        }
    },
    components:{
        FileIndex,
        NewsIndex,
        AllCompare,
        ShowFast,
        ImageWidget,
        HorizontalProduct,
        BigSlider,
        BackProduct,
        SearchIndex,
        SpecialIndex,
        BrandIndex,
        ProfessionalCategory,
        AdsIndex,
        SuggestContainer,
        SiteProperty,
        HomeLayout,
        AdsHooper,
        FlipCountdown,
        HomeHoopers,
        SvgIcon,
    },
    metaInfo() {
        return {
            title: `${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                amp: true,
                reptilian: 'gator'
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    methods:{
        getLike(){
            const url = `/get-like`;
            axios.get(url)
                .then(response=>{
                    this.$eventHub.emit('getLike' , response.data);
                })
        },
        sendRobotMessage(){
            for ( this.i ; this.i <  this.robots.length; this.i++) {
                const url = `/send-robot/${this.robots[this.i]}`;
                axios.get(url);
            }
        },
        getBookmark(){
            const url = `/get-bookmark`;
            axios.get(url)
                .then(response=>{
                    this.$eventHub.emit('getBookmark' , response.data);
                })
        },
    },
    mounted(){
        this.getLike();
        this.getBookmark();
        this.sendRobotMessage();
    },
    created: function() {
        this.$eventHub.on('allLike', this.getLike);
        this.$eventHub.on('allBookmark', this.getBookmark);
    },
}
</script>

<style scoped>

</style>
