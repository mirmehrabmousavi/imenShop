<template>
    <admin-layout>
        <div class="allDashboard">
            <div class="allDashboardTitle">
                <h1>داشبورد</h1>
                <div class="title">
                    <inertia-link href="/">خانه</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin">داشبورد</inertia-link>
                </div>
            </div>
            <div class="topPanelVidgets">
                <div class="topPanelVidgetsPic">
                    <img src="/img/backVidgetPanel.jpg">
                </div>
                <div class="topPanelVidget">
                    <h4>امروز</h4>
                    <h2>{{viewDay|NumFormat}}</h2>
                    <span>تعداد بازدید</span>
                </div>
                <div class="topPanelVidget">
                    <h4>دیروز</h4>
                    <h2>{{viewYesterday|NumFormat}}</h2>
                    <span>تعداد بازدید</span>
                </div>
                <div class="topPanelVidget">
                    <h4>این هفته</h4>
                    <h2>{{viewWeek|NumFormat}}</h2>
                    <span>تعداد بازدید</span>
                </div>
                <div class="topPanelVidget">
                    <h4>این ماه</h4>
                    <h2>{{viewMonth|NumFormat}}</h2>
                    <span>تعداد بازدید</span>
                </div>
                <div class="topPanelVidget">
                    <h4>امسال</h4>
                    <h2>{{viewYear|NumFormat}}</h2>
                    <span>تعداد بازدید</span>
                </div>
            </div>
            <div class="allDashboardLastPost">
                <label>آخرین کالا ها</label>
                <table>
                    <tr>
                        <div>
                            <th>#</th>
                            <th>تصویر</th>
                            <th>عنوان</th>
                            <th>وضعیت</th>
                            <th>مبلغ</th>
                            <th>موجودیت</th>
                            <th>تاریخ ثبت</th>
                        </div>
                    </tr>
                    <tr v-for="(item , index) in lastPost" :key="index">
                        <div>
                            <td>{{++index}}</td>
                            <td>
                                <span class="tablePic">
                                    <img :src="JSON.parse(item.image)[0]">
                                </span>
                            </td>
                            <td>
                                <span>{{item.title}}</span>
                            </td>
                            <td>
                                <span>
                                    <span class="unActive" v-if="item.status == 0">پیشنویس</span>
                                    <span class="activeStatus" v-else>منتشر شده</span>
                                </span>
                            </td>
                            <td>
                                <span>{{item.price|NumFormat}} تومان</span>
                            </td>
                            <td>
                                <span>
                                    <span class="unActive" v-if="item.count == 0">ناموجود</span>
                                    <span class="activeStatus" v-else>موجود</span>
                                </span>
                            </td>
                            <td>
                                <span>{{item.created_at}}</span>
                            </td>
                        </div>
                    </tr>
                </table>
            </div>
            <div class="allDashboardComments">
                <div class="allDashboardCommentsItems">
                    <label>آخرین دیدگاه ها</label>
                    <table>
                        <tr>
                            <div>
                                <th>#</th>
                                <th>کالا</th>
                                <th>نویسنده</th>
                                <th>توضیح</th>
                            </div>
                        </tr>
                        <tr v-for="(item , index) in lastComment" :key="index">
                            <div>
                                <td>{{++index}}</td>
                                <td>
                                    <span>
                                        <inertia-link class="tablePic" :href="'/product/' + item.post.slug">
                                            <img :src="JSON.parse(item.post.image)[0]">
                                        </inertia-link>
                                    </span>
                                </td>
                                <td>
                                    <span>{{item.user.name}}</span>
                                </td>
                                <td>
                                    <span>{{item.body}}</span>
                                </td>
                            </div>
                        </tr>
                    </table>
                </div>
                <div class="allDashboardCommentsChart">
                    <apexchart width="500px" type="donut" :options="chartOptions" :series="series"></apexchart>
                </div>
            </div>
            <div class="allDashboardPay">
                <label>آمار پرداختی ها</label>
                <apexchart height="400px" type="area" :options="chartPay" :series="seriesPay"></apexchart>
            </div>
            <div class="allDashboardUsers">
                <div class="allDashboardUsersChart">
                    <apexchart width="500px" type="donut" :options="userOptions" :series="userSeries"></apexchart>
                </div>
                <div class="allDashboardUsersItem">
                    <label>آخرین کاربران</label>
                    <table>
                        <tr>
                            <div>
                                <th>#</th>
                                <th>نام</th>
                                <th>تاریخ ثبت</th>
                            </div>
                        </tr>
                        <tr v-for="(item , index) in lastUser" :key="index">
                            <div>
                                <td>
                                    <span>{{++index}}</span>
                                </td>
                                <td>
                                    <span>{{item.name}}</span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                            </div>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../components/layout/AdminLayout";
import { Hooper, Slide} from 'hooper';
import 'hooper/dist/hooper.css';
import SvgIcon from "../Svg/SvgIcon";
import Vue from 'vue';
import VueApexCharts from 'vue-apexcharts';
Vue.use(VueApexCharts);

Vue.component('apexchart', VueApexCharts)
export default {
    name: "panel",
    props : ['allow','userData','onlineUser','viewYear','viewWeek','viewDay','viewYesterday','viewMonth','offUser','lastComment','lastUser','farvardinPay','ordibeheshtPay','khordadPay','tirPay','mordadPay','shahrivarPay','mehrPay','abanPay','azarPay','deyPay','bahmanPay','esfandPay','checkComment','acceptComment','lastPost'],
    components:{
        Hooper,
        SvgIcon,
        Slide,
        AdminLayout
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                infiniteScroll:true,
                centerMode: false,
                transition: 2000,
                autoPlay:true,
                playSpeed : 2200,
                itemsToSlide: 1,
                breakpoints: {
                    1100: {
                        itemsToShow: 1,
                    },
                    1200: {
                        itemsToShow: 2,
                    },
                    1300: {
                        itemsToShow: 3,
                    },
                    1400: {
                        itemsToShow: 4,
                    },
                }
            },
            allows: [],
            chartOptions:{
                labels: ['در انتظار تایید','تایید شده'],
                chart:{
                    fontFamily:'irsans, irsans, sans-serif',
                    height:'auto',
                    width:'100%',
                },
                plotOptions:{
                    pie:{
                        donut:{
                            labels:{
                                show: true,
                            }
                        }
                    }
                }
            },
            series:[this.checkComment,this.acceptComment],
            chartPay: {
                chart: {
                    id: 'vuechart-example',
                    fontFamily:'irsans, irsans, sans-serif',
                    fontWeight:'300',
                },
                labels:['فروردین', 'اردیبهشت', 'خرداد', 'تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'],
            },
            seriesPay: [{
                name: 'تومان',
                data: [this.farvardinPay, this.ordibeheshtPay, this.khordadPay, this.tirPay,this.mordadPay,this.shahrivarPay,this.mehrPay,this.abanPay,this.azarPay,this.deyPay,this.bahmanPay,this.esfandPay],
            }],
            userOptions:{
                labels: ['آفلاین','آنلاین'],
                chart:{
                    fontFamily:'irsans, irsans, sans-serif',
                    height:'auto',
                    width:'100%',
                },
                plotOptions:{
                    pie:{
                        donut:{
                            labels:{
                                show: true,
                            }
                        }
                    }
                }
            },
            userSeries:[this.offUser,this.onlineUser],
        }
    },
    metaInfo: {
        title: 'داشبورد'
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , '0');
        }
    },
    mounted() {
        this.sidebar();
    },
}
</script>

<style scoped>

</style>
