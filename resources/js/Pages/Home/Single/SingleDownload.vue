<template>
    <home-layout>
        <div class="allSingleDownload width">
            <div class="address">
                <inertia-link href="/">{{ $t('home') }}</inertia-link>
                <inertia-link :href="'/archive/category/'+posts.category[0].slug" v-if="$i18n.locale == 'fa'">{{ posts.category[0].name }}</inertia-link>
                <inertia-link :href="'/archive/category/'+posts.category[0].slug" v-if="$i18n.locale == 'en'">{{ posts.category[0].nameEn }}</inertia-link>
                <inertia-link :href="'/download-product/' + posts.slug" v-if="$i18n.locale == 'fa'">{{ posts.title }}</inertia-link>
                <inertia-link :href="'/download-product/' + posts.slug" v-if="$i18n.locale == 'en'">{{ posts.titleEn }}</inertia-link>
            </div>
            <h1 v-if="$i18n.locale == 'fa'">{{posts.title}}</h1>
            <h1 v-if="$i18n.locale == 'en'">{{posts.titleEn}}</h1>
            <div class="singleTop">
                <div class="right">
                    <div class="rightItems">
                        <div class="pic">
                            <div class="image">
                                <figure>
                                    <img :src="JSON.parse(posts.image)[0]" :alt="posts.title">
                                </figure>
                            </div>
                        </div>
                    </div>
                    <div class="tabs">
                        <div class="tab">
                            <a @click="tab = 0" class="active" v-if="tab == 0">{{ $t('review') }}</a>
                            <a @click="tab = 0" v-else>{{ $t('review') }}</a>
                        </div>
                        <div class="tab">
                            <a @click="tab = 1" class="active" v-if="tab == 1">
                                {{ $t('userComments') }}
                            </a>
                            <a @click="tab = 1" v-else>
                                {{ $t('userComments') }}
                                <span v-if="$i18n.locale == 'fa'">{{posts.comments_count}}</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{posts.comments_count}}</span>
                            </a>
                        </div>
                        <div class="tab">
                            <a @click="tab = 2" class="active" v-if="tab == 2">
                                {{ $t('qa') }}
                            </a>
                            <a @click="tab = 2" v-else>
                                {{ $t('qa') }}
                                <span v-if="$i18n.locale == 'fa'">{{posts.question_count}}</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{posts.question_count}}</span>
                            </a>
                        </div>
                    </div>
                    <div class="review" v-if="tab == 0">
                        <p v-html="posts.review[0].body" v-if="$i18n.locale == 'fa'"></p>
                        <p v-html="posts.review[0].bodyEn" v-if="$i18n.locale == 'en'" class="en"></p>
                    </div>
                    <div class="comment" v-if="tab == 1">
                        <all-comment :posts="posts" :rate="JSON.parse(posts.review[0].rate)" :replyAllow="reply" :showUser="showUser"></all-comment>
                    </div>
                    <div class="allSingleQuestion" v-if="tab == 2">
                        <div class="allSingleQuestionSends">
                            <div class="allSingleQuestionSend">
                                <i>
                                    <svg-icon :icon="'#question'"></svg-icon>
                                </i>
                                <p>{{$t('sendQuestionDescription')}}</p>
                                <h3 @click="btnShowQuestion(0)">{{$t('sendQuestion')}}</h3>
                            </div>
                        </div>
                        <div class="allSingleQuestionBody">
                            <div class="allSingleQuestionItems" v-if="posts.question.length">
                                <div class="allSingleQuestionItem" v-for="item in posts.question">
                                    <div class="allSingleQuestionTitle">
                                        <i>
                                            <svg-icon :icon="'#question'"></svg-icon>
                                        </i>
                                        <p>{{ item.body}}</p>
                                    </div>
                                    <div class="allSingleQuestionAnswers">
                                        <div class="allSingleQuestionAnswer" v-for="answer in item.questions">
                                            <h4>{{$t('reply')}} :</h4>
                                            <p>{{ answer.body }}</p>
                                        </div>
                                    </div>
                                    <div class="allSingleQuestionItemSend" @click="btnShowQuestion(item.id)">
                                        <span>{{$t('sendResponse')}}</span>
                                        <i>
                                            <svg-icon :icon="'#left'"></svg-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                            <div class="allSingleQuestionEmpty" v-else>
                                <i>
                                    <svg-icon :icon="'#question'"></svg-icon>
                                </i>
                                <p>{{$t('emptyQuestion')}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="related">
                        <div class="allFileIndex" v-if="related.length">
                            <div class="allFileTitle">
                                <h3>{{$t('relatedProducts')}}</h3>
                            </div>
                            <hooper :settings="hooperSettings">
                                <slide v-for="(item,index) in related" :key="index">
                                    <inertia-link class="fileItem" :href="'/download-product/' + item.slug" :title="item.title">
                                        <div class="pic">
                                            <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                        </div>
                                        <div class="titleTop">
                                            <i>
                                                <svg-icon :icon="'#link'"></svg-icon>
                                            </i>
                                            <div class="title" v-if="$i18n.locale == 'fa'">
                                                <h3>{{ item.title }}</h3>
                                                <h4 v-if="item.category.length">{{ item.category[0].name }}</h4>
                                            </div>
                                            <div class="title" v-if="$i18n.locale == 'en'">
                                                <h3>{{ item.titleEn }}</h3>
                                                <h4 v-if="item.category.length">{{ item.category[0].nameEn }}</h4>
                                            </div>
                                        </div>
                                        <p v-if="$i18n.locale == 'fa'">{{item.body}}</p>
                                        <p v-if="$i18n.locale == 'en'">{{item.bodyEn}}</p>
                                        <div class="options">
                                            <div class="option">
                                                <span v-if="$i18n.locale == 'fa'">{{ item.price|NumFormat }}</span>
                                                <span v-if="$i18n.locale == 'en'" class="en">{{ item.price|NumFormat }}</span>
                                                <span>{{ $t('arz') }}</span>
                                            </div>
                                            <div class="option">
                                                <i>
                                                    <svg-icon :icon="'#left'"></svg-icon>
                                                </i>
                                                <h4 v-if="$i18n.locale == 'fa'">اطلاعات بیشتر</h4>
                                                <h4 v-if="$i18n.locale == 'en'">more info</h4>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </slide>
                                <hooper-navigation slot="hooper-addons"></hooper-navigation>
                            </hooper>
                        </div>
                    </div>
                </div>
                <div class="left">
                    <div class="detail">
                        <div class="detailPrice">
                            <div class="item">
                                <h4>{{ $t('numberPurchases') }}</h4>
                                <h5 v-if="$i18n.locale == 'fa'">{{ posts.pay_meta_count }}</h5>
                                <h5 v-if="$i18n.locale == 'en'" class="en">{{ posts.pay_meta_count }}</h5>
                            </div>
                            <div class="item">
                                <h4 v-if="$i18n.locale == 'fa'">{{$t('price')}}</h4>
                                <h4 v-if="$i18n.locale == 'en'" class="en">{{$t('price')}}</h4>
                                <h5 v-if="$i18n.locale == 'fa'">
                                    {{ posts.price|NumFormat }}
                                    <span>{{ $t('arz') }}</span>
                                </h5>
                                <h5 v-if="$i18n.locale == 'en'" class="en">
                                    {{ posts.price|NumFormat }}
                                    <span>{{ $t('arz') }}</span>
                                </h5>
                            </div>
                        </div>
                        <div class="rates">
                            <div class="top">
                            <span v-if="$i18n.locale == 'fa'">
                                امتیاز خریداران از
                                <span>{{countRate}}</span>
                                رای :
                            </span>
                            <span v-if="$i18n.locale == 'en'" class="en">
                                Buyers rating from
                                <span> {{countRate}} </span>
                                Vote :
                            </span>
                                <h4 v-if="$i18n.locale == 'fa'">
                                    {{allRate}}
                                    <span>/ 5</span>
                                </h4>
                                <h4 v-if="$i18n.locale == 'en'" class="en">
                                    {{allRate}}
                                    <span>/ 5</span>
                                </h4>
                            </div>
                            <div class="container">
                                <div class="feedback">
                                    <div class="rating">
                                        <input type="radio" value="5" id="5" v-model="rating">
                                        <label @click="btnRate(5)" :for="rating"></label>
                                        <input type="radio" value="4" id="4" v-model="rating">
                                        <label @click="btnRate(4)" :for="rating"></label>
                                        <input type="radio" value="3" id="3" v-model="rating">
                                        <label @click="btnRate(3)" :for="rating"></label>
                                        <input type="radio" value="2" id="2" v-model="rating">
                                        <label @click="btnRate(2)" :for="rating"></label>
                                        <input type="radio" value="1" id="1" v-model="rating">
                                        <label @click="btnRate(1)" :for="rating"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="addCart">
                        <div class="addItems" v-if="user">
                            <a v-if="paid" :href="'/product/'+posts.slug+'/download'">
                                <svg-icon :icon="'#free'"></svg-icon>
                                <span v-if="$i18n.locale == 'fa'">دانلود محصول</span>
                                <span v-if="$i18n.locale == 'en'">Download the product</span>
                            </a>
                            <a v-else :href="'/download/shop?product='+posts.id">
                                <svg-icon :icon="'#free'"></svg-icon>
                                <span v-if="$i18n.locale == 'fa'">خرید مستقیم محصول</span>
                                <span v-if="$i18n.locale == 'en'">Buy the product directly</span>
                            </a>
                        </div>
                        <a href="/login" v-else>
                            <svg-icon :icon="'#free'"></svg-icon>
                            <span v-if="$i18n.locale == 'fa'">ابتدا عضو شوید</span>
                            <span v-if="$i18n.locale == 'en'">Register first</span>
                        </a>
                        <p v-if="$i18n.locale == 'fa'">این محصول را به لیست علاقه مندیتان اضافه کنین تا بعدا آن را خرید کنید</p>
                        <p v-if="$i18n.locale == 'en'">Add this product to your favorites list to buy later</p>
                        <div class="options">
                            <div class="option">
                                <h3>{{ $t('offerOthers') }}</h3>
                                <i v-if="showLoaderOption != 3" @click.stop="btnLike(posts.id)">
                                    <i v-for="values in like" v-if="values == posts.id">
                                        <svg-icon :icon="'#like'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#unlike'"></svg-icon>
                                    </i>
                                </i>
                                <i v-else>
                                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                </i>
                            </div>
                            <div class="option">
                                <h3>{{ $t('saveYourself') }}</h3>
                                <i v-if="showLoaderOption != 2" @click.stop="btnBookmark(posts.id)">
                                    <i v-for="values in bookmark" v-if="values == posts.id">
                                        <svg-icon :icon="'#bookmark'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#unbookmark'"></svg-icon>
                                    </i>
                                </i>
                                <i v-else>
                                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="description">
                        <div class="item">
                            <h4>{{$t('productID')}} :</h4>
                            <span v-if="$i18n.locale == 'fa'">{{ posts.product_id }}</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{ posts.product_id }}</span>
                        </div>
                        <div class="item">
                            <h4>{{ $t('dateRegistration') }} :</h4>
                            <span v-if="$i18n.locale == 'fa'">{{ posts.created_at }}</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{ posts.created_at }}</span>
                        </div>
                        <div class="item">
                            <h4>{{ $t('category') }} :</h4>
                            <inertia-link v-for="(item,index2) in posts.category" :key="index2" :href="'/archive/category/' + item.slug">
                                <span v-if="$i18n.locale == 'fa'">{{ item.name }}</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{ item.nameEn }}</span>
                            </inertia-link>
                        </div>
                    </div>
                    <div class="property" v-if="JSON.parse(posts.review[0].rate).length">
                        <div class="propertyTitle">{{ $t('productReview') }} :</div>
                        <div class="item" v-for="(item,index) in JSON.parse(posts.review[0].rate)" :key="index" :title="item.title">
                            <div class="itemTitle">
                                <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                <span v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                <span v-if="$i18n.locale == 'fa'">{{item.rate*25}}%</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{item.rate*25}}%</span>
                            </div>
                            <div class="datas">
                                <div class="data" :style="'width:'+item.rate*25+'%;'"></div>
                            </div>
                        </div>
                    </div>
                    <div class="checkHeight" :style="styleHeader" :class="{ 'navbar--hidden': !showNavbar }">
                        <div class="addCart">
                            <div class="addItems" v-if="user">
                                <a v-if="paid" :href="'/product/'+posts.slug+'/download'">
                                    <svg-icon :icon="'#free'"></svg-icon>
                                    <span v-if="$i18n.locale == 'fa'">دانلود محصول</span>
                                    <span v-if="$i18n.locale == 'en'">Download the product</span>
                                </a>
                                <a v-else :href="'/download/shop?product='+posts.id">
                                    <svg-icon :icon="'#free'"></svg-icon>
                                    <span v-if="$i18n.locale == 'fa'">خرید مستقیم محصول</span>
                                    <span v-if="$i18n.locale == 'en'">Buy the product directly</span>
                                </a>
                            </div>
                            <a href="/login" v-else>
                                <svg-icon :icon="'#free'"></svg-icon>
                                <span v-if="$i18n.locale == 'fa'">ابتدا عضو شوید</span>
                                <span v-if="$i18n.locale == 'en'">Register first</span>
                            </a>
                            <p v-if="$i18n.locale == 'fa'">این محصول را به لیست علاقه مندیتان اضافه کنین تا بعدا آن را خرید کنید</p>
                            <p v-if="$i18n.locale == 'en'">Add this product to your favorites list to buy later</p>
                            <div class="options">
                                <div class="option">
                                    <h3>{{ $t('offerOthers') }}</h3>
                                    <i v-if="showLoaderOption != 3" @click.stop="btnLike(posts.id)">
                                        <i v-for="values in like" v-if="values == posts.id">
                                            <svg-icon :icon="'#like'"></svg-icon>
                                        </i>
                                        <i>
                                            <svg-icon :icon="'#unlike'"></svg-icon>
                                        </i>
                                    </i>
                                    <i v-else>
                                        <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                    </i>
                                </div>
                                <div class="option">
                                    <h3>{{ $t('saveYourself') }}</h3>
                                    <i v-if="showLoaderOption != 2" @click.stop="btnBookmark(posts.id)">
                                        <i v-for="values in bookmark" v-if="values == posts.id">
                                            <svg-icon :icon="'#bookmark'"></svg-icon>
                                        </i>
                                        <i>
                                            <svg-icon :icon="'#unbookmark'"></svg-icon>
                                        </i>
                                    </i>
                                    <i v-else>
                                        <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="showSingleItem" v-if="showQuestion">
                <all-question v-on:sendClose="getClose" :parentId="parentId" :post="posts.id"></all-question>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import AllComment from "../../../components/Single/AllComment";
