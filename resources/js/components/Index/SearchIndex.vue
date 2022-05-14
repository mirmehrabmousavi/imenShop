<template>
    <div class="allSearchAdvance width">
        <div class="searchAdvance" v-if="$page.showSearch == 1">
            <div class="searchAdvanceTitle">
                <span v-if="$i18n.locale == 'fa'">{{$t('advancedSearchSubject')}}</span>
                <span v-if="$i18n.locale == 'en'" class="en">{{$t('advancedSearchSubject')}}</span>
            </div>
            <div class="searchAdvanceContainer">
                <div class="searchAdvanceContainerRight">
                    <div class="searchAdvanceItems">
                        <div class="searchAdvanceItem">
                            <label>{{$t('productName')}} :</label>
                            <input type="text" :placeholder="$t('productName')" v-model="postName">
                        </div>
                        <div class="searchAdvanceItem">
                            <label>{{$t('productID')}} :</label>
                            <input type="text" :placeholder="$t('productID')" v-model="postId">
                        </div>
                        <div class="searchAdvanceItem">
                            <label>{{$t('productDiscounts')}} :</label>
                            <input type="text" :placeholder="$t('productDiscounts')" v-model="postOff">
                        </div>
                    </div>
                    <div class="searchAdvanceItems">
                        <div class="searchAdvanceItem">
                            <label>{{$t('category')}} :</label>
                            <div class="allCategoryPanel">
                                <div class="categoryShow" @click="showTax= !showTax">
                                    <ul>
                                        <li v-if="allCat.length == 0">{{$t('category')}}</li>
                                        <li v-for="(item , index) in allCat">
                                            <i @click.stop="btnClose(index)">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                            <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                            <span v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</span>
                                        </li>
                                    </ul>
                                    <i>
                                        <svg-icon :icon="'#down'"></svg-icon>
                                    </i>
                                </div>
                                <ul v-if="showTax">
                                    <li class="searchUser">
                                        <input v-model="search" type="text" :placeholder="$t('search')" @keyup="btnSearch">
                                    </li>
                                    <VuePerfectScrollbar class="scroll-area">
                                        <li v-for="(item , index) in allTax" @click.stop="sendCat(item)">
                                            <i>
                                                <svg-icon :icon="'#forward'"></svg-icon>
                                            </i>
                                            <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                            <span v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</span>
                                        </li>
                                    </VuePerfectScrollbar>
                                </ul>
                            </div>
                        </div>
                        <div class="searchAdvanceItem">
                            <label>{{$t('brand')}} :</label>
                            <div class="allCategoryPanel">
                                <div class="categoryShow" @click="showTax2= !showTax2">
                                    <ul>
                                        <li v-if="allBrand.length == 0">{{$t('brand')}}</li>
                                        <li v-for="(item , index) in allBrand">
                                            <i @click.stop="btnClose2(index)">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                            <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                            <span v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</span>
                                        </li>
                                    </ul>
                                    <i>
                                        <svg-icon :icon="'#down'"></svg-icon>
                                    </i>
                                </div>
                                <ul v-if="showTax2">
                                    <li class="searchUser">
                                        <input v-model="search2" type="text" :placeholder="$t('brand')" @keyup="btnSearch2">
                                    </li>
                                    <VuePerfectScrollbar class="scroll-area">
                                        <li v-for="(item , index) in allTax2" @click.stop="sendBrand(item)">
                                            <i>
                                                <svg-icon :icon="'#forward'"></svg-icon>
                                            </i>
                                            <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                            <span v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</span>
                                        </li>
                                    </VuePerfectScrollbar>
                                </ul>
                            </div>
                        </div>
                        <div class="searchAdvanceItemRange">
                            <label>{{$t('price')}} :</label>
                            <div class="rangeContainer">
                                <VueSimpleRangeSlider
                                    style="min-width: 100%;"
                                    :max="maxPrice"
                                    :logarithmic="true"
                                    v-model="range"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searchAdvanceContainerLeft">
                    <button @click.stop="btnSearchAdvance">
                        <i v-if="showSearch">
                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                        </i>
                        <i v-else>
                            <svg-icon :icon="'#search'"></svg-icon>
                        </i>
                        {{$t('advancedSearch')}}
                    </button>
                </div>
            </div>
        </div>
        <div class="containerCategories" v-if="searchResult.length">
            <div class="containerCategoriesTitle">
                <label>{{$t('advancedResult')}}</label>
                <div class="moreProduct">
                </div>
            </div>
            <hooper :settings="hooperSettings">
                <slide v-for="(item , index) in searchResult" :key="index" :title="item.title">
                    <inertia-link class="containerCategoriesItem" :href="'/product/' + item.slug">
                        <div class="offProduct" v-if="item.off != null & $i18n.locale == 'fa'">
                                <span>
                                    ٪
                                    <br>
                                    {{item.off}}
                                </span>
                        </div>
                        <div class="offProduct en" v-if="item.off != null & $i18n.locale == 'en'">
                            <span>
                                ٪
                                <br>
                                {{item.off}}
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
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import { Hooper, Slide, Navigation as HooperNavigation, Pagination as HooperPagination,} from 'hooper';
import 'hooper/dist/hooper.css';
import SvgIcon from "../../Pages/Svg/SvgIcon";
import VueSimpleRangeSlider from 'vue-simple-range-slider';
import 'vue-simple-range-slider/dist/vueSimpleRangeSlider.css'
export default {
    name: "SearchIndex",
    props:['minPrice','maxPrice'],
    components:{
        Hooper,
        HooperNavigation,
        HooperPagination,
        Slide,
        VueSimpleRangeSlider,
        SvgIcon,
        VuePerfectScrollbar,
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                transition: 700,
                breakpoints: {
                    100: {
                        itemsToShow: 2,
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
                    1600: {
                        itemsToShow: 6,
                        itemsToSlide: 1,
                    },
                    1800: {
                        itemsToShow: 7,
                        itemsToSlide: 1,
                    },
                }
            },
            range: [this.minPrice,this.maxPrice],
            showTax: false,
            showTax2: false,
            showSearch: false,
            showCompare: false,
            allCat: [],
            allTax: [],
            allBrand: [],
            allTax2: [],
            searchResult: [],
            postName: '',
            postId: '',
            postOff: '',
            search: '',
            search2: '',
            allComparison: [],
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
            i: 0,
        };
    },
    methods:{
        btnSearchAdvance(){
            this.showSearch = true;
            const url = '/search-advance';
            axios.post(url ,{
                postId: this.postId,
                postName: this.postName,
                postOff: this.postOff,
                range: this.range,
                allBrand: this.allBrand,
                allCat: this.allCat,
            })
                .then(response=>{
                    this.searchResult = response.data;
                    this.showSearch = false;
                })
        },
        sendCat(cat){
            this.allCat.push(cat);
        },
        btnClose(index){
            this.allCat.splice(index , 1);
        },
        getCat() {
            this.allTax = this.catPost;
        },
        sendBrand(Brand){
            this.allBrand.push(Brand);
        },
        btnClose2(index){
            this.allBrand.splice(index , 1);
        },
        getBrand() {
            this.allTax2 = this.allBrands;
        },
        btnSearch(){
            const url = '/search-cat';
            axios.post(url ,{
                search: this.search
            })
                .then(response=>{
                    if (this.search === ''){
                        this.allTax = this.catPost
                    }else{
                        this.allTax = response.data;
                    }
                })
        },
        btnSearch2(){
            const url = '/search-brand';
            axios.post(url ,{
                search: this.search2
            })
                .then(response=>{
                    if (this.search2 === ''){
                        this.allTax2 = this.allBrands
                    }else{
                        this.allTax2 = response.data;
                    }
                })
        },
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
