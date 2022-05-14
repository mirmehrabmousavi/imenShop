<template>
    <div class="allHeaderIndexRegisterItemsContainers">
        <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 2">
            <div class="allHeaderIndexRegisterItem">
                <span>{{$t('password')}}</span>
                <label for="checkPhone" class="allHeaderIndexInput">
                    <input type="password" id="checkPhone" ref="item" v-model="password" :placeholder="$t('password')"/>
                </label>
            </div>
            <div class="allHeaderIndexRegisterItemsContainerDetail" @click="btnSendCode(2)">
                {{$t('forgotPass')}}
            </div>
            <div class="allHeaderIndexRegisterItemsContainerButton">
                <div>
                    <button @click="btnLogin" :style="styleLoader"><span>{{$t('accept')}}</span></button>
                </div>
            </div>
        </div>
        <div class="allHeaderIndexRegisterLoader" v-if="level == 4">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginEmail",
    props:['shows','levels','email'],
    data(){
        return{
            styleLoader : {
                transition: 'all .3s ease-in-out',
                width: '10rem'
            },
            level: this.levels,
            show: this.shows,
            password:'',
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
        btnSendCode(show){
            this.level = 4;
            const url  = '/check-email';
            axios.post(url,{
                email : this.email,
                show : show
            })
                .then(response=>{
                    this.show=show;
                    this.level = response.data;
                    this.$emit('sendData' , [this.show,this.level]);
                })
        },
        btnLogin(){
            this.level = 4;
            const url  = '/login-email';
            axios.post(url,{
                email : this.email,
                password : this.password,
            })
                .then(response=>{
                    if(response.data[0] == 'success'){
                        window.location.href= '/admin';
                        this.$toast.success('انجام شد', 'عضویت با موفقیت انجام شد', this.notificationSystem.options.success);
                    }else{
                        this.level = 2;
                        this.$toast.error('انجام نشد', 'رمز اشتباه است', this.notificationSystem.options.error);
                    }
                })
                .catch((error)=>{
                    this.level = 2;
                    this.errors = error.response.data.errors;
                });
        },
    },
    mounted(){
        this.$refs.item.focus();
        window.scrollTo(0,0);
    }
}
</script>

<style scoped>

</style>
