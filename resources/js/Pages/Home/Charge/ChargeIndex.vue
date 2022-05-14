<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="10"></user-list>
            </div>
            <div class="allChargeIndex">
                <div class="chargeWidgets">
                    <div class="WidgetItem">
                        <div class="WidgetIcon">
                            <svg-icon :icon="'#successPay'"></svg-icon>
                        </div>
                        <div class="WidgetSubject">
                            <h4>{{ $t('increaseChargeSuccess') }}</h4>
                            <h5>{{chargesSuccess|NumFormat}} {{ $t('arz') }}</h5>
                        </div>
                    </div>
                    <div class="WidgetItem">
                        <div class="WidgetIcon">
                            <svg-icon :icon="'#failPay'"></svg-icon>
                        </div>
                        <div class="WidgetSubject">
                            <h4>{{ $t('increaseChargeFail') }}</h4>
                            <h5>{{chargesFail|NumFormat}} {{ $t('arz') }}</h5>
                        </div>
                    </div>
                    <div class="WidgetItem">
                        <div class="WidgetIcon">
                            <svg-icon :icon="'#successPay'"></svg-icon>
                        </div>
                        <div class="WidgetSubject">
                            <h4>{{ $t('sellSuccess') }}</h4>
                            <h5>{{chargesIncrease|NumFormat}} {{ $t('arz') }}</h5>
                        </div>
                    </div>
                    <div class="WidgetItem">
                        <div class="WidgetIcon">
                            <svg-icon :icon="'#failPay'"></svg-icon>
                        </div>
                        <div class="WidgetSubject">
                            <h4>{{ $t('sellFail') }}</h4>
                            <h5>{{chargesDecrease|NumFormat}} {{ $t('arz') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="chargePrice">
                    <div class="right">
                        <span>{{ $t('amount') }} ({{ $t('arz') }}):</span>
                        <input type="text" :placeholder="$t('amount')" v-model="price">
                        <a :href="'/charge/shop?amount=' + price">{{$t('increaseCharge')}}</a>
                    </div>
                    <div class="left">
                        <h4>{{ $t('minimumAmount') }}</h4>
                    </div>
                </div>
                <table>
                    <tr>
                        <div>
                            <th>#</th>
                            <th>{{$t('orderNumber')}}</th>
                            <th>{{$t('price')}}</th>
                            <th>{{$t('typeCharge')}}</th>
                            <th>{{$t('paymentStatus')}}</th>
                            <th>{{$t('orderDate')}}</th>
                        </div>
                    </tr>
                    <tr v-for="(item , index) in charges.data">
                        <div>
                            <td>
                                <span v-if="$i18n.locale == 'fa'">{{++index}}</span>
                                <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{++index}}</span>
                            </td>
                            <td>
                                <span v-if="$i18n.locale == 'fa'">{{item.property}}</span>
                                <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{item.property}}</span>
                            </td>
                            <td>
                                <span v-if="$i18n.locale == 'fa'">{{item.price|NumFormat}} {{$t('arz')}}</span>
                                <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{item.price|NumFormat}} {{$t('arz')}}</span>
                            </td>
                            <td>
                                <span>
                                    <span v-if="item.type == 0">{{$t('addCharge')}}</span>
                                    <span v-else>{{$t('pay')}}</span>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <span class="activeStatus" v-if="item.status == 100">{{$t('paid')}}</span>
                                    <span class="unActive" v-else>{{$t('unpaid')}}</span>
                                </span>
                            </td>
                            <td>
                                <span>{{item.created_at}}</span>
                            </td>
                        </div>
                    </tr>
                </table>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import UserList from "../User/UserList";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "ChargeIndex",
    components: {UserList,SvgIcon,HomeLayout},
    props: ['charges','chargesSuccess','chargesFail','chargesIncrease','chargesDecrease'],
    data(){
        return{
            price: ''
        }
    }
}
</script>

<style scoped>

</style>
