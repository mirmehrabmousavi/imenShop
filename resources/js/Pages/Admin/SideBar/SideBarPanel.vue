<template>
    <div class="allSideBar">
        <VuePerfectScrollbar class="scroll-area">
            <div class="allSideBarUser">
                <div class="allSideBarUserPic">
                    <img v-if="$page.user.profile == null" src="/img/user.png">
                    <img v-else :src="$page.user.profile">
                </div>
                <span>{{ $page.user.name }}</span>
                <h4>{{ $page.user.email }}</h4>
                <div class="userCommunication">
                    <a href="/" title="صفحه اصلی">
                        <svg-icon :icon="'#home2'"></svg-icon>
                    </a>
                    <inertia-link href="/admin/pay" title="پرداختی ها">
                        <svg-icon :icon="'#bill'"></svg-icon>
                    </inertia-link>
                    <inertia-link href="/admin/user" title="کاربران">
                        <svg-icon :icon="'#customer'"></svg-icon>
                    </inertia-link>
                    <inertia-link href="/admin/logout" title="خروج از حساب">
                        <svg-icon :icon="'#off'"></svg-icon>
                    </inertia-link>
                </div>
            </div>
            <div class="allSideBarItem">
                <div class="allSideBarIcons">
                    <div class="allIconsItem" @click="index = 0">
                        <div class="allIconsItemActive" v-if="index == 0">
                            <svg-icon :icon="'#home'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#home'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="gallery == 1" @click="index = 1">
                        <div class="allIconsItemActive" v-if="index == 1">
                            <svg-icon :icon="'#gallery'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#gallery'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="setting == 1" @click="index = 2">
                        <div class="allIconsItemActive" v-if="index == 2">
                            <svg-icon :icon="'#setting2'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#setting2'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="user == 1 || role == 1" @click="index = 3">
                        <div class="allIconsItemActive" v-if="index == 3">
                            <svg-icon :icon="'#customer'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#customer'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="cat == 1 || tag == 1 || brand == 1" @click="index = 4">
                        <div class="allIconsItemActive" v-if="index == 4">
                            <svg-icon :icon="'#box'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#box'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="page == 1 || ticket == 1 || comment == 1" @click="index = 5">
                        <div class="allIconsItemActive" v-if="index == 5">
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="post == 1" @click="index = 6">
                        <div class="allIconsItemActive" v-if="index == 6">
                            <svg-icon :icon="'#post'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#post'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="variety == 1" @click="index = 15">
                        <div class="allIconsItemActive" v-if="index == 15">
                            <svg-icon :icon="'#graph'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#graph'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="pay == 1 || charge == 1" @click="index = 7">
                        <div class="allIconsItemActive" v-if="index == 7">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="notification == 1" @click="index = 8">
                        <div class="allIconsItemActive" v-if="index == 8">
                            <svg-icon :icon="'#notification'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#notification'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="robot == 1" @click="index = 9">
                        <div class="allIconsItemActive" v-if="index == 9">
                            <svg-icon :icon="'#robot'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#robot'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="news == 1" @click="index = 10">
                        <div class="allIconsItemActive" v-if="index == 10">
                            <svg-icon :icon="'#news'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#news'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="excel1 == 1 || excel2 == 1" @click="index = 11">
                        <div class="allIconsItemActive" v-if="index == 11">
                            <svg-icon :icon="'#excel'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#excel'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="event == 1 || feedback" @click="index = 12">
                        <div class="allIconsItemActive" v-if="index == 12">
                            <svg-icon :icon="'#event'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#event'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="document || seller || checkout" @click="index = 13">
                        <div class="allIconsItemActive" v-if="index == 13">
                            <svg-icon :icon="'#seller'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#seller'"></svg-icon>
                        </div>
                    </div>
                    <div class="allIconsItem" v-if="carrier || time" @click="index = 14">
                        <div class="allIconsItemActive" v-if="index == 14">
                            <svg-icon :icon="'#car'"></svg-icon>
                        </div>
                        <div class="allIconsItemUn" v-else>
                            <svg-icon :icon="'#car'"></svg-icon>
                        </div>
                    </div>
                </div>
                <div class="allSideBarIconsText" v-if="index == 0">
                    <h4>خانه</h4>
                    <ul>
                        <li>
                            <inertia-link href="/">خانه</inertia-link>
                        </li>
                        <li v-if="dashboard == 1">
                            <inertia-link href="/admin">داشبورد</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 1">
                    <h4>رسانه</h4>
                    <ul>
                        <li>
                            <inertia-link href="/admin/gallery">همه رسانه ها</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/gallery">افزودن رسانه</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 2">
                    <h4>تنظیمات</h4>
                    <ul>
                        <li>
                            <inertia-link href="/admin/setting/setting-manage">تنظیمات سایت</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/setting/setting-comment">تنظیمات دیدگاه</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/setting/seo">تنظیمات سئو</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/setting/setting-category">تنظیمات دسته بندی</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/setting/setting-design">تنظیمات قالب سایت</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/setting/setting-pay">تنظیمات درگاه</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 3">
                    <h4>کاربران و مقام ها</h4>
                    <ul>
                        <li v-if="ranking == 1">
                            <inertia-link href="/admin/rank">رتبه بندی براساس شارژ کاربر</inertia-link>
                        </li>
                        <li v-if="user == 1">
                            <inertia-link href="/admin/user">همه کاربران</inertia-link>
                        </li>
                        <li v-if="employees == 1">
                            <inertia-link href="/admin/user/role">همه کارمندان</inertia-link>
                        </li>
                        <li v-if="role == 1">
                            <inertia-link href="/admin/role">همه مقام ها</inertia-link>
                        </li>
                        <li v-if="view == 1">
                            <inertia-link href="/admin/view">همه بازدید ها</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 4">
                    <h4>تاکسونامی</h4>
                    <ul>
                        <li v-if="discount == 1">
                            <inertia-link href="/admin/discount">کد تخفیف</inertia-link>
                        </li>
                        <li v-if="cat == 1">
                            <inertia-link href="/admin/category">دسته بندی ها</inertia-link>
                        </li>
                        <li v-if="tag == 1">
                            <inertia-link href="/admin/tag">برچسب ها</inertia-link>
                        </li>
                        <li v-if="brand == 1">
                            <inertia-link href="/admin/brand">برند ها</inertia-link>
                        </li>
                        <li v-if="guarantee == 1">
                            <inertia-link href="/admin/guarantee">گارانتی ها</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 5">
                    <h4>برگه</h4>
                    <ul>
                        <li v-if="page == 1">
                            <inertia-link href="/admin/page">برگه ها</inertia-link>
                        </li>
                        <li v-if="ticket == 1">
                            <inertia-link href="/admin/ticket">درخواست ها</inertia-link>
                        </li>
                        <li v-if="comment == 1">
                            <inertia-link href="/admin/comment">دیدگاه ها</inertia-link>
                        </li>
                        <li v-if="comment == 1">
                            <inertia-link href="/admin/question">پرسش و پاسخ</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 6">
                    <h4>محصول ها</h4>
                    <ul v-if="post|| inventory">
                        <li v-if="post == 1">
                            <inertia-link href="/admin/post">همه محصول ها</inertia-link>
                        </li>
                        <li v-if="post == 1">
                            <inertia-link href="/admin/post/create">افزودن محصول</inertia-link>
                        </li>
                        <li v-if="inventory">
                            <inertia-link href="/admin/inventory">انبار محصولات</inertia-link>
                        </li>
                        <li v-if="post == 1">
                            <inertia-link href="/admin/file/create">افزودن محصول دانلودی</inertia-link>
                        </li>
                        <li v-if="post == 1">
                            <inertia-link href="/admin/file">همه محصول های دانلودی</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 7">
                    <h4>خرید</h4>
                    <ul v-if="pay == 1 || charge == 1">
                        <li v-if="pay == 1">
                            <inertia-link href="/admin/pay">همه سفارش و پرداختی ها</inertia-link>
                        </li>
                        <li v-if="pay == 1">
                            <inertia-link href="/admin/pay/create">ایجاد سفارش دستی</inertia-link>
                        </li>
                        <li v-if="pay == 1">
                            <inertia-link href="/admin/pay/chart">آمار پرداختی ها</inertia-link>
                        </li>
                        <li v-if="charge == 1">
                            <inertia-link href="/admin/charge">شارژ حساب</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 8">
                    <h4>اطلاع رسانی</h4>
                    <ul v-if="notification == 1">
                        <li>
                            <inertia-link href="/admin/notification/sms">ارسال پیامک</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/notification/email">ارسال ایمیل</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 9">
                    <h4>رباط تلگرامی شما</h4>
                    <ul v-if="robot == 1">
                        <li>
                            <inertia-link href="/admin/robot">رباط ها</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 10">
                    <h4>خبر و بلاگ ها</h4>
                    <ul v-if="news == 1">
                        <li>
                            <inertia-link href="/admin/news">همه خبر ها</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/admin/news/create">افزودن خبر</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 11">
                    <h4>اکسل</h4>
                    <ul v-if="excel1 == 1 ||excel2 == 1">
                        <li v-if="excel1 == 1">
                            <inertia-link href="/admin/excel">خروجی اکسل</inertia-link>
                        </li>
                        <li v-if="excel2 == 1">
                            <inertia-link href="/admin/excel/import">تغییر قیمت با وارد کردن اکسل</inertia-link>
                        </li>
                        <li v-if="excel2 == 1">
                            <inertia-link href="/admin/import/product">ایجاد محصول با وارد کردن اکسل</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 12">
                    <h4>رویداد ها</h4>
                    <ul>
                        <li v-if="event == 1">
                            <inertia-link href="/admin/event">رویداد ها</inertia-link>
                        </li>
                        <li v-if="feedback == 1">
                            <inertia-link href="/admin/report/feedback">بازخورد ها</inertia-link>
                        </li>
                        <li v-if="notification2 == 1">
                            <inertia-link href="/admin/report/notification">اطلاع رسانی از شگفتانه</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 13">
                    <h4>فروشندگان</h4>
                    <ul>
                        <li v-if="seller == 1">
                            <inertia-link href="/admin/seller">فروشندگان</inertia-link>
                        </li>
                        <li v-if="document == 1">
                            <inertia-link href="/admin/document">مدارک فروشندگان</inertia-link>
                        </li>
                        <li v-if="checkout == 1">
                            <inertia-link href="/admin/checkout">تسویه حساب ها</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 14">
                    <h4>حامل</h4>
                    <ul>
                        <li v-if="carrier == 1">
                            <inertia-link href="/admin/carrier">حامل</inertia-link>
                        </li>
                        <li v-if="time == 1">
                            <inertia-link href="/admin/time">زمان</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="allSideBarIconsText" v-if="index == 15">
                    <h4>تنوع ها</h4>
                    <ul>
                        <li v-if="variety == 1">
                            <inertia-link href="/admin/variety">همه تنوع ها</inertia-link>
                        </li>
                        <li v-if="post == 1">
                            <inertia-link href="/admin/post">همه محصول ها</inertia-link>
                        </li>
                    </ul>
                </div>
            </div>
        </VuePerfectScrollbar>
    </div>
