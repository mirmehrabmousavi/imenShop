<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="8"></user-list>
            </div>
            <div class="allPayPanel">
                <div class="vidgetItems">
                    <div class="vidgetItem">
                        <div class="vidgetIcon">
                            <svg-icon :icon="'#payList'"></svg-icon>
                        </div>
                        <div class="vidgetSubject">
                            <h4>{{ $t('earnToday') }}</h4>
                            <h5 v-if="$i18n.locale == 'fa'">{{payToday|NumFormat}} تومان</h5>
                            <h5 v-if="$i18n.locale == 'en'" class="en">{{payToday|NumFormat}} tooman</h5>
                        </div>
                    </div>
                    <div class="vidgetItem">
                        <div class="vidgetIcon">
                            <svg-icon :icon="'#payList'"></svg-icon>
                        </div>
                        <div class="vidgetSubject">
                            <h4>{{ $t('earningsWeek') }}</h4>
                            <h5 v-if="$i18n.locale == 'fa'">{{payWeek|NumFormat}} تومان</h5>
                            <h5 v-if="$i18n.locale == 'en'" class="en">{{payWeek|NumFormat}} tooman</h5>
                        </div>
                    </div>
                    <div class="vidgetItem">
                        <div class="vidgetIcon">
                            <svg-icon :icon="'#payList'"></svg-icon>
                        </div>
                        <div class="vidgetSubject">
                            <h4>{{ $t('totalIncome') }}</h4>
                            <h5 v-if="$i18n.locale == 'fa'">{{allPays|NumFormat}} تومان</h5>
                            <h5 v-if="$i18n.locale == 'en'" class="en">{{allPays|NumFormat}} tooman</h5>
                        </div>
                    </div>
                </div>
                <div class="allTable">
                    <div class="paginate">
                        <paginate-panel :link="pays.links"></paginate-panel>
                    </div>
                    <div class="allTableContainer">
                        <table>
                            <tr>
                                <div>
                                    <th>#</th>
                                    <th>{{ $t('orderNumber') }}</th>
                                    <th>{{ $t('user') }}</th>
                                    <th>{{ $t('product') }}</th>
                                    <th>{{ $t('price') }}</th>
                                    <th>{{ $t('theOperation') }}</th>
                                </div>
                            </tr>
                            <tr v-for="(item, index) in pays.data" :key="index">
                                <div>
                                    <td>
                                        <span>{{++index}}</span>
                                    </td>
                                    <td>
                                        <span>{{item.pay.property}}</span>
                                    </td>
                                    <td>
                                        <span>{{item.user.name}}</span>
                                    </td>
                                    <td>
                                        <span v-if="$i18n.locale == 'fa'">{{item.post.title}}</span>
                                        <span v-if="$i18n.locale == 'en'">{{item.post.titleEn}}</span>
                                    </td>
                                    <td>
                                        <span :class="$i18n.locale">{{item.price|NumFormat}} تومان </span>
                                    </td>
                                    <td>
                                        <span>
                                            <i @click="btnShow(item.id)"><svg-icon :icon="'#eye'"></svg-icon></i>
                                        </span>
                                    </td>
                                </div>
                            </tr>
                        </table>
                    </div>
                    <div class="paginate">
                        <div class="showInfo" v-if="$i18n.locale == 'fa'">
                            نمایش
                            {{pays.from}}
                            تا
                            {{pays.to}}
                            از
                            {{pays.total}}
                            ورودی
                        </div>
                        <div class="showInfo en" v-if="$i18n.locale == 'en'">
                            the show
                            {{pays.from}}
                            until the
                            {{pays.to}}
                            From
                            {{pays.total}}
                            Entrance
                        </div>
                        <paginate-panel :link="pays.links"></paginate-panel>
                    </div>
                    <div class="allShowFastPost" v-if="showPay != ''">
                        <div class="allShowFastPostPanel">
                            <div class="userInfo">
                                <div class="userInfoItem">
                                    <label>مبلغ خرید :</label>
                                    <span>{{showPay.price|NumFormat}} تومان</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>محصول :</label>
                                    <span>
                                        <inertia-link :href="'/product/'+ showPay.post.slug">
                                            {{showPay.post.title}}
                                        </inertia-link>
                                    </span>
                                </div>
                                <div class="userInfoItem">
                                    <label>وضعیت پرداخت :</label>
                                    <span v-if="showPay.status == 100">پرداخت شده</span>
                                    <span v-else-if="showPay.status == 50">پرداخت بیعانه</span>
                                    <span v-else>پرداخت نشده</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>وضعیت ارسال :</label>
                                    <span v-if="showPay.pay.deliver == 0">{{$t('takingOrders')}}</span>
                                    <span v-if="showPay.pay.deliver == 1">{{ $t('awaitingReview') }}</span>
                                    <span v-if="showPay.pay.deliver == 2">{{$t('packaged')}}</span>
                                    <span v-if="showPay.pay.deliver == 3">{{$t('courierDelivery')}}</span>
                                    <span v-if="showPay.pay.deliver == 4">{{$t('completed')}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label v-if="JSON.parse(showPay.color) != ''">رنگ محصول :</label>
                                    <span>{{JSON.parse(showPay.color).name}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label v-if="JSON.parse(showPay.size) != ''">سایز :</label>
                                    <span>{{JSON.parse(showPay.size).name}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>تعداد سفارش :</label>
                                    <span>{{showPay.count}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>زمان تحویل :</label>
                                    <span>{{showPay.count}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>زمان پرداخت :</label>
                                    <span>{{showPay.created_at}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>شماره سفارش :</label>
                                    <span>{{showPay.pay.property}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>آیدی خریدار :</label>
                                    <span>{{showPay.user.id}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>نام خریدار :</label>
                                    <span>{{showPay.user.user_meta[0].name}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>شماره تماس خریدار :</label>
                                    <span>{{showPay.user.number}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>کد پستی خریدار :</label>
                                    <span>{{showPay.user.user_meta[0].post}}</span>
                                </div>
                                <div class="userInfoItem">
                                    <label>آدرس خریدار :</label>
                                    <span>{{showPay.user.user_meta[0].address}}</span>
                                </div>
                            </div>
                            <div class="buttons">
                                <button @click="showPay = []">انصراف</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import HomeLayout from "../../../components/layout/HomeLayout";
import PaginatePanel from "../../Admin/PaginatePanel";
import UserList from "./UserList";
export default {
    name: "PayProduct",
    components:{
        UserList,
        PaginatePanel,
        HomeLayout,
        datePicker: VuePersianDatetimePicker,
        SvgIcon,
    },
    props:['pays','allPays','payToday','payWeek'],
    metaInfo: {
        title: 'لیست فروش'
    },
    data() {
        return {
            showPay : [],
        }
    },
    methods:{
        btnShow(id){
            const url = `/profile/product/pay/${id}`;
            axios.get(url)
                .then(response=>{
                    this.showPay = response.data;
                })
        },
    },
}
</script>
