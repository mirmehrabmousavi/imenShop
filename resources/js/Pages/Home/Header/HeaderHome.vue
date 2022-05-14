<template>
    <div class="allHeaderHome" :style="styleHeader" :class="{ 'navbar--hidden': !showNavbar }">
        <div class="headerHomeContent width">
            <div class="headerHomeContentTop">
                <div class="headerHomeContentTopItem">
                    <div class="headerHomeContentLogo">
                        <inertia-link href="/">
                            <img :src="$page.logo" :alt="$page.logo">
                        </inertia-link>
                    </div>
                    <div class="headerHomeContentSearch">
                        <label>
                            <i>
                                <svg-icon :icon="'#search'"></svg-icon>
                            </i>
                            <input type="search" v-model="searchProduct" @keydown="searchDataHandler" id="searching" @keypress.enter="btnSearchProduct" :placeholder="$t('search')">
                            <i class="load" v-if="searchLoader">
                                <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                            </i>
                        </label>
                        <div class="searchLists" v-if="allResult.length">
                            <div class="searchList">
                                <div class="item" v-for="item in allResult">
                                    <div class="pic">
                                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                    </div>
                                    <div class="subject" v-if="item.type == 0">
                                        <inertia-link :href="'/product/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.title}}</inertia-link>
                                        <inertia-link :href="'/product/' + item.slug" v-if="$i18n.locale == 'en'">{{item.titleEn}}</inertia-link>
                                    </div>
                                    <div class="subject" v-if="item.type == 1">
                                        <inertia-link :href="'/download-product/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.title}}</inertia-link>
                                        <inertia-link :href="'/download-product/' + item.slug" v-if="$i18n.locale == 'en'">{{item.titleEn}}</inertia-link>
                                    </div>
                                </div>
                            </div>
                            <ul v-if="$page.catList.length">
                                <li v-for="cat in $page.catList.slice(0,3)">
                                    <inertia-link :href="'/archive/category/'+cat.slug">{{cat.name}}</inertia-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="headerHomeContentTopItem">
                    <div class="headerHomeContentBotCats">
                        <i @click.stop="showCats = !showCats">
                            <svg-icon :icon="'#align'"></svg-icon>
                        </i>
                        <div class="allHeaderHomeContentBotCats" v-if="showCats" @click.stop="showCats = !showCats">
                            <ul @click.stop="">
                                <li>
                                    <div class="pic">
                                        <inertia-link :href="'/'+ $i18n.locale">
                                            <img :src="$page.logo" :alt="$page.logo">
                                        </inertia-link>
                                    </div>
                                    <i @click.stop="showCats = !showCats">
                                        <svg-icon :icon="'#cancel'"></svg-icon>
                                    </i>
                                </li>
                                <li>
                                    <div class="resTheme" v-if="$page.siteTheme">
                                        <input type="checkbox" id="themeSwitch2" v-model="theme" @change="btnChange" class="theme-switch__input" />
                                        <label for="themeSwitch2" class="theme-switch__label">
                                            <span></span>
                                        </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="resLang" v-if="$page.siteLanguages">
                                        <div class="resLangPic" @click="changeLang('fa')">
                                            <svg-icon :icon="'#ir'"></svg-icon>
                                        </div>
                                        <div class="resLangPic" @click="changeLang('en')">
                                            <svg-icon :icon="'#en'"></svg-icon>
                                        </div>
                                    </div>
                                </li>
                                <li v-if="!$page.userData">
                                    <div class="topCat">
                                        <i>
                                            <svg-icon :icon="'#user'"></svg-icon>
                                        </i>
                                        <inertia-link href="/login/">{{ $t('login') }}</inertia-link>
                                    </div>
                                </li>
                                <li v-if="$page.userData">
                                    <div class="titleCat">
                                        <i>
                                            <svg-icon :icon="'#user'"></svg-icon>
                                        </i>
                                        <span>{{$t('manageAccount')}}</span>
                                    </div>
                                </li>
                                <li v-if="$page.userData">
                                    <inertia-link href="/profile">{{$t('manageAccount')}}</inertia-link>
                                </li>
                                <li v-if="$page.userData">
                                    <inertia-link href="/profile/bookmark">{{$t('mySigns')}}</inertia-link>
                                </li>
                                <li v-if="$page.userData">
                                    <inertia-link href="/profile/pay">{{$t('myOrders')}}</inertia-link>
                                </li>
                                <li v-if="$page.userData">
                                    <inertia-link href="/profile/like">{{$t('favorites')}}</inertia-link>
                                </li>
                                <li v-if="$page.userData">
                                    <inertia-link href="/logout">{{$t('logout')}}</inertia-link>
                                </li>
                                <li v-if="admin == 1">
                                    <div class="topCat">
                                        <i>
                                            <svg-icon :icon="'#user2'"></svg-icon>
                                        </i>
                                        <inertia-link :href="'/admin'">
                                            داشبورد
                                        </inertia-link>
                                    </div>
                                </li>
                                <li v-if="$page.pageHome.length">
                                    <div class="titleCat">
                                        <i>
                                            <svg-icon :icon="'#file2'"></svg-icon>
                                        </i>
                                        <span>{{$t('sheets')}}</span>
                                    </div>
                                </li>
                                <li v-if="$i18n.locale == 'fa' && $page.pageHome.length" v-for="(allList , index) in $page.pageHome" :key="allList.id">
                                    <inertia-link :href="'/page/' + allList.slug">
                                        {{allList.title}}
                                    </inertia-link>
                                </li>
                                <li v-if="$i18n.locale == 'en' && $page.pageHome.length" v-for="(allList , index) in $page.pageHome" :key="allList.id">
                                    <inertia-link :href="'/page/' + allList.slug">
                                        {{allList.titleEn}}
                                    </inertia-link>
                                </li>
                                <li v-if="$page.catList.length">
                                    <div class="titleCat">
                                        <i>
                                            <svg-icon :icon="'#box2'"></svg-icon>
                                        </i>
                                        <span>{{$t('category')}}</span>
                                    </div>
                                </li>
                                <li v-if="$i18n.locale == 'fa'" v-for="(allList , index) in $page.catList" :key="allList.id">
                                    <inertia-link :href="allList.type == 1 ? '/news/archive/category/' + allList.slug : '/archive/category/' + allList.slug">
                                        <i v-if="allList.cats.length">
                                            <svg-icon :icon="'#forward'"></svg-icon>
                                        </i>
                                        {{allList.name}}
                                    </inertia-link>
                                    <ul class="allListContainer">
                                        <li v-for="lists in allList.cats.slice(0 , 8)">
                                            <inertia-link :href="lists.type == 1 ? '/news/archive/category/' + lists.slug : '/archive/category/' + lists.slug">
                                                <i>
                                                    <svg-icon :icon="'#circle'"></svg-icon>
                                                </i>
                                                {{lists.name}}
                                            </inertia-link>
                                        </li>
                                    </ul>
                                </li>
                                <li v-if="$i18n.locale == 'en'" v-for="(allList , index) in $page.catList" :key="allList.id">
                                    <inertia-link :href="allList.type ? '/news/archive/category/' + allList.slug : '/archive/category/' + allList.slug">
                                        <i v-if="allList.cats.length">
                                            <svg-icon :icon="'#forward'"></svg-icon>
                                        </i>
                                        {{allList.nameEn}}
                                    </inertia-link>
                                    <ul class="allListContainer">
                                        <li v-for="lists in allList.cats.slice(0 , 8)">
                                            <inertia-link :href="lists.type == 1 ? '/news/archive/category/' + lists.slug : '/archive/category/' + lists.slug">
                                                <i>
                                                    <svg-icon :icon="'#circle'"></svg-icon>
                                                </i>
                                                {{lists.nameEn}}
                                            </inertia-link>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="user" v-if="$page.userData != null">
                        <div class="pic" @click="showUser = !showUser; showCart = false;">
                            <img v-if="$page.userData.profile == null" src="/img/user.png" :alt="$page.userData.name">
                            <img v-else :src="$page.userData.profile" :alt="$page.userData.name">
                        </div>
                        <ul v-if="showUser">
                            <li>
                                <div class="picUser">
                                    <img v-if="$page.userData.profile == null" src="/img/user.png" :alt="$page.userData.name">
                                    <img v-else :src="$page.userData.profile" :alt="$page.userData.name">
                                </div>
                                <div class="infoUser">
                                    <span>{{$page.userData.name}}</span>
                                    <span>{{$page.userData.created_at}}</span>
                                </div>
                            </li>
                            <li>
                                <inertia-link href="/profile">
                                    <i>
                                        <svg-icon :icon="'#home2'"></svg-icon>
                                    </i>
                                    {{$t('manageAccount')}}
                                </inertia-link>
                            </li>
                            <li>
                                <inertia-link href="/profile/bookmark">
                                    <i>
                                        <svg-icon :icon="'#unbookmark'"></svg-icon>
                                    </i>
                                    {{$t('mySigns')}}
                                </inertia-link>
                            </li>
                            <li>
                                <inertia-link href="/profile/pay">
                                    <i>
                                        <svg-icon :icon="'#bill'"></svg-icon>
                                    </i>
                                    {{$t('myOrders')}}
                                </inertia-link>
                            </li>
                            <li>
                                <inertia-link href="/profile/like">
                                    <i>
                                        <svg-icon :icon="'#unlike'"></svg-icon>
                                    </i>
                                    {{$t('favorites')}}
                                </inertia-link>
                            </li>
                            <li>
                                <inertia-link href="/logout">
                                    <i>
                                        <svg-icon :icon="'#exit'"></svg-icon>
                                    </i>
                                    {{$t('logout')}}
                                </inertia-link>
                            </li>
                        </ul>
                    </div>
                    <inertia-link class="loginTopHeader" href="/login" :title="$t('login')" v-else>
                        <i>
                            <svg-icon :icon="'#user'"></svg-icon>
                        </i>
                        <span>{{$t('login')}}</span>
                    </inertia-link>
                    <div class="headerHomeContentCart">
                        <i @click="showCart = !showCart; showUser = false;">
                            <svg-icon :icon="'#cart'"></svg-icon>
                        </i>
                        <h5 v-if="allCarts.length">{{allCarts.length}}</h5>
                        <div class="showCart" v-if="showCart">
                            <ul v-if="allCarts.length && checkLoader == false">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li v-for="(item , index) in allCarts" :key="index">
                                        <div class="cartPic">
                                            <inertia-link :href="'/product/' + item.carts.slug">
                                                <img :src="JSON.parse(item.carts.image)[0]" :alt="item.carts.title">
                                            </inertia-link>
                                        </div>
                                        <div class="cartText">
                                            <div class="cartTitle">
                                                <h4 v-if="$i18n.locale == 'fa'">
                                                    {{item.carts.title}}
                                                    <h4 v-if="JSON.parse(item.count.size) != ''"> - {{JSON.parse(item.count.size).name}}</h4>
                                                    <h4 v-if="JSON.parse(item.count.color) != ''"> - {{JSON.parse(item.count.color).name}}</h4>
                                                </h4>
                                                <h4 v-if="$i18n.locale == 'en'">
                                                    {{item.carts.titleEn}}
                                                    <h4 v-if="JSON.parse(item.count.size) != ''"> - {{JSON.parse(item.count.size).nameEn}}</h4>
                                                    <h4 v-if="JSON.parse(item.count.color) != ''"> - {{JSON.parse(item.count.color).nameEn}}</h4>
                                                </h4>
                                                <i @click.stop="deleteCart(item.count.id , index)">
                                                    <svg-icon :icon="'#cancel'"></svg-icon>
                                                </i>
                                            </div>
                                            <div class="cartTextItem">
                                                <div class="cartPrice" v-if="$i18n.locale == 'fa'">
                                                    <span>{{(item.count.price * item.count.count)|NumFormat}}</span>
                                                </div>
                                                <div class="cartPriceEn" v-if="$i18n.locale == 'en'">
                                                    <span>{{(item.count.price * item.count.count)|NumFormat}}</span>
                                                </div>
                                                <div class="cartCount">
                                                    <button @click="changeCart(item.count.id , index , change = '1')">+</button>
                                                    <span v-if="$i18n.locale == 'fa'">{{item.count.count}}</span>
                                                    <span class="cartCountPrice" v-else>{{item.count.count}}</span>
                                                    <button @click="changeCart(item.count.id , index , change = '0')">-</button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </VuePerfectScrollbar>
                            </ul>
                            <div class="showCartEmpty" v-if="allCarts.length == 0 && checkLoader == false">
                                <span>
                                    <svg-icon :icon="'#cart'"></svg-icon>
                                </span>
                                <h3>{{$t('emptyCart')}}</h3>
                            </div>
                            <div class="loaderCheck" v-if="checkLoader">
                                <svg-icon :icon="'#loading'"></svg-icon>
                            </div>
                            <div class="showCartBot">
                                <inertia-link href="/cart">{{$t('cart')}}</inertia-link>
                                <div class="showCartBotLoader" v-if="showLoader">
                                    <svg-icon :icon="'#loading'"></svg-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="headerHomeContentBot">
                <ul class="headerHomeContentBotContainer">
                    <li v-if="$page.allow != ''">
                        <a href="/admin" v-for="(item , index) in $page.allow" :key="index" v-if="item.name == 'نمایش داشبورد'">
                            داشبورد
                        </a>
                    </li>
                    <li v-else>
                        <a href="/admin" v-if="$page.userData.admin == 1">
                            داشبورد
                        </a>
                    </li>
                    <li>
                        <inertia-link href="/">{{$t('home')}}</inertia-link>
                    </li>
                    <li v-if="$i18n.locale == 'fa' && $page.catList.length" v-for="(allList , index) in $page.catList" :key="index">
                        <inertia-link :href="allList.type == 1 ? '/news/archive/category/' + allList.slug : '/archive/category/' + allList.slug">
                            {{allList.name}}
                            <i v-if="allList.cats.length">
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </inertia-link>
                        <ul class="allListContainer">
                            <li v-for="lists in allList.cats.slice(0 , 8)">
                                <inertia-link :href="'/archive/category/' + lists.slug">{{lists.name}}</inertia-link>
                                <ul class="listsContainer">
                                    <li v-for="list in lists.cats.slice(0 , 4)">
                                        <inertia-link :href="'/archive/category/' + list.slug">{{list.name}}</inertia-link>
                                        <ul class="listContainer">
                                            <li v-for="item in list.cats.slice(0 , 6)">
                                                <inertia-link :href="'/archive/category/' + item.slug">
                                                    <i>
                                                        <svg-icon :icon="'#forward'"></svg-icon>
                                                    </i>
                                                    {{item.name}}
                                                </inertia-link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li v-if="$i18n.locale == 'en' && $page.catList.length" v-for="(allList , index) in $page.catList" :key="index">
                        <inertia-link :href="allList.type == 1 ? '/news/archive/category/' + allList.slug : '/archive/category/' + allList.slug">
                            {{allList.nameEn}}
                            <i v-if="allList.cats.length">
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </inertia-link>
                        <ul class="allListContainer">
                            <li v-for="lists in allList.cats.slice(0 , 8)">
                                <inertia-link :href="'/archive/category/' + lists.slug">{{lists.nameEn}}</inertia-link>
                                <ul class="listsContainer">
                                    <li v-for="list in lists.cats.slice(0 , 4)">
                                        <inertia-link :href="'/archive/category/' + list.slug">{{list.nameEn}}</inertia-link>
                                        <ul class="listContainer">
                                            <li v-for="item in list.cats.slice(0 , 6)">
                                                <inertia-link :href="'/archive/category/' + item.slug">
                                                    <i>
                                                        <svg-icon :icon="'#forward'"></svg-icon>
                                                    </i>
                                                    {{item.nameEn}}
                                                </inertia-link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <span @click="showTicket = true">{{$t('question')}}</span>
                    </li>
                    <li v-if="$page.myRank">
                        <inertia-link href="/rank/suggest">{{$t('mySuggest')}}</inertia-link>
                    </li>
                </ul>
                <div class="headerHomeContentBotTheme" v-if="$page.siteTheme">
                    <input type="checkbox" id="themeSwitch" v-model="theme" @change="btnChange" class="theme-switch__input" />
                    <label for="themeSwitch" class="theme-switch__label">
                        <span></span>
                    </label>
                </div>
                <div class="headerHomeContentBotLang" v-if="$page.siteLanguages">
                    <div class="headerHomeContentBotLangPic" @click="changeLang('fa')">
                        <svg-icon :icon="'#ir'"></svg-icon>
                    </div>
                    <div class="headerHomeContentBotLangPic" @click="changeLang('en')">
                        <svg-icon :icon="'#en'"></svg-icon>
                    </div>
                </div>
            </div>
        </div>
        <div class="allSendTickets" v-if="showTicket">
            <send-ticket v-on:sendClose="getClose"></send-ticket>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import SendTicket from "./SendTicket";
