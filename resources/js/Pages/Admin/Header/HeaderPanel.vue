<template>
    <div class="allHeaderPanel">
        <div class="rightOption">
            <div class="logoHeaderPanel">
                <a href="/">
                    <img :src="$page.logo">
                </a>
            </div>
            <div class="iconHeaderPanel">
                <i>
                    <svg-icon :icon="'#align'"></svg-icon>
                </i>
            </div>
            <div class="searchHeaderPanel">
                <label>
                    <input type="search" placeholder="جستجو ...">
                    <i>
                        <svg-icon :icon="'#search'"></svg-icon>
                    </i>
                </label>
            </div>
        </div>
        <div class="leftOption">
            <div class="iconHeaderPanel">
                <i @click="toggle">
                    <svg-icon :icon="'#full-screen'"></svg-icon>
                </i>
            </div>
            <div class="alarmHeaderPanel">
                <i v-if="$page.ticketSeen != 0 || $page.paySeen != 0 || $page.userSeen != 0 || $page.commentSeen != 0" @click="showBell = !showBell" class="active">
                    <svg-icon :icon="'#bell'"></svg-icon>
                </i>
                <i v-else @click="showBell = !showBell" class="unActive">
                    <svg-icon :icon="'#bell'"></svg-icon>
                </i>
                <ul v-if="showBell">
                    <li>اطلاعات جدید</li>
                    <li>
                        <div class="alarmHeaderPanelIcon">
                            <svg-icon :icon="'#message'"></svg-icon>
                        </div>
                        <div class="alarmHeaderPanelInfo">
                            <inertia-link href="/admin/ticket">درخواست جدید</inertia-link>
                            <span>{{ $page.ticketSeen }} مورد </span>
                        </div>
                    </li>
                    <li>
                        <div class="alarmHeaderPanelIcon">
                            <svg-icon :icon="'#bill'"></svg-icon>
                        </div>
                        <div class="alarmHeaderPanelInfo">
                            <inertia-link href="/admin/pay">پرداختی جدید</inertia-link>
                            <span>{{ $page.paySeen }} مورد </span>
                        </div>
                    </li>
                    <li>
                        <div class="alarmHeaderPanelIcon">
                            <svg-icon :icon="'#customer'"></svg-icon>
                        </div>
                        <div class="alarmHeaderPanelInfo">
                            <inertia-link href="/admin/user">کاربر جدید</inertia-link>
                            <span>{{ $page.userSeen }} مورد </span>
                        </div>
                    </li>
                    <li>
                        <div class="alarmHeaderPanelIcon">
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </div>
                        <div class="alarmHeaderPanelInfo">
                            <inertia-link href="/admin/comment">دیدگاه جدید</inertia-link>
                            <span>{{ $page.commentSeen }} مورد </span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="userHeaderPanel">
                <span>{{ $page.user.name }}</span>
                <img v-if="$page.user.profile == null" src="/img/user.png">
                <img v-else :src="$page.user.profile">
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "HeaderPanel",
    components:{
        SvgIcon
    },
    data() {
        return {
            showSetting: false,
            showBell: false,
            fullscreen: false
        }
    },
    methods: {
        fullscreenChange (fullscreen) {
            this.fullscreen = fullscreen
        },
        toggle () {
            this.$eventHub.emit('fullS');
        },
    },
}
</script>

<style scoped>

</style>
