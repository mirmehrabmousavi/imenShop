<template>
    <div class="allHeaderIndexRegisterItemsContainers">
        <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 6">
            <div class="allHeaderIndexRegisterItem">
                <span>{{ $t('password') }}</span>
                <label for="checkPhone" class="allHeaderIndexInput">
                    <input type="password" ref="item" id="checkPhone" :placeholder="$t('password')" v-model="password"/>
                </label>
            </div>
            <div class="allHeaderIndexRegisterItemsContainerButton">
                <div>
                    <button @click="btnChangePassword" :style="styleLoader"><span>{{ $t('accept') }}</span></button>
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
    name: "ChangePassword",
    props:['number','shows','levels'],
    data(){
        return{
            password: '',
            show: this.shows,
            level: this.levels,
            errors: [],
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
            styleLoader : {
                transition: 'all .3s ease-in-out',
                width: '10rem'
            },
        }
    },
    methods:{
        btnChangePassword(){
            this.level = 4;
            const url  = '/change-password';
            axios.post(url,{
                phone : this.number,
                password : this.password,
            })
                .then(response=>{
                    if(response.data[0] == 'success'){
                        this.$toast.success('انجام شد', 'تغییر رمز با موفقیت انجام شد', this.notificationSystem.options.success);
                        this.$emit('sendEnd' , response.data[1]);
                    }else{
                        this.level = 6;
                        this.$toast.error('انجام نشد', 'رمز اشتباه است', this.notificationSystem.options.error);
                    }
                })
                .catch((error)=>{
                    this.level = 6;
                    this.errors = error.response.data.errors;
                    this.$toast.error('انجام نشد', 'رمز را وارد کنید', this.notificationSystem.options.error);
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
