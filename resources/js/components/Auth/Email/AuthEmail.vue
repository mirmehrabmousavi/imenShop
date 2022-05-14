<template>
    <div class="allHeaderIndexRegister">
        <div class="allHeaderIndexRegisterData">
            <i @click="btnShowLogin" class="allHeaderIndexRegisterIcon">
                <svg-icon :icon="'#cancel'"></svg-icon>
            </i>
            <div class="allHeaderIndexRegisterItems">
                <div class="allHeaderIndexRegisterItemsTitle">
                    <div class="allHeaderIndexRegisterTitleItem">{{ $t('login') }}</div>
                </div>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 0">
                        <start-email v-on:sendData="getData"></start-email>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 3">
                        <send-email-code v-on:sendData="getData" :shows="show" :email="email" :levels="level"></send-email-code>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 5">
                        <make-user-email :shows="show" :email="email" :levels="level" v-on:sendEnd="getEnd"></make-user-email>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 6">
                        <change-email-password :shows="show" :email="email" :levels="level" v-on:sendEnd="getEnd"></change-email-password>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 2">
                        <login-email :shows="show" :email="email" :levels="level" v-on:sendData="getData" v-on:sendEnd="getEnd"></login-email>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from '../../../Pages/Svg/SvgIcon.vue'
import StartEmail from "./StartEmail";
import LoginEmail from "./LoginEmail";
import ChangeEmailPassword from "./ChangeEmailPassword";
import MakeUserEmail from "./MakeUserEmail";
import SendEmailCode from "./SendEmailCode";

export default {
    name: "AuthEmail",
    components: {StartEmail, SvgIcon , LoginEmail , ChangeEmailPassword , MakeUserEmail , SendEmailCode },
    data(){
        return{
            email: '',
            level: 0,
            show: 0,
        }
    },
    methods: {
        getEnd(data){
            this.$emit('sendUser' , data);
            this.$emit('sendClose');
        },
        getData(data){
            this.show = data[0];
            this.level = data[1];
            if(data[2]){
                this.email = data[2];
            }
        },
        btnShowLogin(){
            this.$emit('sendClose');
        },
    },
}
</script>

<style scoped>

</style>
