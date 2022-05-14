<template>
    <div class="allSingleIndexItems">
        <div class="allSingleHomeTop">
            <div class="allSingleHomeAddress">
                <inertia-link href="/">{{$t('home')}}</inertia-link>
                <span>/</span>
                <inertia-link v-if="posts.category.length && $i18n.locale == 'fa'" v-for="item in posts.category" :key="item.id" :href="'/archive/category/' + item.slug">{{item.name}} /</inertia-link>
                <inertia-link v-if="posts.category.length && $i18n.locale == 'en'" v-for="item in posts.category" :key="item.id" :href="'/archive/category/' + item.slug">{{item.nameEn}} /</inertia-link>
                <inertia-link v-if="$i18n.locale == 'fa'" :href="'/product/' + posts.slug">{{posts.title}}</inertia-link>
                <inertia-link v-if="$i18n.locale == 'en'" :href="'/product/' + posts.slug">{{posts.titleEn}}</inertia-link>
            </div>
            <div class="allSingleHomeProperties" v-if="$i18n.locale == 'fa'">
                <label>{{$t('productID')}} :</label>
                <span>{{posts.product_id}}</span>
            </div>
            <div class="allSingleHomePropertiesEn" v-if="$i18n.locale == 'en'">
                <label>{{$t('productID')}} :</label>
                <span>{{posts.product_id}}</span>
            </div>
        </div>
        <div class="allSingleHomeInfo">
            <div class="allSingleHomeInfoImages">
                <div class="allSingleHomeInfoImagesTop">
                    <div class="allSingleHomeInfoImagesTopOptions">
                        <div class="allSingleHomeInfoImagesTopOption" v-if="!notificationBell" @click.prevent="showNotification = true" :title="$t('horn')">
                            <i>
                                <svg-icon :icon="'#bell'"></svg-icon>
                            </i>
                        </div>
                        <div class="allSingleHomeInfoImagesTopOption" @click.prevent="showShare = true" :title="$t('share')">
                            <i>
                                <svg-icon :icon="'#share'"></svg-icon>
                            </i>
                        </div>
                        <div class="allSingleHomeInfoImagesTopOption" @click.prevent="btnComparison(posts.id)">
                            <i class="active" v-for="values in allComparison" v-if="values == posts.id">
                                <svg-icon :icon="'#chart'"></svg-icon>
                            </i>
                            <i>
                                <svg-icon :icon="'#chart'"></svg-icon>
                            </i>
                        </div>
                    </div>
                    <div class="allSingleHomeInfoImagesTopPic">
                        <zoom-on-hover :img-normal="JSON.parse(posts.image)[showImage]" :scale="3"></zoom-on-hover>
                    </div>
                </div>
                <hooper :settings="hooperSettings2">
                    <slide v-for="(item,index2) in JSON.parse(posts.image)" :key="index2">
                        <img @click="showImage = index2" :src="item" :alt="item">
                    </slide>
                    <hooper-navigation slot="hooper-addons"></hooper-navigation>
                </hooper>
            </div>
            <div class="allSingleHomeInfoSubject">
                <div class="allSingleHomeInfoSubjectTitle">
                    <h1 v-if="$i18n.locale == 'fa'">
                        {{posts.title}}
                        <span class="original" v-if="!posts.original">غیراصل</span>
                        <span class="used" v-if="posts.used">کارکرده</span>
                    </h1>
                    <h1 class="en" v-if="$i18n.locale == 'en'">
                        {{posts.titleEn}}
                        <span class="original" v-if="!posts.original">Fake</span>
                        <span class="used" v-if="posts.used">Worked</span>
                    </h1>
                </div>
                <div class="allSingleHomeInfoItem">
                    <div class="allSingleHomeInfoItemComment" :title="$t('numberComments')">
                        <i>
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </i>
                        <span>{{posts.comments.length}}</span>
                    </div>
                </div>
                <div class="allSingleHomeInfoSubjectBrand">
                    <div class="allSingleHomeInfoSubjectBrandItem" v-if="posts.brand.length">
                        <label>{{$t('brand')}} :</label>
                        <ul>
                            <li>
                                <inertia-link :href="'/archive/brand/' + posts.brand[0].slug" v-if="$i18n.locale == 'fa'">{{posts.brand[0].name}}</inertia-link>
                                <inertia-link :href="'/archive/brand/' + posts.brand[0].slug" v-if="$i18n.locale == 'en'">{{posts.brand[0].nameEn}}</inertia-link>
                            </li>
                        </ul>
                    </div>
                    <div class="allSingleHomeInfoSubjectBrandItem" v-if="posts.category.length">
                        <label>{{$t('category')}} :</label>
                        <ul>
                            <li>
                                <inertia-link :href="'/archive/category/' + posts.category[0].slug" v-if="$i18n.locale == 'fa'">{{posts.category[0].name}}</inertia-link>
                                <inertia-link :href="'/archive/category/' + posts.category[0].slug" v-if="$i18n.locale == 'en'">{{posts.category[0].nameEn}}</inertia-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="allSingleHomeInfoSubjectColor" v-if="JSON.parse(posts.review[0].colors).length">
                    <i>
                        <svg-icon :icon="'#color'"></svg-icon>
                    </i>
                    <label>{{$t('colors')}} :</label>
                    <ul>
                        <li v-for="(item , index) in JSON.parse(posts.review[0].colors)" @click="btnColor(item,index)">
                            <div class="active" v-if="showColor == index">
                                <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                <i>
                                    <svg-icon :icon="'#circle'" :style="{'fill' : item.color}"></svg-icon>
                                </i>
                            </div>
                            <div class="unActive" v-if="showColor != index">
                                <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                <i>
                                    <svg-icon :icon="'#circle'" :style="{'fill' : item.color}"></svg-icon>
                                </i>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="allSingleHomeInfoSubjectSize" v-if="JSON.parse(posts.review[0].size).length">
                    <i>
                        <svg-icon :icon="'#sizeFront'"></svg-icon>
                    </i>
                    <label>{{$t('sizes')}} :</label>
                    <div class="allCategoryPanel">
                        <div class="categoryShow" @click="showSize = !showSize">
                            <h4>{{sizeName.name}}</h4>
                            <i>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <ul v-if="showSize">
                            <VuePerfectScrollbar class="scroll-area">
                                <li v-for="item in JSON.parse(posts.review[0].size)" v-if="item.count >= 0" :key="item.name" :title="item.name" @click.prevent="btnSize(item)">
                                    <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                    <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                </li>
                            </VuePerfectScrollbar>
                        </ul>
                    </div>
                </div>
                <div class="allSingleHomeInfoSubjectAbility" v-if="JSON.parse(posts.review[0].ability).length">
                    <label>
                        <i>
                            <svg-icon :icon="'#ability'"></svg-icon>
                        </i>
                        {{$t('productfeatures')}}
                    </label>
                    <ul>
                        <li v-for="(item , check) in JSON.parse(posts.review[0].ability)" :key="check">
                            <i>
                                <svg-icon :icon="'#checked'"></svg-icon>
                            </i>
                            <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                            <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                        </li>
                    </ul>
                </div>
                <div class="allSingleHomePrice" v-if="count == 1">
                    <div class="allSingleHomePriceNext">
                        <i v-if="showLoaderOption == 1">
                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                        </i>
                        <i v-else>
                            <svg-icon :icon="'#cart'"></svg-icon>
                        </i>
                        <button @click.prevent="addCart">{{$t('addCart')}}</button>
                    </div>
                    <div class="allSingleHomePriceCounts" v-if="posts.count <= 5">
                        <div class="allSingleHomePriceCount">
                            <div class="abilityPostToolTip">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p v-if="$i18n.locale == 'fa'">فقط {{posts.count}} محصول باقی مانده</p>
                                <p class="en" v-if="$i18n.locale == 'en'">Only {{posts.count}} product left</p>
                            </div>
                        </div>
                    </div>
                    <div class="allSingleHomePriceCounts" v-else-if="colorNames || sizeName">
                        <div class="allSingleHomePriceCount" v-if="colorNames.count <= 5 || sizeName.count <= 5">
                            <div class="abilityPostToolTip" v-if="$i18n.locale == 'fa'">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p v-if="colorNames.count <= 5">فقط {{colorNames.count}} محصول با رنگ {{colorNames.name}} باقی مانده</p>
                                <p v-else-if="sizeName.count <= 5">فقط {{sizeName.count}} محصول با سایز {{sizeName.name}} باقی مانده</p>
                            </div>
                            <div class="abilityPostToolTip en" v-if="$i18n.locale == 'en'">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p v-if="colorNames.count <= 5">Only {{colorNames.count}} product with color {{colorNames.name}} left</p>
                                <p v-else-if="sizeName.count <= 5">Only {{sizeName.count}} product with size {{sizeName.name}} left</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allSingleHomeInfoPriceCheck2" v-else>
                    <span>{{$t('notAvailable')}}</span>
                </div>
                <div class="reportSingle" v-if="!feedbackReport">
                    <div class="reportSingleItem" @click="showReport = !showReport">
                        <i>
                            <svg-icon :icon="'#report'"></svg-icon>
                        </i>
                        <span>{{$t('feedback')}}</span>
                    </div>
                </div>
            </div>
            <div class="allSingleHomeInfoOptions">
                <div class="allSingleHomeInfoTime" v-if="posts.suggest">
                    <div class="allSingleHomeInfoTimeText">
                        <h4>{{$t('offers')}}</h4>
                        <h5>{{$t('end')}}</h5>
                    </div>
                    <div class="allSingleHomeInfoTimeItem" v-if="$i18n.locale == 'fa'">
                        <flip-countdown :deadline="posts.suggest"></flip-countdown>
                    </div>
                    <div class="allSingleHomeInfoTimeItem en" v-if="$i18n.locale == 'en'">
                        <flip-countdown :deadline="posts.suggest"></flip-countdown>
                    </div>
                </div>
                <div class="allSingleHomeInfoOptionPrice">
                    <h4>{{$t('price')}} :</h4>
                    <h5 v-if="$i18n.locale == 'fa'">
                        {{price|NumFormat}}
                        <span>تومان</span>
                    </h5>
                    <h5 v-if="$i18n.locale == 'en'" class="en">
                        {{price|NumFormat}}
                        <span>toman</span>
                    </h5>
                </div>
                <div class="allSingleHomeInfoRates">
                    <div class="allSingleHomeInfoItemRate" :title="$t('rateProduct')">
                        {{$t('rateProduct')}} :
                        <span>
                            {{ allRate }}
                            ({{ countRate }})
                        </span>
                    </div>
                    <div class="rateItems" :title="$t('rate')">
                        <div class="rateItem" @click="btnRate('1')">
                            <i v-if="finalRate > 0">
                                <svg-icon :icon="'#star'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#unStar'"></svg-icon>
                            </i>
                        </div>
                        <div class="rateItem" @click="btnRate('2')">
                            <i v-if="finalRate > 1">
                                <svg-icon :icon="'#star'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#unStar'"></svg-icon>
                            </i>
                        </div>
                        <div class="rateItem" @click="btnRate(3)">
                            <i v-if="finalRate > 2">
                                <svg-icon :icon="'#star'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#unStar'"></svg-icon>
                            </i>
                        </div>
                        <div class="rateItem" @click="btnRate('4')">
                            <i v-if="finalRate > 3">
                                <svg-icon :icon="'#star'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#unStar'"></svg-icon>
                            </i>
                        </div>
                        <div class="rateItem" @click="btnRate('5')">
                            <i v-if="finalRate > 4">
                                <svg-icon :icon="'#star'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#unStar'"></svg-icon>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="allSingleHomeInfoDatas">
                    <div class="allSingleHomeInfoData" :title="$t('mySigns')" @click.stop="btnBookmark(posts.id)">
                        <h5>{{$t('bookmark')}} :</h5>
                        <div class="allSingleHomeInfoDataItem" v-if="showLoaderOption != 2">
                            <i v-for="values in bookmark" v-if="values == posts.id">
                                <svg-icon :icon="'#bookmark'"></svg-icon>
                            </i>
                            <i>
                                <svg-icon :icon="'#unbookmark'"></svg-icon>
                            </i>
                        </div>
                        <i v-else>
                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                        </i>
                    </div>
                    <div class="allSingleHomeInfoData" :title="$t('favorites')" @click.stop="btnLike(posts.id)">
                        <h5>{{$t('like')}} :</h5>
                        <div class="allSingleHomeInfoDataItem" v-if="showLoaderOption != 3">
                            <i v-for="values in like" v-if="values == posts.id">
                                <svg-icon :icon="'#like'"></svg-icon>
                            </i>
                            <i>
                                <svg-icon :icon="'#unlike'"></svg-icon>
                            </i>
                        </div>
                        <i v-else>
                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                        </i>
                    </div>
                </div>
                <div class="allSingleHomeInfoGuarantee" v-if="getGuarantee != ''" @click="showGuarantee = !showGuarantee">
                    <i>
                        <svg-icon :icon="'#security'"></svg-icon>
                    </i>
                    <span v-if="$i18n.locale == 'fa'">{{getGuarantee.name}}</span>
                    <span v-if="$i18n.locale == 'en'" class="en">{{getGuarantee.nameEn}}</span>
                    <i>
                        <svg-icon v-if="showGuarantee" class="active" :icon="'#down'"></svg-icon>
                        <svg-icon v-else :icon="'#down'"></svg-icon>
                    </i>
                    <div class="allSingleHomeOptionItems" v-if="posts.guarantee.length && showGuarantee">
                        <div class="allSingleHomeOptionItem" v-for="(item,index) in posts.guarantee">
                            <label :for="index" @click="getGuarantee = item">
                                <input :id="index" type="checkbox" v-if="getGuarantee.id == item.id" checked>
                                <input :id="index" type="checkbox" v-else unchecked>
                                <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="allSingleHomeInfoOption">
                    <i>
                        <svg-icon :icon="'#available'"></svg-icon>
                    </i>
                    <span v-if="count == 1">{{$t('availableStock')}}</span>
                    <span v-else>{{$t('notAvailableStock')}}</span>
                </div>
                <div class="allSingleHomeInfoOption" v-if="!posts.suggest">
                    <i>
                        <svg-icon :icon="'#sizeFront'"></svg-icon>
                    </i>

                    <span v-if="sizeName.name">
                                <span v-if="$i18n.locale == 'fa'">{{sizeName.name}}</span>
                                <span class="en" v-if="$i18n.locale == 'en'">{{sizeName.nameEn}}</span>
                            </span>
                    <span v-else>{{$t('unknown')}}</span>
                </div>
                <div class="allSingleHomeInfoOption" v-if="!posts.suggest">
                    <i>
                        <svg-icon :icon="'#color'"></svg-icon>
                    </i>
                    <span v-if="colorNames.name">
                                <span v-if="$i18n.locale == 'fa'">{{colorNames.name}}</span>
                                <span class="en" v-if="$i18n.locale == 'en'">{{colorNames.nameEn}}</span>
                            </span>
                    <span v-else>{{$t('unknown')}}</span>
                </div>
                <div class="scoreSingle" v-if="posts.score >= 1">
                    <div class="rightScore">
                        <i>
                            <svg-icon :icon="'#score'"></svg-icon>
                        </i>
                        <span>{{$t('amountScore')}}</span>
                    </div>
                    <span>{{posts.score}} {{$t('score')}}</span>
                </div>
            </div>
        </div>
        <div class="reportsSingle" v-if="showReport">
            <report-single v-on:sendClose="getCloseFeed" :post="posts.id"></report-single>
        </div>
        <div class="showSingleItem" v-if="showShare">
            <all-share v-on:sendClose="getClose" :slug="posts.product_id"></all-share>
        </div>
        <div class="showSingleItem" v-if="showNotification">
            <single-notification v-on:sendClose="getCloseNotification" :post="posts.id"></single-notification>
        </div>
    </div>
