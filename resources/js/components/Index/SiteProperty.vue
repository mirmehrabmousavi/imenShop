<template>
    <div class="siteProperties width">
        <div class="sitePropertiesItems">
            <div class="sitePropertiesItem">
                <div class="sitePropertiesItemPic">
                    <svg-icon :icon="'#7day'"></svg-icon>
                </div>
                <span>{{$t('sevenDays')}}</span>
            </div>
            <div class="sitePropertiesItem">
                <div class="sitePropertiesItemPic">
                    <svg-icon :icon="'#homePay'"></svg-icon>
                </div>
                <span>{{$t('PaymentSpot')}}</span>
            </div>
            <div class="sitePropertiesItem">
                <div class="sitePropertiesItemPic">
                    <svg-icon :icon="'#fastGive'"></svg-icon>
                </div>
                <span>{{$t('fastestSend')}}</span>
            </div>
            <div class="sitePropertiesItem">
                <div class="sitePropertiesItemPic">
                    <svg-icon :icon="'#securePay'"></svg-icon>
                </div>
                <span>{{$t('commodityGuarantee')}}</span>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "SiteProperty",
    components: {SvgIcon}
}
</script>

<style scoped>

</style>
