<template>
    <home-layout>
        <div class="allRegisters">
            <div class="allHeaderIndexRegister" v-if="level == 0">
                <div class="allHeaderIndexRegisterData">
                    <div class="allHeaderIndexRegisterItems">
                        <div class="allHeaderIndexRegisterItemsTitle">
                            <div class="allHeaderIndexRegisterTitleItem">{{$t('login')}}</div>
                            <svg-icon :icon="'#login'"></svg-icon>
                        </div>
                        <div class="registerWaysBlock">
                            <div class="registerWays">
                                <button @click="level = 2">
                                    <svg-icon :icon="'#email2'"/>
                                    <span>
                                        {{$t('loginVia')}}
                                        <span>{{$t('emailAddress')}}</span>
                                    </span>
                                    <svg-icon :icon="'#left'"/>
                                </button>
                                <button @click="level = 1">
                                    <svg-icon :icon="'#sms2'"/>
                                    <span>
                                        {{$t('loginVia')}}
                                        <span>{{$t('sms')}}</span>
                                    </span>
                                    <svg-icon :icon="'#left'"/>
                                </button>
                            </div>
                            <div class="registerWays">
                                <a target="blank" href="/google-login">
                                    <svg-icon :icon="'#google'"/>
                                    <span>
                                        {{$t('loginVia')}}
                                        <span>{{$t('gmail')}}</span>
                                    </span>
                                    <svg-icon :icon="'#left'"/>
                                </a>
                            </div>
                            <div class="registerWayBody">
                                <p>{{$t('choiceWay')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <transition name="slide-fade">
                <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 1">
                    <auth-phone v-on:sendUser="getUser"></auth-phone>
                </div>
            </transition>
            <transition name="slide-fade">
                <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 2">
                    <auth-email v-on:sendUser="getUser"></auth-email>
                </div>
            </transition>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import AuthPhone from "../../../components/Auth/Phone/AuthPhone";
import AuthEmail from "../../../components/Auth/Email/AuthEmail";
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "AuthIndex",
    components: {HomeLayout, AuthEmail, AuthPhone, SvgIcon},
    data(){
        return{
            level: 0
        }
    },
    methods:{
        getUser(){
            window.location.reload();
        },
    }
}
</script>

<style scoped>

</style>
