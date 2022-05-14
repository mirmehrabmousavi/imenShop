<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="4"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <label>{{$t('myOrders')}}</label>
                <div class="allUserIndexInfoPayFilter">
                    <div class="allUserIndexInfoPayFilterItem" @click="btnPay(0)">
                            <span class="active" v-if="show == 0">{{$t('all')}}</span>
                            <span v-else>{{$t('all')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnPay(1)">
                        <span class="active" v-if="show == 1">{{$t('paid')}}</span>
                        <span v-else>{{$t('paid')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnPay(2)">
                        <span class="active" v-if="show == 2">{{$t('unpaid')}}</span>
                        <span v-else>{{$t('unpaid')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnPay(3)">
                        <span class="active" v-if="show == 3">{{$t('delivered')}}</span>
                        <span v-else>{{$t('delivered')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnPay(4)">
                        <span class="active" v-if="show == 4">{{$t('sending')}}</span>
                        <span v-else>{{$t('sending')}}</span>
                    </div>
                </div>
                <div class="paginate" v-if="pays.links">
                    <paginate-panel :link="pays.links"></paginate-panel>
                </div>
                <div class="allUserIndexInfoPay">
                    <table>
                        <tr>
                            <th>#</th>
                            <th>{{$t('postStatus')}}</th>
                            <th>{{$t('orderNumber')}}</th>
                            <th>{{$t('paymentStatus')}}</th>
                            <th>{{$t('orderDate')}}</th>
                            <th>{{$t('theOperation')}}</th>
                        </tr>
                        <div class="showTr">
                            <tr v-for="(item , index) in pays.data">
                                <td>
                                    <span v-if="$i18n.locale == 'fa'">{{++index}}</span>
                                    <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{++index}}</span>
                                </td>
                                <td>
                                    <span class="unActive" v-if="item.deliver == 0">{{$t('takingOrders')}}</span>
                                    <span class="unActive" v-if="item.deliver == 1">{{ $t('awaitingReview') }}</span>
                                    <span class="unActive" v-if="item.deliver == 2">{{$t('packaged')}}</span>
                                    <span class="unActive" v-if="item.deliver == 3">{{$t('courierDelivery')}}</span>
                                    <span class="active" v-if="item.deliver == 4">{{$t('completed')}}</span>
                                </td>
                                <td>
                                    <span v-if="$i18n.locale == 'fa'">{{item.property}}</span>
                                    <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{item.property}}</span>
                                </td>
                                <td>
                                    <span class="active" v-if="item.status == 100">{{$t('paid')}}</span>
                                    <span class="active" v-else-if="item.status == 50">{{$t('buyDeposit')}}</span>
                                    <span class="unActive" v-else>{{$t('unpaid')}}</span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                                <td>
                                    <inertia-link :href="'/show-pay/'+item.property">
                                        <svg-icon :icon="'#left'"></svg-icon>
                                    </inertia-link>
                                </td>
                            </tr>
                        </div>
                    </table>
                </div>
                <div class="paginate" v-if="pays.links">
                    <paginate-panel :link="pays.links"></paginate-panel>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../../Admin/PaginatePanel";
import UserList from "./UserList";
export default {
    name: "PayUser",
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    props: ['pays','title'],
    data() {
        return {
            show : 0,
        }
    },
    metaInfo() {
        return {
            title: `پرداختی ها - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                reptilian: 'gator',
                amp: true
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    methods:{
        btnPay(index){
            const url = `/profile/pay`;
            this.show = index;
            this.$inertia.post(url , {
                show : this.show,
            })
                .then(response=>{
                })
        },
    }
}
</script>

<style scoped>

</style>
