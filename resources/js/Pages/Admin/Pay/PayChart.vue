<template>
    <admin-layout>
        <div class="allPayChart">
            <div class="allPayChartData">
                <div class="items">
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>کل مبلغ فروش</h4>
                            <h5>{{allPrice|NumFormat}} تومان</h5>
                        </div>
                    </div>
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>کل سفارشات</h4>
                            <h5>{{allPay|NumFormat}} عدد</h5>
                        </div>
                    </div>
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>فروش این هفته</h4>
                            <h5>{{ weekPrice|NumFormat }} تومان</h5>
                        </div>
                    </div>
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>سفارشات این هفته</h4>
                            <h5>{{weekPay|NumFormat}} عدد</h5>
                        </div>
                    </div>
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>فروش این ماه</h4>
                            <h5>{{ monthPrice|NumFormat }} تومان</h5>
                        </div>
                    </div>
                    <div class="item">
                        <div class="itemIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="itemSubject">
                            <h4>سفارشات این ماه</h4>
                            <h5>{{monthPay|NumFormat}} عدد</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="allPayChartTops">
                <div class="title">پرفروش ترین محصولات</div>
                <div class="items">
                    <div v-for="(item , index) in tops" class="allPayChartTopsItem">
                        <h2>#{{ ++index }}</h2>
                        <inertia-link :href="'/product/' + item.slug" class="pic">
                            <img :alt="item.title" :src="JSON.parse(item.image)[0]">
                        </inertia-link>
                        <inertia-link :href="'/product/' + item.slug">{{ item.title }}</inertia-link>
                        <div class="allDashboardVidgetOptionMid">
                            <div class="allDashboardVidgetOptionMidItem">
                                <div class="allDashboardVidgetOptionMidItemTitle">
                                    <h5>کل خرید این محصول</h5>
                                    <span>{{ item.pay_meta_count|NumFormat }}</span>
                                </div>
                                <div class="allDashboardVidgetOptionMidItemSize">
                                    <div :style="{'width' : (item.pay_meta_count * '100') / item.pay_meta_count +'%'}"
                                         class="allDashboardVidgetOptionMidItemSizeFill">
                                        <span>{{ (item.pay_meta_count * '100') / item.pay_meta_count }} %</span>
                                    </div>
                                </div>
                            </div>
                            <div class="allDashboardVidgetOptionMidItem">
                                <div class="allDashboardVidgetOptionMidItemTitle">
                                    <h5>خرید امروز</h5>
                                    <span>{{ item.pay_meta.length|NumFormat }}</span>
                                </div>
                                <div class="allDashboardVidgetOptionMidItemSize">
                                    <div :style="{'width' : (item.pay_meta.length * '100') / item.pay_meta_count +'%'}"
                                         class="allDashboardVidgetOptionMidItemSizeFill">
                                        <span
                                            v-if="item.pay_meta.length >= 1">{{ (item.pay_meta.length * '100') / item.pay_meta_count }} %</span>
                                    </div>
                                </div>
                            </div>
                            <div class="allDashboardVidgetOptionMidItem">
                                <div class="allDashboardVidgetOptionMidItemTitle">
                                    <h5>باقیمانده این محصول</h5>
                                    <span>{{ (item.count + item.pay_meta_count)|NumFormat }}</span>
                                </div>
                                <div class="allDashboardVidgetOptionMidItemSize">
                                    <div :style="{'width' : (item.count * '100') / (item.count + item.pay_meta_count) +'%'}"
                                         class="allDashboardVidgetOptionMidItemSizeFill">
                                        <span
                                            v-if="item.count >= 1">{{ (item.count * '100') / (item.count + item.pay_meta_count) }} %</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="allPayChartToday">
                <div class="title">پرداختی های امروز</div>
                <div class="items">
                    <div class="item">
                        <apexchart :options="chartOptions" :series="series" type="donut" width="500px"></apexchart>
                    </div>
                    <div class="item">
                        <apexchart :options="chartOptions2" :series="series2" type="donut" width="500px"></apexchart>
                    </div>
                </div>
            </div>
            <div class="allPayChartLast">
                <h2>آخرین پرداختی ها</h2>
                <table>
                    <tr>
                        <div>
                            <th>#</th>
                            <th>محصول</th>
                            <th>آیدی خریدار - نام خریدار</th>
                            <th>تعداد</th>
                            <th>وضعیت خرید</th>
                            <th>زمان خرید</th>
                        </div>
                    </tr>
                    <tr v-for="(item, index) in lastPay" :key="index">
                        <div>
                            <td>
                                <span>{{++index}}</span>
                            </td>
                            <td>
                                <span>{{item.post.title}}</span>
                            </td>
                            <td>
                                <span>{{item.user.name}} - {{item.user.id}}</span>
                            </td>
                            <td>
                                <span>{{item.count}}</span>
                            </td>
                            <td>
                                <span>
                                    <span v-if="item.status == 100" class="activeStatus">پرداخت شده</span>
                                    <span v-else class="unActive">در حال پرداخت</span>
                                </span>
                            </td>
                            <td>
                                <span>{{item.created_at}}</span>
                            </td>
                        </div>
                    </tr>
                </table>
            </div>
            <div class="allDashboardPay">
                <label>آمار پرداختی ها</label>
                <apexchart height="400px" type="area" :options="chartPay" :series="seriesPay"></apexchart>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import Vue from 'vue';
import VueApexCharts from 'vue-apexcharts';
Vue.use(VueApexCharts);
Vue.component('apexchart', VueApexCharts);
export default {
    name: "PayChart",
    components: {AdminLayout,SvgIcon},
    props: ['tops', 'unPay', 'farvardinPay','ordibeheshtPay','khordadPay','tirPay','mordadPay','shahrivarPay','mehrPay','abanPay','azarPay','deyPay','bahmanPay','esfandPay' ,'acceptPay', 'lastPay', 'notDeliver', 'deliver' , 'allPrice' , 'weekPrice' , 'monthPrice' , 'allPay' , 'weekPay' , 'monthPay'],
    metaInfo: {
        title: 'آمار پرداختی ها'
    },
    data() {
        return {
            chartOptions: {
                labels: ['پرداخت شده', 'پرداخت نشده'],
                chart: {
                    fontFamily: 'irsans, irsans, sans-serif',
                    height: 'auto',
                    width: '100%',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                            }
                        }
                    }
                }
            },
            chartOptions2: {
                labels: ['تحویل داده شده', 'در حال بررسی'],
                chart: {
                    fontFamily: 'irsans, irsans, sans-serif',
                    height: 'auto',
                    width: '100%',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                            }
                        }
                    }
                }
            },
            series: [this.acceptPay, this.unPay],
            series2: [this.deliver, this.notDeliver],
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
        }
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , '7');
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
