<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="10"></user-list>
            </div>
            <div class="allCheckoutIndex">
                <div class="allCheckoutTop">
                    <ul>
                        <li>{{$t('checkoutBody1')}}</li>
                        <li>
                            <h4 v-if="$i18n.locale == 'fa'">
                                شماره شبا شما در
                                <inertia-link href="/profile/personal-info">ویرایش حساب</inertia-link>
                                قابل مشاهده است
                            </h4>
                            <h4 v-if="$i18n.locale == 'en'">
                                Your shaba number in
                                <inertia-link href = "/profile/personal-info"> edit account </inertia-link>
                                Is visible
                            </h4>
                        </li>
                    </ul>
                </div>
                <div class="allCheckoutData">
                    <ul>
                        <li>
                            <h4>{{ $t('totalIncome') }} :</h4>
                            <span v-if="$i18n.locale == 'fa'">{{allPays|NumFormat}} تومان</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{allPays|NumFormat}} tooman</span>
                        </li>
                        <li>
                            <h4>{{ $t('calculatedSettlement') }} :</h4>
                            <span v-if="$i18n.locale == 'fa'">{{checkSum|NumFormat}} تومان</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{checkSum|NumFormat}} tooman</span>
                        </li>
                        <li>
                            <h4>{{ $t('inWallet') }} :</h4>
                            <span v-if="$i18n.locale == 'fa'">{{(allPays-checkSum)|NumFormat}} تومان</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{(allPays-checkSum)|NumFormat}} tooman</span>
                        </li>
                    </ul>
                    <button v-if="send && !exist" @click="btnCheckout">{{ $t('requestSettlement') }}</button>
                </div>
                <table>
                    <tr>
                        <div>
                            <th>#</th>
                            <th>{{ $t('applicationNumber') }}</th>
                            <th>{{ $t('shabaNumber') }}</th>
                            <th>{{ $t('price') }}</th>
                            <th>{{ $t('paymentStatus') }}</th>
                        </div>
                    </tr>
                    <tr v-for="(item, index) in check" :key="index">
                        <div>
                            <td>
                                <span>{{++index}}</span>
                            </td>
                            <td>
                                <span>{{item.id}}</span>
                            </td>
                            <td>
                                <span>{{item.shaba}}</span>
                            </td>
                            <td>
                                <span v-if="$i18n.locale == 'fa'">{{item.price|NumFormat}} تومان </span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{item.price|NumFormat}} tooman </span>
                            </td>
                            <td>
                                <span>
                                    <span v-if="item.status == 0">{{ $t('awaitingReview') }}</span>
                                    <span v-if="item.status == 1" class="unActive">{{ $t('failed') }}</span>
                                    <span v-if="item.status == 2" class="activeStatus">{{ $t('cleared') }}</span>
                                </span>
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
import UserList from "./UserList";
export default {
    name: "CheckoutIndex",
    components: {UserList, HomeLayout},
    props: ['check','allPays','checkSum','exist'],
    data(){
        return{
            send: 0
        }
    },
    methods:{
        checkData(){
            if(this.check.length){
                if((parseInt(this.allPays) - parseInt(this.checkSum)) >= 1000){
                    this.send = this.check[0].status;
                }
            }else{
                if((parseInt(this.allPays) - parseInt(this.checkSum)) >= 1000){
                    this.send = 1;
                }
            }
        },
        btnCheckout(){
            const url = `/profile/checkout`;
            this.$inertia.post(url,{
                send: 1
            })
                .then(response=>{
                    this.send = 0;
                })
        }
    },
    mounted(){
        this.checkData();
    }
}
</script>

<style scoped>

</style>
