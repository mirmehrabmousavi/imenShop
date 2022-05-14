<template>
    <div class="allHeaderIndexRegisterItemsContainers">
        <div class="allHeaderIndexRegisterItemsContainer" v-if="level == 0">
            <div class="allHeaderIndexRegisterItem">
                <span>{{ $t('phoneNumber') }}</span>
                <label for="checkPhone" class="allHeaderIndexInput">
                    <input type="text" @keyup="changeNum" :placeholder="$t('phoneNumber')" v-model="number" ref="item" id="checkPhone" maxlength="17" />
                </label>
            </div>
            <div class="alert" v-if="errors['number']">
                {{errors['number'][0]}}
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
    name: "StartAuth",
    data(){
        return{
            errors:[],
            show: 0,
            level: 0,
            number: '',
            styleLoader : {
                transition: 'all .3s ease-in-out',
                width: '10rem'
            },
        }
    },
    methods:{
        changeNum(){
            const isNumericInput = (event) => {
                const key = event.keyCode;
                return ((key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105)
                );
            };

            const isModifierKey = (event) => {
                const key = event.keyCode;
                return (event.shiftKey === true || key === 35 || key === 36) ||
                    (key === 8 || key === 9 || key === 13 || key === 46) ||
                    (key > 36 && key < 41) ||
                    (
                        (event.ctrlKey === true || event.metaKey === true) &&
                        (key === 65 || key === 67 || key === 86 || key === 88 || key === 90)
                    )
            };

            const enforceFormat = (event) => {
                if(!isNumericInput(event) && !isModifierKey(event)){
                    event.preventDefault();
                }
            };

            const formatToPhone = (event) => {
                if(isModifierKey(event)) {return;}

                const input = event.target.value.replace(/\D/g,'').substring(0,17);
                const areaCode = input.substring(0,4);
                const middle = input.substring(4,7);
                const last = input.substring(7,17);

                if(input.length > 7){event.target.value = `${areaCode} - ${middle} - ${last}`;}
                else if(input.length > 4){event.target.value = `${areaCode} - ${middle}`;}
                else if(input.length > 0){event.target.value = `${areaCode}`;}
            };

            const inputElement = document.getElementById('checkPhone');
            inputElement.addEventListener('keydown',enforceFormat);
            inputElement.addEventListener('keyup',formatToPhone);
        },
        btnSendCode(show){
            this.level = 4;
            const url  = '/check-auth';
            axios.post(url,{
                number : this.number,
                show : show
            })
                .then(response=>{
                    this.show=show;
                    this.level = response.data;
                    this.$emit('sendData' , [this.show,this.level,this.number]);
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
