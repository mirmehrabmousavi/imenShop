<template>
    <div class="allSendTicket">
        <div class="sendTicket">
            <div class="sendTicketTitle">
                {{$t('shareTicket')}}
            </div>
            <div class="sendTicketPic">
                <svg-icon :icon="'#ticketSend'"></svg-icon>
            </div>
            <textarea v-model="ticket" :placeholder="$t('request')"></textarea>
            <div class="sendTicketButtons">
                <button v-if="loading">
                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                </button>
                <button v-else @click="sendTicket">{{$t('send')}}</button>
                <button @click="btnClose">{{$t('cancel')}}</button>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "SendTicket",
    components: {SvgIcon},
    data(){
        return{
            showTicket: false,
            ticket: '',
            loading: false,
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
        sendTicket(){
            if(this.ticket == ''){
                this.$toast.error('انجام نشد', 'فیلد درخواست اجباری است', this.notificationSystem.options.error);
            }else{
                this.loading = true;
                const url = '/ticket';
                axios.post(url,{
                    ticket: this.ticket
                })
                    .then(response =>{
                        if (response.data == 'noUser'){
                            this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        }else{
                            this.$toast.success('انجام شد', 'عملیات با موفقیت انجام شد', this.notificationSystem.options.success);
                            this.ticket = '';
                            this.$emit('sendClose');
                        }
                        this.loading = false;
                    })
                .catch(err=>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                    this.loading = false;
                })
            }
        },
    }
}
</script>

<style scoped>

</style>
