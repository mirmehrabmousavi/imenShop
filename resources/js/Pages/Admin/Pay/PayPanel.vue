<template>
    <admin-layout>
        <div class="allPayPanel">
            <div class="allPayPanelTop">
                <h1>پرداختی ها</h1>
                <div class="allPayPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/pay">پرداختی ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length">حذف</button>
                        <button class="button" @click="showFast(value[0])" v-if="value.length == 1">نمایش سریع</button>
                    </div>
                    <div class="allTopTableItem">
                        <div class="filterItems">
                            <div class="ContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
                                <i>
                                    <svg-icon :icon="'#filter'"></svg-icon>
                                </i>
                                فیلتر اطلاعات
                            </div>
                            <transition name="bounce">
                                <div class="filterContent" v-if="showFilter">
                                    <div class="filterContentItem">
                                        <label>فیلتر شماره تراکنش پرداخت یا شماره سفارش</label>
                                        <input type="text" v-model="search"  placeholder="شماره تراکنش پرداخت یا شماره سفارش را وارد کنید" @keypress.enter="btnSearch(0)">
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر تاریخ</label>
                                        <div class="allTicketPanelTitleDate">
                                            <date-picker
                                                v-model="date"
                                                type="datetime"
                                                format="YYYY-MM-DD"
                                                display-format="jYYYY-jMM-jDD"
                                                @close="btnSearch(0)"
                                                placeholder="تاریخ را وارد کنید"
                                                :timezone="true"
                                            />
                                            <i @click.stop="btnSearch(1)" v-if="date">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر وضعیت پرداخت</label>
                                        <div class="allCategoryPanel" @click="showSort = !showSort">
                                            <div class="categoryShow">
                                                <h4 v-if="sort == 0">همه</h4>
                                                <h4 v-if="sort == 1">پرداخت نشده</h4>
                                                <h4 v-if="sort == 2">پرداخت شده</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showSort">
                                                <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                                <li @click="sort = 1" v-on:click="btnSearch(0)">پرداخت نشده</li>
                                                <li @click="sort = 2" v-on:click="btnSearch(0)">پرداخت شده</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر وضعیت ارسال</label>
                                        <div class="allCategoryPanel" @click="showSortDeliver = !showSortDeliver">
                                            <div class="categoryShow">
                                                <h4 v-if="sortDeliver == 5">همه</h4>
                                                <h4 v-if="sortDeliver == 0">دریافت سفارش</h4>
                                                <h4 v-if="sortDeliver == 1">در انتظار بررسی</h4>
                                                <h4 v-if="sortDeliver == 2">بسته بندی شده</h4>
                                                <h4 v-if="sortDeliver == 3">تحویل پیک</h4>
                                                <h4 v-if="sortDeliver == 4">تکمیل شده</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showSortDeliver">
                                                <li @click="sortDeliver = 5" v-on:click="btnSearch(0)">همه</li>
                                                <li @click="sortDeliver = 0" v-on:click="btnSearch(0)">دریافت سفارش</li>
                                                <li @click="sortDeliver = 1" v-on:click="btnSearch(0)">در انتظار بررسی</li>
                                                <li @click="sortDeliver = 2" v-on:click="btnSearch(0)">بسته بندی شده</li>
                                                <li @click="sortDeliver = 3" v-on:click="btnSearch(0)">تحویل پیک</li>
                                                <li @click="sortDeliver = 4" v-on:click="btnSearch(0)">تکمیل شده</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>تعداد نمایش</label>
                                        <div class="allCategoryPanel" @click="showPage = !showPage">
                                            <div class="categoryShow">
                                                <h4 v-if="getPage == 25">25</h4>
                                                <h4 v-if="getPage == 50">50</h4>
                                                <h4 v-if="getPage == 70">70</h4>
                                                <h4 v-if="getPage == 100">100</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showPage">
                                                <li @click="getPage = 25" v-on:click="btnSearch(0)">25</li>
                                                <li @click="getPage = 50" v-on:click="btnSearch(0)">50</li>
                                                <li @click="getPage = 70" v-on:click="btnSearch(0)">70</li>
                                                <li @click="getPage = 100" v-on:click="btnSearch(0)">100</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
                <div class="paginate">
                    <paginate-panel :link="pays.links"></paginate-panel>
                </div>
                <div class="allTableContainer" v-if="show">
                    <div class="postItem" v-for="item in pays.data" @click="getCheck(item.id)">
                        <div class="postTop">
                            <div class="postTitle">
                                <div class="postImages">
                                    <div class="postImage" v-for="post in item.pay_meta.slice(0,3)">
                                        <img :src="JSON.parse(post.post.image)[0]" :alt="post.post.title">
                                    </div>
                                    <div class="postMore" v-if="item.pay_meta.length >= 4">
                                        <i>
                                            <svg-icon :icon="'#more'"></svg-icon>
                                        </i>
                                    </div>
                                </div>
                                <ul v-if="item.time">
                                    <li>
                                        <span>روزی که باید ارسال بشه :</span>
                                        <span>
                                            {{JSON.parse(item.time).dayL}}
                                            {{JSON.parse(item.time).day}}
                                            {{JSON.parse(item.time).month}}
                                        </span>
                                    </li>
                                    <li>
                                        <span>ساعتی که باید ارسال بشه :</span>
                                        <span>
                                            00::{{JSON.parse(item.time).from}}
                                            -
                                            00::{{JSON.parse(item.time).to}}
                                        </span>
                                    </li>
                                </ul>
                                <ul v-else>
                                    <li>
                                        <span>نوع محصول :</span>
                                        <span>محصول دانلودی</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="postOptions">
                                <a :href="'/admin/pay/invoice/' + item.id" title="پرینت خرید">
                                    <svg-icon :icon="'#print'"></svg-icon>
                                </a>
                                <a :href="'/admin/get-excel/pay?pay=' + item.id" title="دریافت اکسل">
                                    <svg-icon :icon="'#excel2'"></svg-icon>
                                </a>
                                <inertia-link :href="'/admin/show-pay/' + item.id" title="مشاهده خرید">
                                    <svg-icon :icon="'#eye'"></svg-icon>
                                </inertia-link>
                                <i v-if="deleted" title="حذف خرید" @click="btnRemoveArray(item.id)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </div>
                        </div>
                        <div class="postBot">
                            <ul>
                                <li>
                                    <span>نوع ارسال :</span>
                                    <span v-if="item.carrier.length">{{item.carrier[0].name}}</span>
                                    <span v-else>نامشخص</span>
                                </li>
                                <li>
                                    <span>مبلغ پرداختی :</span>
                                    <span>{{ item.price|NumFormat }} تومان</span>
                                </li>
                                <li>
                                    <span>شماره سفارش :</span>
                                    <span>{{item.property}}</span>
                                </li>
                                <li>
                                    <span>وضعیت پرداخت :</span>
                                    <span v-if="item.status == 100">پرداخت شده</span>
                                    <span v-else>پرداخت نشده</span>
                                </li>
                                <li>
                                    <span>وضعیت ارسال :</span>
                                    <span class="unActive" v-if="item.deliver == 0">دریافت سفارش</span>
                                    <span class="unActive" v-if="item.deliver == 1">در انتظار بررسی</span>
                                    <span class="unActive" v-if="item.deliver == 2">بسته بندی شده</span>
                                    <span class="unActive" v-if="item.deliver == 3">تحویل پیک</span>
                                    <span class="activeStatus" v-if="item.deliver == 4">تکمیل شده</span>
                                </li>
                            </ul>
                            <i>
                                <svg-icon v-for="(values , index2) in value" :key="index2" v-if="values == item.id" :icon="'#check'"></svg-icon>
                                <svg-icon :icon="'#uncheck'"></svg-icon>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="paginate">
                    <div class="showInfo">
                        نمایش
                        {{pays.from}}
                        تا
                        {{pays.to}}
                        از
                        {{pays.total}}
                        ورودی
                    </div>
                    <paginate-panel :link="pays.links"></paginate-panel>
                </div>
            </div>
            <div class="allShowFastPost" v-if="showPayMeta != ''">
                <div class="allShowFastPostPanel">
                    <ul>
                        <li v-for="item in showPayMeta">
                            <div class="payMetaItem">
                                <div class="payMetaPic"><img :src="JSON.parse(item.post.image)[0]"></div>
                                <div class="payMetaSubject">
                                    <div class="payMetaSubjectTitle">{{item.post.title}}</div>
                                    <div class="payMetaSubjectItem" v-if="item.color">
                                        <i>
                                            <svg-icon :icon="'#color'"></svg-icon>
                                        </i>
                                        {{JSON.parse(item.color).name}}
                                    </div>
                                    <div class="payMetaSubjectItem" v-if="item.size">
                                        <i>
                                            <svg-icon :icon="'#size'"></svg-icon>
                                        </i>
                                        {{JSON.parse(item.size).name}}
                                    </div>
                                    <div class="payMetaSubjectItem" v-if="item.count">
                                        {{item.count}}
                                        عدد
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="allCategoryPanel" @click="showStatus2 = !showStatus2">
                        <div class="categoryShow">
                            <h4 v-if="showStatus.deliver == 0">دریافت سفارش</h4>
                            <h4 v-if="showStatus.deliver == 1">در انتظار بررسی</h4>
                            <h4 v-if="showStatus.deliver == 2">بسته بندی شده</h4>
                            <h4 v-if="showStatus.deliver == 3">تحویل پیک</h4>
                            <h4 v-if="showStatus.deliver == 4">تکمیل شده</h4>
                            <i>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <ul v-if="showStatus2">
                            <li @click="showStatus.deliver = 0">دریافت سفارش</li>
                            <li @click="showStatus.deliver = 1">در انتظار بررسی</li>
                            <li @click="showStatus.deliver = 2">بسته بندی شده</li>
                            <li @click="showStatus.deliver = 3">تحویل پیک</li>
                            <li @click="showStatus.deliver = 4">تکمیل شده</li>
                        </ul>
                    </div>
                    <div class="userInfo">
                        <div class="userInfoItem">
                            <label>مبلغ خریداری شده :</label>
                            <span>{{showStatus.price|NumFormat}} تومان</span>
                        </div>
                        <div class="userInfoItem">
                            <label>وضعیت خریداری :</label>
                            <span v-if="showStatus.deliver == 0">دریافت سفارش</span>
                            <span v-if="showStatus.deliver == 1">در انتظار بررسی</span>
                            <span v-if="showStatus.deliver == 2">بسته بندی شده</span>
                            <span v-if="showStatus.deliver == 3">تحویل پیک</span>
                            <span v-if="showStatus.deliver == 4">تکمیل شده</span>
                        </div>
                        <div class="userInfoItem" v-if="showPrint.time">
                            <label>روز ارسال :</label>
                            <span>
                                {{JSON.parse(showStatus.time).dayL}}
                            </span>
                        </div>
                        <div class="userInfoItem" v-if="showPrint.time">
                            <label>بازه زمانی ارسال :</label>
                            <span>
                                {{JSON.parse(showStatus.time).from}}
                                -
                                {{JSON.parse(showStatus.time).to}}
                            </span>
                        </div>
                        <div class="userInfoItem" v-if="showPrint.carrier">
                            <label>نوع ارسال :</label>
                            <span v-if="showPrint.carrier.length">{{showStatus.carrier[0].title}}</span>
                            <span v-else>نامشخص</span>
                        </div>
                        <div class="userInfoItem">
                            <label>زمان خرید :</label>
                            <span>{{showStatus.created_at}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>شماره سفارش :</label>
                            <span>{{showStatus.property}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>آیدی خریدار :</label>
                            <span>{{showStatus.user.id}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>نام خریدار :</label>
                            <span>{{showStatus.user.user_meta[0].name}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>شماره تماس خریدار :</label>
                            <span>{{showStatus.user.number}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>کد پستی :</label>
                            <span>{{showStatus.user.user_meta[0].post}}</span>
                        </div>
                        <div class="userInfoItem">
                            <label>آدرس خریدار :</label>
                            <span>{{showStatus.user.user_meta[0].address}}</span>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="sendUpdate">اعمال تغییر</button>
                        <button @click="showPayMeta = []">انصراف</button>
                    </div>
                </div>
            </div>
            <div class="printPay" id="printMe" v-if="showPrint != ''">
                <div class="printPayItems" style="direction: rtl">
                    <div class="userInfo">
                        <div class="userInfoItem" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>مبلغ خریداری شده :</label>
                            <span>{{showPrint.price|NumFormat}} تومان</span>
                        </div>
                        <div class="userInfoItem" v-if="showPrint.carrier.length" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>نوع ارسال :</label>
                            <span>{{showPrint.carrier[0].title}}</span>
                        </div>
                        <div class="userInfoItem" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>زمان خرید :</label>
                            <span>{{showPrint.created_at}}</span>
                        </div>
                        <div class="userInfoItem" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>شماره سفارش :</label>
                            <span>{{showPrint.property}}</span>
                        </div>
                        <div class="userInfoItem" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>نام خریدار :</label>
                            <span>{{showPrint.user.user_meta[0].name}}</span>
                        </div>
                        <div class="userInfoItem" style="border-bottom: 1.5px solid black; padding: 1rem 0;">
                            <label>شماره تماس خریدار :</label>
                            <span>{{showPrint.user.number}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import AllTable from '../Table/AllTable.vue';
export default {
    name: "PayPanel",
    components:{
        AdminLayout,
        PaginatePanel,
        datePicker: VuePersianDatetimePicker,
        SvgIcon,
        AllTable
    },
    props:['pays','deleted','labels','deleted','show'],
    metaInfo: {
        title: 'پرداختی ها'
    },
    data() {
        return {
            search : '',
            date : '',
            sort : 0,
            sortDeliver : 0,
            i : 0,
            getPage : 25,
            showPage : false,
            showFilter : false,
            showStatus2 : false,
            showSort : false,
            showSortDeliver : false,
            showPayMeta : [],
            showStatus : [],
            showPrint: [],
            postId : '',
            value : [],
        }
    },
    methods:{
        sendUpdate(){
            const url = `/admin/pay`;
            this.$inertia.post(url , {
                deliver : this.showStatus,
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortDeliver : this.sortDeliver,
                date : this.date,
            })
                .then(response=>{
                    this.showPayMeta = [];
                })
        },
        getPrint(id){
            this.showPrint = [];
            const url = `/admin/pay/${id}`;
            axios.get(url)
                .then(response=>{
                    this.showPrint = response.data[0];
                    setTimeout(() => this.$htmlToPaper('printMe'), 200);
                })
        },
        showFast(id){
            const url = `/admin/pay/${id}`;
            axios.get(url)
                .then(response=>{
                    this.showPayMeta = response.data[1];
                    this.showStatus = response.data[0];
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/pay`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortDeliver : this.sortDeliver,
                date : this.date,
            })
        },
        getCheck(id) {
            for (this.i; this.i < this.value.length; this.i++) {
                if (this.value[this.i] == id) {
                    this.value.splice(this.i, 1);
                    this.i = 100;
                }
            }
            if (this.i != 101) {
                this.value.push(id);
            }
            this.i = 0;
        },
        btnRemoveArray(id){
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
                    if(id){
                        this.value = [id]
                    }
                    const url = `/admin/pay`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '7');
        }
    },
    mounted(){
        this.sidebar();
    }
}
</script>
