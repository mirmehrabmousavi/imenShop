<template>
    <div class="singleNotification">
        <div class="singleNotificationItems">
            <div class="singleNotificationTitle">
                <h3>اطلاع رسانی به من در زمان شگفت انگیز</h3>
            </div>
            <i>
                <svg-icon :icon="'#singleNotification'"></svg-icon>
            </i>
            <div class="singleNotificationData">
                <div class="singleNotificationItem">
                    <label :for="5">
                        <input :id="5" type="checkbox" @change="sendData('پیامک')">
                        اطلاع رسانی از طریق پیامک
                    </label>
                </div>
                <div class="singleNotificationItem">
                    <label :for="6">
                        <input :id="6" type="checkbox" @change="sendData('ایمیل')">
                        اطلاع رسانی از طریق ایمیل
                    </label>
                </div>
            </div>
            <div class="singleNotificationButtons">
                <button v-if="!loader && form.data.length" @click="sendReport">ارسال</button>
                <button disabled v-if="!loader && form.data.length == 0" >ارسال</button>
                <button v-if="loader">
                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                </button>
                <button @click="btnClose">انصراف</button>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "SingleNotification",
    components: {SvgIcon},
    props:['post'],
    data(){
        return{
            form:{
                data: [],
                datas: [],
                post_id: this.post,
                type: 2
            },
            i: 0,
            loader: false,
            notificationSystem: {
                options: {
                    show: {
                        icon: "icon-person",
                        position: "topCenter",
                    },
                    success: {
                        position: "bottomRight"
                    },
                    error: {
                        position: "bottomRight"
                    },
                }
            },
        }
    },
    methods:{
        btnClose(){
            this.$emit('sendClose');
        },
        sendReport(){
            this.loader = true;
            this.form.datas = JSON.stringify(this.form.data);
            const url = '/send-report'
            axios.post(url,this.form)
                .then(response=>{
                    this.loader = false;
                    if(response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$toast.success('انجام شد', 'با موفقیت ثبت شد', this.notificationSystem.options.success);
                        this.$emit('sendClose' , 1);
                    }
                })
                .catch(err =>{
                    this.loader = false;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        sendData(data){
            for ( this.i ; this.i <  2; this.i++) {
                if (this.form.data[this.i] == data){
                    this.form.data.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.form.data.push(data);
            }
            this.i = 0;
        }
    }
}
</script>

<style scoped>

</style>
