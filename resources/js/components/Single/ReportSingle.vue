<template>
    <div class="allReportSingle">
        <div class="allReportSingleShow">
            <div class="allReportSinglePic">
                <svg-icon :icon="'#report2'"></svg-icon>
            </div>
            <div class="allReportSingleTitle">
                <i>
                    <svg-icon :icon="'#report'"></svg-icon>
                </i>
                <span>بازخورد در مورد این کالا</span>
            </div>
            <div class="allReportSingleItems">
                <div class="allReportSingleItem">
                    <label :for="1">
                        <input :id="1" type="checkbox" @change="sendData('نام کالا صحیح نیست')">
                        نام کالا صحیح نیست
                    </label>
                </div>
                <div class="allReportSingleItem">
                    <label :for="2">
                        <input :id="2" type="checkbox" @change="sendData('عکس‌های کالا مناسب نیست')">
                        عکس‌های کالا مناسب نیست
                    </label>
                </div>
                <div class="allReportSingleItem">
                    <label :for="3">
                        <input :id="3" type="checkbox" @change="sendData('مشخصات فنی کالا صحیح نیست')">
                        مشخصات فنی کالا صحیح نیست
                    </label>
                </div>
                <div class="allReportSingleItem">
                    <label :for="4">
                        <input :id="4" type="checkbox" @change="sendData('توضیحات کالا صحیح نیست')">
                        توضیحات کالا صحیح نیست
                    </label>
                </div>
                <div class="allReportSingleItem">
                    <label :for="5">
                        <input :id="5" type="checkbox" @change="sendData('این کالا غیراصل است')">
                        این کالا غیراصل است
                    </label>
                </div>
                <div class="allReportSingleItem">
                    <label :for="6">
                        <input :id="6" type="checkbox" @change="sendData('کالا تکراری است')">
                        کالا تکراری است
                    </label>
                </div>
            </div>
            <textarea v-if="form.data.length" v-model="form.body" placeholder="توضیحات را وارد کنید ... "></textarea>
            <div class="allReportSingleButtons">
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
    name: "ReportSingle",
    components: {SvgIcon},
    props:['post'],
    data(){
        return{
            form:{
                data: [],
                datas: [],
                type: 1,
                body: '',
                post_id: this.post
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
                        this.$toast.success('انجام شد', 'بازخورد شما با موفقیت ثبت شد', this.notificationSystem.options.success);
                        this.$emit('sendClose' , 1);
                    }
                })
                .catch(err =>{
                    this.loader = false;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        sendData(data){
            for ( this.i ; this.i <  6; this.i++) {
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
