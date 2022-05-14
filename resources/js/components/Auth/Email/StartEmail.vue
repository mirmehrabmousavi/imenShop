<template>
    <div class="allHeaderIndexRegisterItemsContainers">
        <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 0">
            <div class="allHeaderIndexRegisterItem">
                <span>{{ $t('Email') }}</span>
                <label for="emails" class="allHeaderIndexInput">
                    <input type="email" ref="item" id="emails" :placeholder="$t('Email')" v-model="email"/>
                </label>
            </div>
            <div class="alert" v-if="errors['email']">
                {{errors['email'][0]}}
            </div>
            <div class="allHeaderIndexRegisterItemsContainerButton">
                <div>
                    <button @click="btnSendCode(1)" :style="styleLoader">
                        <span>{{ $t('accept') }}</span>
                    </button>
                </div>
            </div>
            <p class="allHeaderIndexRegisterItemsContainerSecurity">{{$t('choiceWay')}}</p>
        </div>
        <div class="allHeaderIndexRegisterLoader" v-if="level == 4">
            <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
        </div>
    </div>
</template>

<script>
export default {
    name: "StartEmail",
    data(){
        return{
            errors:[],
            show: 0,
            level: 0,
            email: '',
            styleLoader : {
                transition: 'all .3s ease-in-out',
                width: '10rem'
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
                    this.$emit('sendData' , [this.show,this.level,this.email]);
                })
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