export default {
    name: "HeaderHome",
    components:{
        SendTicket,
        VuePerfectScrollbar,
        SvgIcon,
    },
    data(){
        return{
            allCarts: [],
            count: [],
            theme: 0,
            carts: [],
            allows: [],
            showCart: false,
            btnSearchResponsive: false,
            showCats: false,
            showLoader: false,
            checkLoader: false,
            showUser: false,
            showTicket: false,
            change: '',
            searchProduct: '',
            allResult: [],
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
            showNavbar: true,
            searchLoader: false,
            lastScrollPosition: 0,
            styleHeader : {
                top: '0',
                visibility: 'visible',
            },
        }
    },
    beforeDestroy () {
        window.removeEventListener('scroll', this.onScroll)
    },
    methods:{
        btnChange(){
            this.$cookies.set('theme', this.theme);
            window.location.reload();
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
            this.showNavbar = currentScrollPosition < this.lastScrollPosition
            if(currentScrollPosition >= this.lastScrollPosition){
                this.styleHeader = {
                    top: '-9rem',
                    visibility: 'hidden'
                };
            }else{
                this.styleHeader = {
                    top: '0',
                    visibility: 'visible'
                };
            }
            this.lastScrollPosition = currentScrollPosition;
            if (window.innerWidth <= 700) {
                this.styleHeader = {
                    top: 'auto',
                    position: 'relative',
                    visibility: 'visible'
                };
            }
        },
        getClose(){
            this.showTicket = false;
        },
        changeLang(lang){
            this.$i18n.locale = lang;
        },
        btnSearchProduct(){
            const url = `/archive/search?search=${this.searchProduct}`;
            this.$inertia.post(url , {
                search : this.searchProduct,
            })
                .then(response=>{
                    this.showLoader = false;
                })
        },
        startedTyping() {
        },
        searchDataHandler(){
            if (this.searchProduct.length > 1){
                this.searchLoader = true;
                this.stoppedTyping();
            }else{
                this.allResult = [];
            }
        },
        stoppedTyping() {
            if(this.searchProduct){
                const url = '/search-nav';
                axios.post(url , {
                    search : this.searchProduct
                })
                    .then(res => {
                        this.searchLoader = false;
                        this.allResult = res.data;
                    })
            }else{
                this.allResult = [];
            }
        },
        deleteCart(id , index){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل حذف شده برگشتی ندارد!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    this.showLoader = true;
                    const url = `/delete-cart/${id}`;
                    axios.delete(url)
                        .then(response=>{
                            this.showLoader = false;
                            this.allCarts.splice(index , 1);
                            this.$eventHub.emit('getCart');
                        })
                }
            })
        },
        getCarts(){
            this.checkLoader = true;
            const url = '/get-carts';
            axios.get(url)
                .then(response=>{
                    if (response.data == 'no'){

                    }else{
                        this.allCarts =[];
                        this.carts = response.data[0];
                        this.count = response.data[1];
                        for ( this.i ; this.i <  this.carts.length; this.i++) {
                            this.allCarts.push({count : '',carts : '',});
                            this.allCarts[this.i].count = this.count[this.i];
                            this.allCarts[this.i].carts = this.carts[this.i];
                        }
                        this.i = 0;
                    }
                    this.checkLoader = 0;
                })
        },
        changeCart(id , index){
            this.showLoader = true;
            const url = `/change-carts/${id}`;
            axios.put(url,{change : this.change})
                .then(response=>{
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if(response.data == 'success'){
                        if(this.change == 0){
                            if (this.allCarts[index].count.count == 1){
                                this.allCarts.splice(index , 1);
                            }else{
                                this.allCarts[index].count.count = --this.allCarts[index].count.count;
                            }
                        }else{
                            this.allCarts[index].count.count = ++this.allCarts[index].count.count;
                        }
                        this.$eventHub.emit('getCart');
                    }
                    this.showLoader = false;
                })
        },
        getData(){
            if(this.$cookies.get('theme') == 'true' || this.$cookies.get('theme') == 'false'){
                if(this.$cookies.get('theme') == 'true'){
                    this.theme = true;
                }
                if(this.$cookies.get('theme') == 'false'){
                    this.theme = false;
                }
            }else{
                if(this.$page.dark == 1){
                    this.theme = true;
                }else{
                    this.theme = false;
                }
            }
        }
    },
    mounted() {
        this.getCarts();
        this.getData();
        window.addEventListener('scroll', this.onScroll);
    },
    created: function() {
        this.$eventHub.on('getCart', this.getCarts);
    },
}

</script>

<style scoped>

</style>
