<template>
    <home-layout>
        <div class="allCartIndex width">
            <div class="allCartIndexTitle">
                <h4>{{$t('cart')}}</h4>
                <div class="allCartIndexAddress">
                    <inertia-link href="/">{{$t('home')}}</inertia-link>
                    <span>/</span>
                    <inertia-link href="/cart">{{$t('cart')}}</inertia-link>
                </div>
            </div>
            <i class="getCartLoader" v-if="showLoader == true">
                <svg-icon :icon="'#loading'"></svg-icon>
            </i>
            <div class="allCartIndexItem" v-if="allCarts.length">
                <div class="allCartIndexItemDetail">
                    <ul>
                        <li v-for="(item , index) in allCarts">
                            <inertia-link :href="'/product/'+item.carts.slug" class="cartDetailPic">
                                <img :src="JSON.parse(item.carts.image)[0]" :alt="item.carts.title">
                            </inertia-link>
                            <div class="cartDetailInfo">
                                <inertia-link :href="'/product/'+item.carts.slug" class="cartDetailInfoItem">
                                    <h3 v-if="$i18n.locale == 'fa'">{{item.carts.title}}</h3>
                                    <h3 v-if="$i18n.locale == 'en'">{{item.carts.titleEn}}</h3>
                                </inertia-link>
                                <div class="cartDetailInfoItem" v-if="JSON.parse(item.count.color) != ''">
                                    <i>
                                        <svg-icon :icon="'#color'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{JSON.parse(item.count.color).name}}</span>
                                    <span v-if="$i18n.locale == 'en'">{{JSON.parse(item.count.color).nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem" v-if="JSON.parse(item.count.size) != ''">
                                    <i>
                                        <svg-icon :icon="'#sizeFront'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{JSON.parse(item.count.size).name}}</span>
                                    <span v-if="$i18n.locale == 'en'">{{JSON.parse(item.count.size).nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem" v-if="item.count.guarantee">
                                    <i>
                                        <svg-icon :icon="'#security'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{item.count.guarantee.name}}</span>
                                    <span v-if="$i18n.locale == 'en'">{{item.count.guarantee.nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem">
                                    <i>
                                        <svg-icon :icon="'#shop'"></svg-icon>
                                    </i>
                                    <span>{{item.count.user.name}}</span>
                                </div>
                                <div class="cartDetailInfoPrice">
                                    <div class="cartCount">
                                        <button @click="changeCart(item.count.id , index , change = '0')">-</button>
                                            <span v-if="$i18n.locale == 'fa'">{{item.count.count}}</span>
                                            <span class="cartCountPrice" v-else>{{item.count.count}}</span>
                                        <button @click="changeCart(item.count.id , index , change = '1')">+</button>
                                    </div>
                                    <i @click="deleteCart(item.count.id , index)">
                                        <svg-icon :icon="'#trash'"></svg-icon>
                                    </i>
                                    <div class="cartPrice" v-if="$i18n.locale == 'fa'">
                                        <div class="offPrice" v-if="item.carts.off != null">
                                            <s>{{(item.carts.offPrice*item.count.count)|NumFormat}} تومان</s>
                                        </div>
                                        <h3>
                                            {{(item.count.price*item.count.count)|NumFormat}}
                                            <span>تومان</span>
                                        </h3>
                                    </div>
                                    <div class="cartPrice en" v-if="$i18n.locale == 'en'">
                                        <div class="offPrice" v-if="item.carts.off != null">
                                            <s>{{(item.carts.offPrice*item.count.count)|NumFormat}} toman</s>
                                        </div>
                                        <h3>
                                            {{(item.count.price*item.count.count)|NumFormat}}
                                            <span>toman</span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="allCartIndexItemNext">
                    <div class="WithoutOff">
                        <label>{{$t('price')}} :</label>
                        <span v-if="$i18n.locale == 'fa'">{{allPay|NumFormat}} تومان</span>
                        <span v-else class="allCartIndexItemNextEn">{{allPay|NumFormat}} t</span>
                    </div>
                    <div class="postCount">
                        <label>{{$t('productCount')}} :</label>
                        <span v-if="$i18n.locale == 'fa'">{{allCount|NumFormat}} {{$t('product')}}</span>
                        <span v-else class="allCartIndexItemNextEn">{{allCount|NumFormat}} {{$t('product')}}</span>
                    </div>
                    <div class="allOff">
                        <label>{{$t('productDiscounts')}} :</label>
                        <span v-if="$i18n.locale == 'fa'">{{allOff|NumFormat}} تومان</span>
                        <span v-else class="allCartIndexItemNextEn">{{allOff|NumFormat}} t</span>
                    </div>
                    <div class="allProfit">
                        <label>{{$t('profitRate')}} :</label>
                        <span v-if="$i18n.locale == 'fa'">{{allOff|NumFormat}} تومان</span>
                        <span v-else class="allCartIndexItemNextEn">{{allOff|NumFormat}} t</span>
                    </div>
                    <div class="allMoney">
                        <label>{{$t('amountPayable')}} :</label>
                        <span v-if="$i18n.locale == 'fa'">{{allSum2|NumFormat}} تومان</span>
                        <span v-else class="allCartIndexItemNextEn">{{allSum2|NumFormat}} t</span>
                    </div>
                    <div class="nextItem">
                        <inertia-link href="/user-info-cart">{{$t('continueBuying')}}</inertia-link>
                    </div>
                    <div class="scoreCart">
                        <div class="rightScore">
                            <i>
                                <svg-icon :icon="'#score'"></svg-icon>
                            </i>
                            <span>{{$t('scorePay')}}</span>
                        </div>
                        <span>{{ allScore|NumFormat }} {{$t('score')}}</span>
                    </div>
                </div>
            </div>
            <div class="allCartIndexEmpty" v-if="allCarts.length == 0 && showLoader == false">
                <i>
                    <svg-icon :icon="'#cart'"></svg-icon>
                </i>
                <h3>{{$t('emptyCart')}}</h3>
                <p>{{$t('followingPage')}}</p>
                <div class="allCartIndexEmptyOptions">
                    <inertia-link href="/">{{$t('mainPage')}}</inertia-link>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";

export default {
    name: "CartIndex",
    props:['title'],
    metaInfo() {
        return {
            title: `سبد خرید - ${this.title}`,
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
    components:{
        HomeLayout,
        SvgIcon,
    },
    data(){
        return{
            allCarts: [],
            allSum: 0,
            allSum2: 0,
            allPay: 0,
            allCount: 0,
            allOff: 0,
            allSends: 0,
            allProfit: 0,
            allScore: 0,
            showLoader: false,
            count: [],
            carts: [],
            change: '',
            i: 0,
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
                    const url = `/delete-cart/${id}`;
                    axios.delete(url)
                        .then(response=>{
                            this.allCarts.splice(index , 1);
                            this.$eventHub.emit('getCart');
                        })
                }
            })
        },
        getCarts(){
            this.showLoader = true;
            const url = '/get-carts';
            axios.get(url)
                .then(response=>{
                    if (response.data === 'no'){

                    }else{
                        this.allCarts =[];
                        this.allSum =0;
                        this.allCount =0;
                        this.allPay =0;
                        this.allOff =0;
                        this.allScore =0;
                        this.carts = response.data[0];
                        this.count = response.data[1];
                        this.allSends = response.data[2];
                        for ( this.i ; this.i <  this.carts.length; this.i++) {
                            this.allCarts.push({count : '',carts : '',});
                            this.allCarts[this.i].count = this.count[this.i];
                            this.allCarts[this.i].carts = this.carts[this.i];
                            this.allScore = this.allScore + (parseInt(this.allCarts[this.i].carts.score) * this.allCarts[this.i].count.count);
                            this.allPay = parseInt(this.allPay) + parseInt(this.count[this.i].price) * parseInt(this.count[this.i].count);
                            if (this.allCarts[this.i].carts.off != null){
                                this.allOff = parseInt(this.allOff) + parseInt(this.allCarts[this.i].carts.price) * parseInt(this.allCarts[this.i].count.count);
                            }
                            this.allCount = parseInt(this.allCount) + parseInt(this.allCarts[this.i].count.count);
                        }
                        this.allSum2 = parseInt(this.allPay);
                        this.i = 0;
                        this.showLoader = false;
                    }
                })
        },
        changeCart(id , index){
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
                })
        }
    },
    mounted() {
        this.getCarts();
    },
    created: function() {
        this.$eventHub.on('getCart', this.getCarts);
    },
}
</script>

<style scoped>

</style>
