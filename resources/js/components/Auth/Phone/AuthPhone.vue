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
                        <start-phone v-on:sendData="getData"></start-phone>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 3">
                        <send-code v-on:sendData="getData" :shows="show" :number="number" :levels="level"></send-code>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 5">
                        <make-user :shows="show" :number="number" :levels="level" v-on:sendEnd="getEnd"></make-user>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 6">
                        <change-password :shows="show" :number="number" :levels="level" v-on:sendEnd="getEnd"></change-password>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 2">
                        <login-user :shows="show" :number="number" :levels="level" v-on:sendData="getData" v-on:sendEnd="getEnd"></login-user>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from '../../../Pages/Svg/SvgIcon.vue'
import StartPhone from "./StartPhone";
import SendCode from "./SendCode";
import MakeUser from "./MakeUser";
import ChangePassword from "./ChangePassword";
import LoginUser from "./LoginUser";
export default {
    components: {LoginUser, ChangePassword, MakeUser, SendCode, StartPhone, SvgIcon },
    data(){
        return{
            number: '',
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
                this.number = data[2];
            }
        },
        btnShowLogin(){
            this.$emit('sendClose');
        },
    },
}
</script>

<style>

</style>