import {Hooper, Navigation as HooperNavigation, Slide} from "hooper";
import HomeLayout from "../../../components/layout/HomeLayout";
import AllQuestion from "../../../components/Single/AllQuestion";
import 'hooper/dist/hooper.css';
export default {
    name: "SingleDownload",
    props:['posts','checkUser','related' , 'paid' ,'possibilities','rates','reply','showUser','showComment'],
    components: {
        AllQuestion,
        HomeLayout,
        AllComment,
        Hooper,
        HooperNavigation,
        Slide,
        SvgIcon,
    },
    data() {
        return{
            rating: 0,
            showNavbar: false,
            lastScrollPosition: 0,
            showLoaderOption: 0,
            bookmark:[],
            like:[],
            allRate: 0,
            countRate: 0,
            tab: 0,
            user: this.$page.userData,
            parentId:0,
            showQuestion: false,
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
                        itemsToShow: 2,
                        itemsToSlide: 1,
                    },
                    1000: {
                        itemsToShow: 3,
                        itemsToSlide: 1,
                    },
                    1800: {
                        itemsToShow: 4,
                        itemsToSlide: 1,
                    },
                }
            },
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
            styleHeader : {
                visibility: 'hidden',
                opacity: '0',
            },
        }
    },
    beforeDestroy () {
        window.removeEventListener('scroll', this.onScroll)
    },
    methods:{
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
                        this.rating = response.data[0];
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
                    this.rating = response.data[0];
                    this.allRate = response.data[1];
                    this.countRate = response.data[2];
                })
        },
        btnShowQuestion(id){
            this.parentId = id;
            this.showQuestion = true;
        },
        allViews(){
            const url = "/view";
            axios.post(url,{
                postId : this.posts.id
            })
        },
        getClose(){
            this.showQuestion = false;
            this.showShare = false;
        },
        onScroll () {
            const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop
            if (currentScrollPosition < 0) {
                return
            }  // Stop executing this function if the difference between
            // current scroll position and last scroll position is less than some offset
            if (Math.abs(currentScrollPosition - this.lastScrollPosition) < 60) {
                return
            }
            this.showNavbar = currentScrollPosition >= 843;
            if(currentScrollPosition >= 843){
                this.styleHeader = {
                    visibility: 'visible',
                    opacity: '1',
                };
            }else{
                this.styleHeader = {
                    visibility: 'hidden',
                    opacity: '0',
                };
            }
            if (window.innerWidth <= 700) {
                this.styleHeader = {
                    visibility: 'visible',
                    opacity: '1',
                };
            }
        },
    },
    mounted() {
        window.addEventListener('scroll', this.onScroll);
        this.getLike();
        this.getBookmark();
        this.allViews();
        this.getRate();
    },
}
</script>

<style scoped>

</style>
