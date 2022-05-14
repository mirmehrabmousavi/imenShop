<template>
    <home-layout>
        <div class="allPersonalUser width">
        <div class="allCartIndexTitle">
            <h4>{{$t('selectAddress')}}</h4>
            <div class="allCartIndexAddress">
                <inertia-link href="/">{{$t('home')}}</inertia-link>
                <span>/</span>
                <inertia-link href="/cart">{{$t('cart')}}</inertia-link>
                <span>/</span>
                <inertia-link href="/user-info-cart">{{$t('selectAddress')}}</inertia-link>
            </div>
        </div>
        <div class="allPersonalUserBot">
            <div class="userCart">
                <div class="allAddress">
                    <div class="title">
                        <i>
                            <svg-icon :icon="'#location'"></svg-icon>
                        </i>
                        آدرس تحویل سفارش را انتخاب نمایید:
                    </div>
                    <div class="addresses">
                        <div class="addressItem" v-for="(item,index) in allAddress" @click="btnSelect(item.id)">
                            <div class="detail active" v-if="item.status == 1">
                                <div class="titleAddress">
                                    <i>
                                        <svg-icon :icon="'#circle'"></svg-icon>
                                    </i>
                                    به این آدرس ارسال میشود
                                </div>
                                <div class="addressData">
                                    <p>{{item.address}}</p>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#location'"></svg-icon>
                                        </i>
                                        <span>{{ item.post }}</span>
                                    </div>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#messagePanel'"></svg-icon>
                                        </i>
                                        <span>{{ item.number }}</span>
                                    </div>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#customer'"></svg-icon>
                                        </i>
                                        <span>{{ item.name }}</span>
                                    </div>
                                    <div class="options">
                                        <button @click.prevent="editAddress(item.id)">ویرایش</button>
                                        <button>حذف</button>
                                    </div>
                                </div>
                            </div>
                            <div class="detail" v-else>
                                <div class="titleAddress">
                                    <i>
                                        <svg-icon :icon="'#circle'"></svg-icon>
                                    </i>
                                    برای انتخاب آدرس کلیک کنید
                                </div>
                                <div class="addressData">
                                    <p>{{item.address}}</p>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#location'"></svg-icon>
                                        </i>
                                        <span>{{ item.post }}</span>
                                    </div>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#messagePanel'"></svg-icon>
                                        </i>
                                        <span>{{ item.number }}</span>
                                    </div>
                                    <div class="dataItem">
                                        <i>
                                            <svg-icon :icon="'#customer'"></svg-icon>
                                        </i>
                                        <span>{{ item.name }}</span>
                                    </div>
                                    <div class="options">
                                        <button @click.prevent="editAddress(item.id)">ویرایش</button>
                                        <button @click.prevent="deleteAddress(item.id , index)">حذف</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="addAddress" @click="btnAddAddress">
                            <div class="add">
                                <i>
                                    <svg-icon :icon="'#plus'"></svg-icon>
                                </i>
                                <h4>ایجاد آدرس جدید</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cartDays" id="time">
                    <div class="cartDaysTitle">
                        <i class="icon">
                            <svg-icon :icon="'#clock'"></svg-icon>
                        </i>
                        <h5>{{ $t('choiceTime') }}</h5>
                        <div class="allCategoryPanel" @click="showAllCarrier = !showAllCarrier">
                            <div class="categoryShow">
                                <h4 v-if="$i18n.locale == 'fa'">{{getCarriers.name}}</h4>
                                <h4 v-if="$i18n.locale == 'en'">{{getCarriers.nameEn}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showAllCarrier">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li v-for="item in carriers" @click="sendGetCarriers(item,item.id)">{{item.name}}</li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                    </div>
                    <ul>
                        <li v-for="item in days">
                            <h3 v-if="$i18n.locale == 'fa'">{{item.dayL}}</h3>
                            <h3 v-if="$i18n.locale == 'en'">{{item.dayLEn}}</h3>
                            <p>
                                <span>{{item.day}}</span>
                                <span v-if="$i18n.locale == 'fa'">{{item.month}}</span>
                                <span v-if="$i18n.locale == 'en'">{{item.monthEn}}</span>
                            </p>
                            <div class="dayItem">
                                <div class="active" v-if="item.day == getTime.day">
                                    <h4 v-if="$i18n.locale == 'fa'">
                                        بازه
                                        {{item.from}}
                                        -
                                        {{item.to}}
                                    </h4>
                                    <h4 v-if="$i18n.locale == 'en'" class="en">
                                        Time
                                        {{item.from}}
                                        -
                                        {{item.to}}
                                    </h4>
                                    <h5 v-if="getCarriers.price >= 1 && getCarriers.limit>=allPay">
                                        <span v-if="$i18n.locale == 'fa'">
                                            {{ getCarriers.price|NumFormat }}
                                            تومان
                                        </span>
                                        <span v-if="$i18n.locale == 'en'" class="en">
                                            {{ getCarriers.price|NumFormat }}
                                            tooman
                                        </span>
                                    </h5>
                                    <h5 v-else>
                                        <span v-if="$i18n.locale == 'fa'">رایگان</span>
                                        <span v-if="$i18n.locale == 'en'">Free</span>
                                    </h5>
                                </div>
                                <div v-else @click="btnTimeGet(item)">
                                    <h4 v-if="$i18n.locale == 'fa'">
                                        بازه
                                        {{item.from}}
                                        -
                                        {{item.to}}
                                    </h4>
                                    <h4 v-if="$i18n.locale == 'en'" class="en">
                                        Time
                                        {{item.from}}
                                        -
                                        {{item.to}}
                                    </h4>
                                    <h5 v-if="getCarriers.price >= 1 && getCarriers.limit>=allPay">
                                        <span v-if="$i18n.locale == 'fa'">
                                            {{ getCarriers.price|NumFormat }}
                                            تومان
                                        </span>
                                        <span v-if="$i18n.locale == 'en'" class="en">
                                            {{ getCarriers.price|NumFormat }}
                                            tooman
                                        </span>
                                    </h5>
                                    <h5 v-else>
                                        <span v-if="$i18n.locale == 'fa'">رایگان</span>
                                        <span v-if="$i18n.locale == 'en'">Free</span>
                                    </h5>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
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
                <div class="allSum">
                    <label>{{$t('total')}} :</label>
                    <span v-if="$i18n.locale == 'fa'">{{allPay|NumFormat}} تومان</span>
                    <span v-else class="allCartIndexItemNextEn">{{allPay|NumFormat}} t</span>
                </div>
                <div class="sendMoney">
                    <label>{{$t('shippingCost')}} :</label>
                    <span v-if="$i18n.locale == 'fa'">
                        <span v-if="allSends >= 1 && getCarriers.limit>=allPay">{{allSends|NumFormat}} تومان</span>
                        <h5 v-else>رایگان</h5>
                    </span>
                    <span v-else class="allCartIndexItemNextEn">
                        <span v-if="allSends >= 1">{{allSends|NumFormat}} t</span>
                        <h5 v-else>Free</h5>
                    </span>
                </div>
                <div class="allMoney">
                    <label>{{$t('amountPayable')}} :</label>
                    <span v-if="$i18n.locale == 'fa'">{{allSum2|NumFormat}} تومان</span>
                    <span v-else class="allCartIndexItemNextEn">{{allSum2|NumFormat}} t</span>
                </div>
                <div class="discount">
                    <h4>کد تخفیف :</h4>
                    <label for="dis">
                        <input type="text" id="dis" v-model="discount" placeholder="کد تخفیف را وارد کنید">
                        <i v-if="check == 3">
                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                        </i>
                        <button v-if="check == 0" @click="btnCheckDiscount">
                            بررسی
                        </button>
                        <button v-if="check == 1" class="accept" @click="btnCheckDiscount">
                            بررسی
                        </button>
                        <button v-if="check == 2" class="fail" @click="btnCheckDiscount">
                            بررسی
                        </button>
                    </label>
                </div>
                <div class="nextItem">
                    <a class="choiceShop" v-if="getCarriers && getTime" @click="showWay = !showWay">{{$t('choiceBuy')}}</a>
                    <a class="choiceShop" v-else>زمان ارسال و حامل را انتخاب کنین</a>
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
        <div class="buyWays" v-if="showWay">
            <div class="buyWaysItems">
                <div class="title">
                    <div class="right">
                        <i>
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </i>
                        <span>برای خرید مرسوله ها یکی از دو روش را انتخاب کنید</span>
                    </div>
                    <div class="close" @click="showWay = !showWay">
                        <svg-icon :icon="'#cancel'"></svg-icon>
                    </div>
                </div>
                <div class="wayItems">
                    <a href="/shop" class="wayItem">
                        <i>
                            <svg-icon :icon="'#online-shop'"></svg-icon>
                        </i>
                        <h4>{{$t('buyGate')}}</h4>
                        <p>{{ $t('payWay1') }}</p>
                    </a>
                    <a href="/shop/wallet" class="wayItem" v-if="$page.wallet >= allSum2">
                        <i>
                            <svg-icon :icon="'#wallet'"></svg-icon>
                        </i>
                        <h4>{{$t('buyWallet')}}</h4>
                        <p>{{$t('payWay2')}}</p>
                    </a>
                    <a href="/charge-increase" class="wayItem" v-else>
                        <i>
                            <svg-icon :icon="'#wallet'"></svg-icon>
                        </i>
                        <h4>{{$t('buyWallet')}}</h4>
                        <p>{{ $t('payWay3') }}</p>
                    </a>
                    <a href="/payment-spot" class="wayItem">
                        <i>
                            <svg-icon :icon="'#homePay'"></svg-icon>
                        </i>
                        <h4>{{$t('buyHome')}}</h4>
                        <p>{{ $t('payWay4') }}</p>
                    </a>
                </div>
            </div>
        </div>
        <add-address v-if="showAddress" :editAddress="editMyAddress" :map="map" :edited="edit" v-on:sendAddress="getAddress" v-on:sendClose="getClose"></add-address>
    </div>
    </home-layout>
</template>

<script>
import VuePersianDatetimePicker from 'vue-persian-datetime-picker'
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import AddAddress from "./AddAddress";
export default {
    name: "UserCart",
    props: ['users','number','errors','days','map','carriers','title'],
    metaInfo() {
        return {
            title: `آدرس و زمان ارسال - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                reptilian: 'gator',
                amp: true,
            },
            headAttrs: {
                nest: 'eggs',
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    components: {
        AddAddress,
        datePicker: VuePersianDatetimePicker,
        HomeLayout,
        SvgIcon
    },
    data() {
        return {
            form:{
                date: '',
                name: '',
                address: '',
                post: '',
                job: '',
                code: '',
            },
            allCarts: [],
            allSum: 0,
            allSum2: 0,
            edit: 0,
            allPay: 0,
            allCount: 0,
            allOff: 0,
            allSends: 0,
            check: 0,
            showAddress: false,
            showWay: false,
            allProfit: 0,
            allScore: 0,
            discount: '',
            count: [],
            carts: [],
            i: 0,
            getCarriers: '',
            allAddress: [],
            getTime: '',
            showAllCarrier: false,
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
        btnSelect(id){
            const url = '/select-address';
            axios.post(url,{
                address: id
            })
                .then(response =>{
                    this.getAllAddress();
                })
        },
        getClose(){
            this.showAddress = false;
        },
        getAddress(address){
            this.showAddress = false;
            this.allAddress.push(address);
        },
        editMyAddress(){
            this.getAllAddress();
        },
        editAddress(address){
            const url = '/edit-address';
            axios.post(url,{
                address: address
            })
                .then(response =>{
                    this.edit = response.data;
                    setTimeout(() => this.showAddress = true, 200);
                })
        },
        deleteAddress(address,index){
            const url = '/delete-address';
            axios.post(url,{
                address: address
            })
                .then(response =>{
                    this.allAddress.splice(index,1);
                })
        },
        btnAddAddress(){
            this.edit = 0;
            setTimeout(() => this.showAddress = true, 200);
        },
        btnCheckDiscount(){
            this.check = 3;
            const url = '/check-discount';
            axios.post(url,{
                discount: this.discount
            })
            .then(response =>{
                this.check = response.data;
            })
        },
        btnTimeGet(item){
            this.getTime = item;
            if (this.getCarriers){
                if (this.getCarriers.limit <= this.allPay){
                    this.allSends = 0;
                    this.getTime.price = 0;
                }else{
                    this.getTime.price = this.getCarriers.price;
                    this.allSends = this.getCarriers.price;
                }
            }
            this.allSum2 = parseInt(this.allPay) + parseInt(this.allSends);
            const url = '/change-time-delivery';
            axios.post(url,{
                time: JSON.stringify(item)
            })
        },
        sendGetCarriers(item){
            this.getCarriers = item;
            if (this.getTime){
                if (this.getCarriers.limit <= this.allPay){
                    this.allSends = 0;
                    this.getTime.price = 0;
                }else{
                    this.getTime.price = item.price;
                    this.allSends = this.getCarriers.price;
                }
            }
            this.allSum2 = parseInt(this.allPay) + parseInt(this.allSends);
            const url = '/change-carrier';
            axios.post(url,{
                carrier: item.id
            })
        },
        getCarts(){
            if(this.carriers.length){
                this.getCarriers = this.carriers[0];
            }
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
                        this.i = 0;
                        if (this.users){
                            this.form.date= this.users.date;
                            this.form.name= this.users.name;
                            this.form.address= this.users.address;
                            this.form.post= this.users.post;
                            this.form.job= this.users.job;
                            this.form.code= this.users.code;
                        }
                        if(response.data[1][0]['carrier'].length){
                            this.getCarriers = response.data[1][0]['carrier'][0];
                        }else{
                            const url = '/change-carrier';
                            axios.post(url,{
                                carrier: this.getCarriers.id
                            })
                        }
                        if(response.data[1][0]['delivery']){
                            this.getTime = JSON.parse(response.data[1][0]['delivery']);
                            if (this.getCarriers.limit <= this.allPay){
                                this.allSends = 0;
                                this.getTime.price = 0;
                            }else{
                                this.allSends = this.getCarriers.price;
                                this.getTime.price = this.getCarriers.price;
                            }
                        }else{
                            this.allSends = this.getCarriers.price;
                            this.getTime.price = this.getCarriers.price;
                        }
                        this.allSum2 = parseInt(this.allPay) + parseInt(this.allSends);
                    }
                })
        },
        getAllAddress(){
            const url = '/get-all-address';
            axios.get(url)
            .then(response =>{
                this.allAddress = response.data;
            })
        },
        changeInfo(){
            const url = '/change-user-info';
            this.$inertia.put(url , this.form)
                .then(response=>{
                    this.$toast.success('انجام شد', 'تنظیمات با موفقیت ذخیره شد', this.notificationSystem.options.success);
                })
                .catch(err=>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    },
    mounted() {
        this.getCarts();
        this.getAllAddress();
    },
    created: function() {
        this.$eventHub.on('getCart', this.getCarts);
    },
}
</script>

<style scoped>

</style>