</template>

<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import SvgIcon from "../../Svg/SvgIcon";
    export default {
        name: "SideBarPanel",
        components:{
            SvgIcon,
            VuePerfectScrollbar
        },
        data() {
            return {
                index: 0,
                i: 0,
                allows: [],
                ticket: 0,
                user: 0,
                role: 0,
                cat: 0,
                tag: 0,
                page: 0,
                time: 0,
                setting: 0,
                gallery: 0,
                comment: 0,
                guarantee: 0,
                post: 0,
                carrier:0,
                news: 0,
                brand: 0,
                pay: 0,
                dashboard: 0,
                notification: 0,
                robot: 0,
                view: 0,
                event: 0,
                excel1: 0,
                excel2: 0,
                feedback: 0,
                notification2: 0,
                seller: 0,
                checkout: 0,
                document: 0,
                variety: 0,
                employees: 0,
                discount: 0,
                inventory: 0,
                charge: 0,
                ranking: 0,
            }
        },
        methods:{
            sidebar2(){
                if (this.$page.allow.length){
                    this.allows = this.$page.allow;
                }
                if (this.$page.userData.admin == 1){
                    this.allows = [];
                    this.allows.push({name : 'admin'});
                }
                this.getAllow();
            },
            getAllow(){
                for ( this.i ; this.i <  this.allows.length; this.i++) {
                    if (this.allows[this.i].name == 'خروجی اکسل' || this.allows[this.i].name == 'admin'){
                        this.excel1 = 1;
                    }
                    if (this.allows[this.i].name == 'ورودی اکسل' || this.allows[this.i].name == 'admin'){
                        this.excel2 = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش مدارک' || this.allows[this.i].name == 'admin'){
                        this.document = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش تسویه حساب ها' || this.allows[this.i].name == 'admin'){
                        this.checkout = 1;
                    }
                    if (this.allows[this.i].name == 'رتبه بندی' || this.allows[this.i].name == 'admin'){
                        this.ranking = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش فروشنده' || this.allows[this.i].name == 'admin'){
                        this.seller = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش کارمندان' || this.allows[this.i].name == 'admin'){
                        this.employees = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش انبارداری' || this.allows[this.i].name == 'admin'){
                        this.inventory = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش بازخورد' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف بازخورد'){
                        this.feedback = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش اطلاع پست' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف اطلاع پست'){
                        this.notification2 = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش همه رباط ها' || this.allows[this.i].name == 'نمایش رباط های خودش' || this.allows[this.i].name == 'افزودن رباط' || this.allows[this.i].name == 'حذف رباط' || this.allows[this.i].name == 'admin'){
                        this.robot = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش همه اطلاع رسانی ها' || this.allows[this.i].name == 'نمایش اطلاع رسانی های خودش' || this.allows[this.i].name == 'افزودن اطلاع رسانی' || this.allows[this.i].name == 'حذف اطلاع رسانی' || this.allows[this.i].name == 'admin'){
                        this.notification = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش همه تصویر ها' || this.allows[this.i].name == 'نمایش تصویر های خودش' || this.allows[this.i].name == 'افزودن تصویر' || this.allows[this.i].name == 'حذف تصویر' || this.allows[this.i].name == 'admin'){
                        this.gallery = 1;
                    }
                    if (this.allows[this.i].name == 'تنظیمات دسته بندی ها' || this.allows[this.i].name == 'تنظیمات قالب سایت' || this.allows[this.i].name == 'تنظیمات سئو'  || this.allows[this.i].name == 'تنظیمات درگاه' || this.allows[this.i].name == 'تنظیمات سایت' || this.allows[this.i].name == 'تنظیمات دیدگاه' || this.allows[this.i].name == 'admin' ){
                        this.setting = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش همه کاربر ها' || this.allows[this.i].name == 'نمایش کاربر های خودش' || this.allows[this.i].name == 'افزودن کاربر' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف کاربر' || this.allows[this.i].name == 'ویرایش کاربر'){
                        this.user = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش بازدید' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف بازدید'){
                        this.view = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش رویداد' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف رویداد'){
                        this.event = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش مقام' || this.allows[this.i].name == 'افزودن مقام' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف مقام' || this.allows[this.i].name == 'ویرایش مقام'){
                        this.role = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش تنوع' || this.allows[this.i].name == 'افزودن تنوع' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف تنوع' || this.allows[this.i].name == 'ویرایش تنوع'){
                        this.variety = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش حامل' || this.allows[this.i].name == 'افزودن حامل' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف حامل' || this.allows[this.i].name == 'ویرایش حامل'){
                        this.carrier = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش زمان' || this.allows[this.i].name == 'افزودن زمان' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف زمان' || this.allows[this.i].name == 'ویرایش زمان'){
                        this.time = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش گارانتی' || this.allows[this.i].name == 'افزودن گارانتی' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف گارانتی' || this.allows[this.i].name == 'ویرایش گارانتی'){
                        this.guarantee = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش دسته های خودش' || this.allows[this.i].name == 'نمایش همه دسته ها' || this.allows[this.i].name == 'افزودن دسته' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف دسته' || this.allows[this.i].name == 'ویرایش دسته'){
                        this.cat = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش برچسب های خودش' || this.allows[this.i].name == 'نمایش همه برچسب ها' || this.allows[this.i].name == 'افزودن برچسب' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف برچسب' || this.allows[this.i].name == 'ویرایش برچسب'){
                        this.tag = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش کالا های خودش' || this.allows[this.i].name == 'نمایش همه کالا ها' || this.allows[this.i].name == 'افزودن کالا' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف کالا' || this.allows[this.i].name == 'ویرایش کالا'){
                        this.post = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش خبر های خودش' || this.allows[this.i].name == 'نمایش همه خبر ها' || this.allows[this.i].name == 'افزودن خبر' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف خبر' || this.allows[this.i].name == 'ویرایش خبر'){
                        this.news = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش برند های خودش' || this.allows[this.i].name == 'نمایش همه برند ها' || this.allows[this.i].name == 'افزودن برند' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف برند' || this.allows[this.i].name == 'ویرایش برند'){
                        this.brand = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش برگه' || this.allows[this.i].name == 'افزودن برگه' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف برگه' || this.allows[this.i].name == 'ویرایش برگه'){
                        this.page = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش شارژ' || this.allows[this.i].name == 'افزودن شارژ' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف شارژ' || this.allows[this.i].name == 'ویرایش شارژ'){
                        this.charge = 1;
                    }
                    if (this.allows[this.i].name == 'نمایش درخواست' || this.allows[this.i].name == 'فرستادن ایمیل' || this.allows[this.i].name == 'افزودن درخواست' || this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف درخواست' || this.allows[this.i].name == 'ویرایش درخواست'){
                        this.ticket = 1;
                    }
                    if (this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف پرداختی' || this.allows[this.i].name == 'نمایش پرداختی'){
                        this.pay = 1;
                    }
                    if (this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'حذف دیدگاه' || this.allows[this.i].name == 'ویرایش دیدگاه'){
                        this.comment = 1;
                    }
                    if (this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'نمایش داشبورد'){
                        this.dashboard = 1;
                    }
                    if (this.allows[this.i].name == 'admin' || this.allows[this.i].name == 'افزودن کد تخفیف'){
                        this.discount = 1;
                    }
                }
            },
            sidebar(item){
                this.index = item;
            },
        },
        mounted(){
            this.sidebar2();
        },
        created: function() {
            this.$eventHub.on('sidebar', this.sidebar);
        },
    }
</script>

<style scoped>

</style>