</template>

<script>
import FlipCountdown from "vue2-flip-countdown";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import ReportSingle from "./ReportSingle";
import SingleNotification from "./SingleNotification";
import AllCompare from "../../Pages/Home/AllCompare";
import AllShare from "./AllShare";
import { Hooper, Navigation as HooperNavigation} from 'hooper';
import 'hooper/dist/hooper.css';
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "SingleTop1",
    props:['posts','feedback','notification'],
    components:{
        SvgIcon,
        FlipCountdown,
        VuePerfectScrollbar,
        Hooper,
        HooperNavigation,
        ReportSingle,
        SingleNotification,
        AllCompare,
        AllShare,
    },
    data(){
        return{
            hooperSettings2: {
                wheelControl:false,
                centerMode: false,
                transition: 700,
                breakpoints: {
                    100: {
                        itemsToShow: 3,
                        itemsToSlide: 3,
                    },
                }
            },
            colors : [],
            bookmark : [],
            like : [],
            rate : [],
            finalRate : 0,
            allRate : 0,
            showShare:false,
            countRate : 0,
            showColor : 0,
            feedbackReport: this.feedback,
            notificationBell: this.notification,
            allComparison: [],
            showSize : false,
            showReport: false,
            showGuarantee:false,
            colorNames : [],
            price: this.posts.price,
            sizeName : [],
            showFast: [],
            postCompares: '',
            getGuarantee: '',
            showImage:0,
            loadingLike: -1,
            loadingBookmark: -1,
            showNotification: false,
            showLoaderOption : 0,
            count: 1,
            i:0,
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
        }
    },
    methods:{
        getCloseNotification(item){
            if(item){
                this.notificationBell = item;
            }
            this.showNotification = false;
        },
        getCloseFeed(item){
            this.showReport = false;
            if(item){
                this.feedbackReport = item;
            }
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
        getClose(item){
            this.showShare = false;
            this.showQuestion = false;
        },
        btnColor(item,index){
            this.showColor = index;
            this.colorNames = item;
            if (this.sizeName != ''){
                this.price = parseInt(this.posts.price) + parseInt(this.sizeName.price) + parseInt(item.price);
                if(item.count <= 0 || this.sizeName.count <= 0 || this.posts.count <= 0){
                    this.count = 0;
                }else{
                    this.count = 1;
                }
            }else{
                this.price = parseInt(this.posts.price) + parseInt(item.price);
                if(item.count <= 0 || this.posts.count <= 0){
                    this.count = 0;
                }else{
                    this.count = 1;
                }
            }
        },
        btnSize(item){
            this.showSize = false;
            this.sizeName = item;
            if (this.colorNames != ''){
                this.price = parseInt(this.posts.price) + parseInt(this.colorNames.price) + parseInt(item.price);
                if(item.count <= 0 || this.colorNames.count <= 0 || this.posts.count <= 0){
                    this.count = 0;
                }else{
                    this.count = 1;
                }
            }else{
                this.price = parseInt(this.posts.price) + parseInt(item.price);
                if(item.count <= 0 || this.posts.count <= 0){
                    this.count = 0;
                }else{
                    this.count = 1;
                }
            }
        },
        addCart(){
            this.showLoaderOption = 1;
            const url = `/add-cart2`;
            axios.post(url ,{
                postID : this.posts.id,
                colorName : this.colorNames,
                sizeName : this.sizeName,
                price: this.price,
                guarantee: this.getGuarantee.id,
            })
                .then(response=>{
                    this.showLoaderOption = 0;
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data == 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                })
                .catch(err =>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        btnShowFast(id){
            this.$eventHub.emit('showFast' , id);
        },
        btnLike(id , index){
            this.loadingLike = index;
            this.showLoaderOption = 3;
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    this.showLoaderOption = 0;
                    this.loadingLike = -1;
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.like = [];
                    }else{
                        if (response.data == 'delete'){
                            this.$toast.success('انجام شد', 'علاقه مندی با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.getLike();
                        }else{
                            this.$toast.success('انجام شد', 'به علاقه مندی با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.getLike();
                        }
                    }
                })
                .catch(err =>{
                    this.showLoaderOption = 0;
                    this.loadingLike = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        getLike(){
            const url = `/get-like`;
            axios.get(url)
                .then(response=>{
                    this.like = response.data;
                })
        },
        btnBookmark(id , index){
            this.loadingBookmark = index;
            this.showLoaderOption = 2;
            const url = `/bookmark`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    this.loadingBookmark = -1;
                    this.showLoaderOption = 0;
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.bookmark = [];
                    }else {
                        if (response.data == 'delete') {
                            this.$toast.success('انجام شد', 'نشانه با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.getBookmark();
                        }else {
                            this.$toast.success('انجام شد', 'نشانه با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.bookmark.push(response.data.post_id);
                        }
                    }
                })
        },
        getBookmark(){
            const url = `/get-bookmark`;
            axios.get(url)
                .then(response=>{
                    this.bookmark = response.data;
                })
        },
        btnRate(index){
            const url = `/rate`;
            axios.post(url ,{
                post_id : this.posts.id,
                rate_post : index,
            })
                .then(response=>{
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }
                    if (response.data == 'found'){

                    }
                    if (response.data != 'found' && response.data != 'noUser'){
                        this.$toast.success('انجام شد', 'رای با موفقیت ثبت شد', this.notificationSystem.options.success);
                        this.finalRate = response.data[0];
                        this.allRate = response.data[1];
                        this.countRate = response.data[2];
                    }
                })
        },
        getRate(){
            const url = '/get-rate';
            axios.post(url ,{
                post_id : this.posts.id,
            })
                .then(response=>{
                    this.finalRate = response.data[0];
                    this.allRate = response.data[1];
                    this.countRate = response.data[2];
                })
        },
        getData(){
            if(this.posts.body == null && this.posts.review[0].body == null){
                this.tab = 1;
            }
            if(this.posts.guarantee.length){
                this.getGuarantee = this.posts.guarantee[0];
            }
            if (JSON.parse(this.posts.review[0].size).length){
                this.sizeName = JSON.parse(this.posts.review[0].size)[0];
                this.price = parseInt(this.price) + parseInt(JSON.parse(this.posts.review[0].size)[0].price);
                if(this.sizeName.count <= 0){
                    this.count = 0;
                }
            }
            if (JSON.parse(this.posts.review[0].colors).length){
                this.colorNames = JSON.parse(this.posts.review[0].colors)[0];
                this.price = parseInt(this.price) + parseInt(JSON.parse(this.posts.review[0].colors)[0].price);
                if(this.colorNames.count <= 0){
                    this.count = 0;
                }
            }
            if(this.posts.count <= 0){
                this.count = 0;
            }
        },
        getCompares(item){
            this.allComparison = item;
        }
    },
    mounted() {
        this.getData();
        this.getBookmark();
        this.getLike();
        this.getRate();
    },
    created: function() {
        this.$eventHub.on('allComparisons', this.getCompares);
    },
}
</script>

<style scoped>

</style>
