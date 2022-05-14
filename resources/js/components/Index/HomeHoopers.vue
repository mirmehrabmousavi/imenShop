<template>
    <div class="allHomeHooper width">
        <div class="containerCategories">
            <div class="containerCategoriesTitle">
                <label v-if="$i18n.locale == 'fa'">{{data.title}}</label>
                <label v-if="$i18n.locale == 'en'" class="en">{{data.titleEn}}</label>
                <div class="moreProduct">
                    <inertia-link v-if="$i18n.locale == 'fa'" :href="'/archive/products/' + data.slug">{{ data.more }}</inertia-link>
                    <inertia-link v-if="$i18n.locale == 'en'" class="en" :href="'/archive/products/' + data.slug">{{ data.moreEn }}</inertia-link>
                    <i>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </i>
                </div>
            </div>
            <hooper :settings="hooperSettings">
                <slide v-for="(item , index) in data.post.data" :key="index" :title="item.title">
                    <inertia-link class="containerCategoriesItem" :href="'/product/' + item.slug">
                        <div class="offProduct" v-if="item.off != null & $i18n.locale == 'fa'">
                            <span>
                                ٪ {{item.off}}
                            </span>
                        </div>
                        <div class="offProduct en" v-if="item.off != null & $i18n.locale == 'en'">
                            <span>
                                % {{item.off}}
                            </span>
                        </div>
                        <div class="pic">
                            <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                        </div>
                        <div class="postTitle">
                            <inertia-link v-if="$i18n.locale == 'fa'" :href="'/product/' + item.slug">{{item.title}}</inertia-link>
                            <inertia-link class="en" v-if="$i18n.locale == 'en'" :href="'/product/' + item.slug">{{item.titleEn}}</inertia-link>
                        </div>
                        <div class="postPrice" v-if="item.count >= 1">
                            <div class="postPriceItem" @click.prevent="addCart(item.id , index)">
                                <i v-if="loadingAdd == index">
                                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                </i>
                                <i v-if="loadingAdd != index">
                                    <svg-icon :icon="'#plus'"></svg-icon>
                                </i>
                            </div>
                            <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                <h3>
                                    {{item.price|NumFormat}}
                                    <span>تومان</span>
                                </h3>
                            </div>
                            <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                <h3>
                                    {{item.price|NumFormat}}
                                    <span>toman</span>
                                </h3>
                            </div>
                        </div>
                        <div class="checkCount" v-else>
                            <span>{{$t('notAvailable')}}</span>
                        </div>
                        <div class="productOptions">
                            <div class="productOption" title="علاقه مندی" @click.prevent="btnLike(item.id , index)">
                                <i v-if="loadingLike == index">
                                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                </i>
                                <i v-for="values in like" v-if="values == item.id && loadingLike != index">
                                    <svg-icon :icon="'#like'"></svg-icon>
                                </i>
                                <i>
                                    <svg-icon :icon="'#unlike'"></svg-icon>
                                </i>
                            </div>
                            <div class="productOption" title="نشانه گذاری" @click.prevent="btnBookmark(item.id ,index)">
                                <i v-if="loadingBookmark == index">
                                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                </i>
                                <i v-for="values in bookmark" v-if="values == item.id && loadingBookmark != index">
                                    <svg-icon :icon="'#bookmark'"></svg-icon>
                                </i>
                                <i>
                                    <svg-icon :icon="'#unbookmark'"></svg-icon>
                                </i>
                            </div>
                            <div class="productOption" title="مقایسه کردن" @click.prevent="btnComparison(item.id)">
                                <i class="active" v-for="values in allComparison" v-if="values == item.id">
                                    <svg-icon :icon="'#chart'"></svg-icon>
                                </i>
                                <i>
                                    <svg-icon :icon="'#chart'"></svg-icon>
                                </i>
                            </div>
                            <div class="productOption" title="مشاهده سریع" @click.prevent="btnShowFast(item.id)">
                                <i>
                                    <svg-icon :icon="'#search'"></svg-icon>
                                </i>
                            </div>
                        </div>
                    </inertia-link>
                </slide>
                <hooper-navigation slot="hooper-addons"></hooper-navigation>
            </hooper>
        </div>
    </div>
