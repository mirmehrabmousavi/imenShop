<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="4"></user-list>
            </div>
            <div class="allShowPay">
                <div class="topShowPay">
                    <div class="title">
                        <h1>{{ $t('orderDetails') }}</h1>
                        <span>{{pay.created_at}}</span>
                        <span v-if="pay.pay_meta[0].post.type != 1 && pay.status == 100">
                            <a :href="'/pay/invoice/' + pay.property">دریافت فاکتور</a>
                        </span>
                        <button v-if="pay.back == 2">{{$t('cash1')}}</button>
                        <button v-if="pay.back == 1">{{$t('cash2')}}</button>
                    </div>
                    <div class="detail">
                        <div class="topDetail">
                            <div class="items" v-if="pay.address.length">
                                <div class="item">
                                    <h5>{{$t('transferee')}}</h5>
                                    <div>{{pay.address[0].name}}</div>
                                </div>
                                <div class="item">
                                    <h5>{{ $t('phoneNumber') }} :</h5>
                                    <div>{{pay.address[0].number}}</div>
                                </div>
                            </div>
                            <div class="items">
                                <div class="item" v-if="pay.address.length">
                                    <h5>{{ $t('residenceAddress') }}</h5>
                                    <div>{{pay.address[0].address}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="botDetail">
                            <div class="items">
                                <div class="item">
                                    <h5>{{ $t('amountPayable') }} :</h5>
                                    <div :class="$i18n.locale">{{pay.price|NumFormat}} {{$t('arz')}}</div>
                                </div>
                                <div class="item" v-if="pay.method == 2">
                                    <h5>{{ $t('depositAmount') }} :</h5>
                                    <div :class="$i18n.locale">{{pay.deposit|NumFormat}} {{$t('arz')}}</div>
                                </div>
                                <div class="item">
                                    <h5>{{$t('paymentStatus')}} :</h5>
                                    <div class="active" v-if="pay.status == 100">{{$t('paid')}}</div>
                                    <div class="active" v-else-if="pay.status == 50">{{$t('buyDeposit')}}</div>
                                    <div class="unActive" v-else>{{$t('unpaid')}}</div>
                                </div>
                                <div class="item">
                                    <h5>{{$t('payMethod')}} :</h5>
                                    <div v-if="pay.method == 0">{{$t('buyGate')}}</div>
                                    <div v-if="pay.method == 1">{{$t('buyWallet')}}</div>
                                    <div v-if="pay.method == 2">{{$t('buyHome')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="trackPay">
                        <label>{{$t('payTrack')}} :</label>
                        <h4 v-if="pay.track">{{pay.track}}</h4>
                        <h4 v-else>{{$t('unknown')}}</h4>
                    </div>
                </div>
                <div class="allShowPayContainer">
                    <div class="topContainer">
                        <div class="level">
                            <h3>{{$t('shippingStage')}} :</h3>
                            <span class="unActive" v-if="pay.deliver == 0">{{$t('takingOrders')}}</span>
                            <span class="unActive" v-if="pay.deliver == 1">{{ $t('awaitingReview') }}</span>
                            <span class="unActive" v-if="pay.deliver == 2">{{$t('packaged')}}</span>
                            <span class="unActive" v-if="pay.deliver == 3">{{$t('courierDelivery')}}</span>
                            <span class="active" v-if="pay.deliver == 4">{{$t('completed')}}</span>
                        </div>
                        <div class="rateItemsCount">
                            <div class="rateItemsCountItem" :title="$t('takingOrders')">
                                <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 1">
                                </div>
                                <div class="rateItemsCountItemBar" v-if="pay.deliver == 0">
                                </div>
                                <div class="rateItemsCountItemCircleActives" v-if="pay.deliver >= 1">
                                    <svg-icon :icon="'#delivery-complete'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircleActive" v-if="pay.deliver == 0">
                                    <svg-icon :icon="'#delivery-complete'"></svg-icon>
                                </div>
                            </div>
                            <div class="rateItemsCountItem" :title="$t('awaitingReview')">
                                <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 2">
                                </div>
                                <div class="rateItemsCountItemBar" v-if="pay.deliver <= 1">
                                </div>
                                <div class="rateItemsCountItemCircleActives" v-if="pay.deliver >= 2">
                                    <svg-icon :icon="'#waiting-room'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircleActive" v-if="pay.deliver == 1">
                                    <svg-icon :icon="'#waiting-room'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircle" v-if="pay.deliver <= 0">
                                    <svg-icon :icon="'#waiting-room'"></svg-icon>
                                </div>
                            </div>
                            <div class="rateItemsCountItem" :title="$t('packaged')">
                                <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 3">
                                </div>
                                <div class="rateItemsCountItemBar" v-if="pay.deliver <= 2">
                                </div>
                                <div class="rateItemsCountItemCircleActives" v-if="pay.deliver >= 3">
                                    <svg-icon :icon="'#package-delivery'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircleActive" v-if="pay.deliver == 2">
                                    <svg-icon :icon="'#package-delivery'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircle" v-if="pay.deliver <= 1">
                                    <svg-icon :icon="'#package-delivery'"></svg-icon>
                                </div>
                            </div>
                            <div class="rateItemsCountItem" :title="$t('courierDelivery')">
                                <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 4">
                                </div>
                                <div class="rateItemsCountItemBar" v-if="pay.deliver <= 3">
                                </div>
                                <div class="rateItemsCountItemCircleActives" v-if="pay.deliver >= 4">
                                    <svg-icon :icon="'#delivery-truck'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircleActive" v-if="pay.deliver == 3">
                                    <svg-icon :icon="'#delivery-truck'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircle" v-if="pay.deliver <= 2">
                                    <svg-icon :icon="'#delivery-truck'"></svg-icon>
                                </div>
                            </div>
                            <div class="rateItemsCountItem" :title="$t('completed')">
                                <div class="rateItemsCountItemCircleActive" v-if="pay.deliver == 4">
                                    <svg-icon :icon="'#delivery-box'"></svg-icon>
                                </div>
                                <div class="rateItemsCountItemCircle" v-if="pay.deliver <= 3">
                                    <svg-icon :icon="'#delivery-box'"></svg-icon>
                                </div>
                            </div>
                        </div>
                        <div class="code">
                            <h3>{{$t('orderNumber')}} :</h3>
                            <span>{{pay.property}}</span>
                        </div>
                    </div>
                    <div class="items">
                        <div class="title">{{ $t('productsOrdered') }}</div>
                        <div class="item" v-for="item in pay.pay_meta">
                            <inertia-link :href="'/product/'+item.post.slug" class="cartDetailPic">
                                <img :src="JSON.parse(item.post.image)[0]" :alt="item.post.title">
                            </inertia-link>
                            <div class="cartDetailInfo">
                                <inertia-link :href="'/product/'+item.post.slug" class="cartDetailInfoItem">
                                    <h3 v-if="$i18n.locale == 'fa'">{{item.post.title}}</h3>
                                    <h3 v-if="$i18n.locale == 'en'" class="en">{{item.post.titleEn}}</h3>
                                </inertia-link>
                                <div class="cartDetailInfoItem" v-if="JSON.parse(item.color) != ''">
                                    <i>
                                        <svg-icon :icon="'#color'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{JSON.parse(item.color).name}}</span>
                                    <span v-if="$i18n.locale == 'en'" class="en">{{JSON.parse(item.color).nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem" v-if="JSON.parse(item.size) != ''">
                                    <i>
                                        <svg-icon :icon="'#sizeFront'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{JSON.parse(item.size).name}}</span>
                                    <span v-if="$i18n.locale == 'en'" class="en">{{JSON.parse(item.size).nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem" v-if="item.guarantee">
                                    <i>
                                        <svg-icon :icon="'#security'"></svg-icon>
                                    </i>
                                    <span v-if="$i18n.locale == 'fa'">{{item.guarantee.name}}</span>
                                    <span v-if="$i18n.locale == 'en'" class="en">{{item.guarantee.nameEn}}</span>
                                </div>
                                <div class="cartDetailInfoItem">
                                    <i>
                                        <svg-icon :icon="'#post'"></svg-icon>
                                    </i>
                                    <span>{{item.count}}</span>
                                </div>
                                <div class="cartDetailInfoItem">
                                    <i>
                                        <svg-icon :icon="'#cost'"></svg-icon>
                                    </i>
                                    <span>{{item.price|NumFormat}} تومان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import UserList from "./UserList";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "ShowPay",
    components: {UserList,HomeLayout,SvgIcon},
    props: ['pay']
}
</script>

<style scoped>

</style>
