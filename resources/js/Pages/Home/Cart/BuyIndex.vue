<template>
    <home-layout>
        <div class="buyIndex">
            <div class="allBuyItems">
                <div class="allBuySuccessItemTitle" v-if="pay.status == 100 || pay.status == 50">
                    <h3>{{ $t('purchaseOperations') }}</h3>
                </div>
                <div class="allBuyFailItemTitle" v-else>
                    <h3>{{ $t('purchaseOperations') }}</h3>
                </div>
                <div class="allBuyItem">
                    <label>{{ $t('dateRegistration') }}</label>
                    <h4>{{pay.created_at}}</h4>
                </div>
                <div class="allBuyItem">
                    <label>{{ $t('customerName') }}</label>
                    <h4>{{name}}</h4>
                </div>
                <div class="allBuyItem">
                    <label>{{ $t('amount') }}</label>
                    <h4 :class="$i18n.locale" v-if="pay.method == 2">{{pay.deposit|NumFormat}} {{$t('arz')}}</h4>
                    <h4 :class="$i18n.locale" v-else>{{pay.price|NumFormat}} {{$t('arz')}}</h4>
                </div>
                <div class="allBuyItem">
                    <label>{{$t('orderNumber')}}</label>
                    <h4>{{pay.property}}</h4>
                </div>
                <div class="allBuyItem">
                    <label>{{$t('paymentStatus')}}</label>
                    <h4 v-if="pay.status == 100 || pay.status == 50">{{ $t('successful') }}</h4>
                    <h4 v-else>{{ $t('unsuccessful') }}</h4>
                </div>
                <div class="allBuyButton">
                    <inertia-link href="/" :title="'صفحه نخست'" :name="'صفحه نخست'">{{ $t('returnHome') }}</inertia-link>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "BuyIndex",
    components: {HomeLayout},
    props : ['name','pay'],

}
</script>

<style scoped>

</style>