</template>

<script>
import { Hooper, Slide, Navigation as HooperNavigation, Pagination as HooperPagination,} from 'hooper';
import 'hooper/dist/hooper.css';
import SvgIcon from "../../Pages/Svg/SvgIcon";
import 'vue-simple-range-slider/dist/vueSimpleRangeSlider.css'
export default {
    name: "HomeHoopers",
    props: ['data'],
    components:{
        Hooper,
        HooperNavigation,
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
                    100: {
                        itemsToShow: 1,
                        itemsToSlide: 1,
                    },
                    700: {
                        itemsToShow: 3,
                        itemsToSlide: 1,
                    },
                    1000: {
                        itemsToShow: 4,
                        itemsToSlide: 1,
                    },
                    1200: {
                        itemsToShow: 5,
                        itemsToSlide: 1,
                    },
                }
            },
            bookmark: [],
            like: [],
            loadingBookmark : -1,
            loadingLike : -1,
            loadingAdd: -1,
            notificationSystem: {
                options: {
                    show: {
                        icon: "icon-person",
                        position: "topCenter",
                    },
                    success: {
                        position: "bottomRight"
                    },
                    error: {
                        position: "bottomRight"
                    },
                }
            },
            allComparison: [],
            i: 0,
        };
    },
    methods:{
        addCart(id,index){
            this.loadingAdd = index;
            const url = `/add-cart`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data == 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                    this.loadingAdd = -1;
                })
                .catch(err =>{
                    this.loadingAdd = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        btnComparison(id){
            this.i = 0;
            if (this.allComparison.length <= 7){
                for ( this.i ; this.i <  this.allComparison.length; this.i++) {
                    if (this.allComparison[this.i] == id){
                        this.allComparison.splice(this.i , 1);
                        this.i = 100;
                    }
                }
                if (this.i != 101){
                    this.allComparison.push(id);
                }
                this.i = 0;
            }
            this.$eventHub.emit('allComparisons' , this.allComparison);
        },
        checkLike(item){
            this.like = item;
        },
        checkBookmark(item){
            this.bookmark = item;
        },
        btnShowFast(id){
            this.$eventHub.emit('showFast' , id);
        },
        btnLike(id,index){
            this.loadingLike = index;
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.$eventHub.emit('getLike' , []);
                    }else{
                        if (response.data == 'delete'){
                            this.$eventHub.emit('allLike');
                        }else{
                            this.$toast.success('انجام شد', 'به علاقه مندی با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.like.push(response.data.post_id);
                            this.$eventHub.emit('getLike' , this.like);
                        }
                    }
                    this.loadingLike = -1;
                })
            .catch(err =>{
                this.loadingLike = -1;
            })
        },
        btnBookmark(id,index){
            this.loadingBookmark = index;
            const url = `/bookmark`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.$eventHub.emit('getBookmark' , []);
                    }else {
                        if (response.data == 'delete') {
                            this.$eventHub.emit('allBookmark');
                        }else {
                            this.$toast.success('انجام شد', 'نشانه با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.bookmark.push(response.data.post_id);
                            this.$eventHub.emit('getBookmark' , this.bookmark);
                        }
                    }
                    this.loadingBookmark = -1;
                })
            .catch(err => {
                this.loadingBookmark = -1;
            })
        },
        getCompares(item){
            this.allComparison = item;
        }
    },
    created: function() {
        this.$eventHub.on('getLike', this.checkLike);
        this.$eventHub.on('getBookmark', this.checkBookmark);
        this.$eventHub.on('allComparisons', this.getCompares);
    },
}
</script>

<style scoped>

</style>
