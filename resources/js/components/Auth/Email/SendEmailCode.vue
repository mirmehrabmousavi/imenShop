<template>
    <div class="allHeaderIndexRegisterItemsContainers">
        <div class="allHeaderIndexRegisterItemsContainer">
            <div class="allHeaderIndexRegisterItemsContainerCounter">
                <div class="allHeaderIndexRegisterItemsContainerCounterTimer" v-if="showTimer">
                    <counter-down v-on:sendEnd="getEnd"></counter-down>
                </div>
                <h3>{{ $t('activeCode') }}</h3>
                <p>{{ $t('activeSubject') }}</p>
            </div>
            <div class="allHeaderIndexRegisterItem">
                <div class="allHeaderIndexRegisterItemInputs">
                    <div class="code-container" @click="changeCode">
                        <label for="codeEnters">
                            <span v-for="i in [0,1,2,3,4,5]"  :class="{highlight: i == code.length}">{{code[i]}}</span>
                        </label>
                        <input ref="codes" autofocus id="codeEnters" type="tel" v-model="code" maxlength="6">
                    </div>
                </div>
            </div>
            <div class="allHeaderIndexRegisterItemsContainerButton">
                <div v-if="sendAgain">
                    <button @click="btnCheckCode" :style="styleLoader">
                        <span v-if="level != 4">{{ $t('accept') }}</span>
                        <svg-icon v-else class="loading" :icon="'#loading'"></svg-icon>
                    </button>
                </div>
                <div v-else>
                    <button @click="btnSendCode(show)" :style="styleLoader"><span>{{ $t('sendAgain') }}</span></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from '../../../Pages/Svg/SvgIcon.vue'
import CounterDown from "../CounterDown";
export default {
    name: "SendCode",
    components: {CounterDown,SvgIcon},
    props:['shows','levels','email'],
    data() {
        return{
            styleLoader : {
                transition: 'all .3s ease-in-out',
                width: '10rem'
            },
            errors: [],
            level: this.levels,
            show: this.shows,
            code: '',
            sendAgain: true,
            showTimer: true,
            notificationSystem: {
                options: {
                    show: {
                        icon: "icon-person",
                        position: "topCenter",
                    },
                    success: {
                        position: "bottomLeft"
                    },
                    error: {
                        theme: "#FCA001",
                        progressBarColor: "#DC0808",
                        position: "bottomRight"
                    },
                }
            },
        }
    },
    methods:{
        btnCheckCode(){
            this.styleLoader = {
                width : '40px',
                transition: 'all .3s ease-in-out',
            }
            this.level = 4;
            const url  = '/check-email-code';
            axios.post(url,{
                email : this.email,
                code : this.code,
                show : this.show,
            })
                .then(response=>{
                    if(response.data[0] == 'success'){
                        this.level = response.data[1];
                        this.$emit('sendData' , [this.show,this.level,this.email]);
                    }else{
                        this.level = 3;
                        this.styleLoader = {
                            width : '10rem',
                            transition: 'all .3s ease-in-out',
                        },
                        this.$toast.error('انجام نشد', 'کد تایید اشتباه است', this.notificationSystem.options.error);
                    }
                })
                .catch((error)=>{
                    this.level = 3;
                    this.styleLoader = {
                        width : '10rem',
                        transition: 'all .3s ease-in-out',
                    },
                    this.errors = error.response.data.errors;
                    this.$toast.error('انجام نشد', this.errors['code'][0], this.notificationSystem.options.error);
                });
        },
        btnSendCode(show){
            this.styleLoader = {
                width : '8rem',
                transition: 'all .3s ease-in-out',
            }
            this.level = 4;
            this.showTimer = false;
            const url  = '/check-email';
            axios.post(url,{
                email : this.email,
                show : show
            })
                .then(response=>{
                    this.sendAgain= true;
                    this.showTimer = true;
                    this.show= show;
                    this.level= response.data;
                    this.$emit('sendData' , [this.show,this.level,this.email]);
                })
        },
        changeCode(){
            document.getElementById("codeEnters").onclick = function() {
                var input = document.getElementById("codeEnters");
                input.focus();

                var val = input.value;
                input.value = '';
                input.value = val;
            }
        },
        getEnd(){
            this.sendAgain = false;
            this.styleLoader = {
                width : '12rem',
                transition: 'all .3s ease-in-out',
            }
        },
    },
    mounted() {
        this.$refs.codes.focus();
        window.scrollTo(0,0);
    }
}
</script>

<style scoped>

</style>
