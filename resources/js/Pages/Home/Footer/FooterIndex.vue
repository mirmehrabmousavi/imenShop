<template>
    <div class="allFooterIndex">
        <div class="blockFooter width">
            <div class="topFooterIndex">
                <div class="topFooterIndexItem" :title="$t('expressDelivery')">
                    <div class="topFooterIndexItemPic">
                        <svg-icon :icon="'#fastGive'"></svg-icon>
                    </div>
                    <span>{{$t('expressDelivery')}}</span>
                </div>
                <div class="topFooterIndexItem" :title="$t('PaymentSpot')">
                    <div class="topFooterIndexItemPic">
                        <svg-icon :icon="'#homePay'"></svg-icon>
                    </div>
                    <span>{{$t('PaymentSpot')}}</span>
                </div>
                <div class="topFooterIndexItem" :title="$t('sevenDays')">
                    <div class="topFooterIndexItemPic">
                        <svg-icon :icon="'#7day'"></svg-icon>
                    </div>
                    <span>{{$t('sevenDays')}}</span>
                </div>
                <div class="topFooterIndexItem" :title="$t('commodityGuarantee')">
                    <div class="topFooterIndexItemPic">
                        <svg-icon :icon="'#securePay'"></svg-icon>
                    </div>
                    <span>{{$t('commodityGuarantee')}}</span>
                </div>
            </div>
            <div class="botFooterIndex">
                <div class="botFooterIndexItem">
                    <div class="footerPic">
                        <img :src="$page.logo" :alt="$page.logo">
                    </div>
                    <p v-html="$page.aboutFooter" v-if="$i18n.locale == 'fa'"></p>
                    <p v-html="$page.aboutFooterEn" v-if="$i18n.locale == 'en'" class="en"></p>
                </div>
                <div class="botFooterIndexItem" v-if="$page.pageHome.length">
                    <span>{{$t('sheets')}}</span>
                    <ul>
                        <li v-for="item in $page.pageHome">
                            <inertia-link :href="'/page/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.title}}</inertia-link>
                            <inertia-link :href="'/page/' + item.slug" v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="botFooterIndexItem" v-if="$page.catFooter.length">
                    <span>{{$t('category')}}</span>
                    <ul>
                        <li v-for="item in $page.catFooter">
                            <inertia-link :href="'/archive/category/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.name}}</inertia-link>
                            <inertia-link :href="'/archive/category/' + item.slug" v-if="$i18n.locale == 'en'">{{item.nameEn}}</inertia-link>
                        </li>
                    </ul>
                </div>
                <div class="communicationFooter">
                    <span>{{$t('socialNetwork')}}</span>
                    <div class="communicationFooterItem">
                        <a :href="$page.telegramSite" title="تلگرام">
                            <i>
                                <svg-icon :icon="'#telegram'"></svg-icon>
                            </i>
                        </a>
                        <a :href="$page.facebookSite" title="فیسبوک">
                            <i>
                                <svg-icon :icon="'#facebook'"></svg-icon>
                            </i>
                        </a>
                        <a :href="$page.instagramSite" title="اینستاگرام">
                            <i>
                                <svg-icon :icon="'#instagram'"></svg-icon>
                            </i>
                        </a>
                        <a :href="$page.twitterSite" title="توییتر">
                            <i>
                                <svg-icon :icon="'#twitter'"></svg-icon>
                            </i>
                        </a>
                    </div>
                    <div class="trustFooter">
                        <a :href="$page.etemadAddress">
                            <img src="/img/etemad.png" alt="اعتماد">
                        </a>
                        <a :href="$page.fanavariAddress">
                            <img src="/img/samandehi-logo.png" alt="اعتماد">
                        </a>
                    </div>
                </div>
            </div>
            <div class="footerContact">
                <div class="footerContactItem">
                    <span>{{$t('support')}}</span>
                </div>
                <div class="footerContactItem" v-if="$page.numberSite != null || $page.numberSite != ''">
                    <span>{{$t('phoneNumber')}} : </span>
                    <span>{{$page.numberSite}}</span>
                </div>
                <div class="footerContactItem" v-if="$page.emailAddress != null || $page.emailAddress != ''">
                    <span>{{$t('emailAddress')}} : </span>
                    <span>{{$page.emailAddress}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";

export default {
    name: "FooterIndex",
    components:{
        SvgIcon,
    },
}
</script>

<style scoped>

</style>
