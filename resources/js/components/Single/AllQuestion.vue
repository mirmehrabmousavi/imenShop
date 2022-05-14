<template>
    <div class="AllQuestions">
        <div class="AllQuestion">
            <div class="AllQuestionTitle">
                <h3>{{$t('askQuestion')}}</h3>
                <i @click="btnClose">
                    <svg-icon :icon="'#cancel'"></svg-icon>
                </i>
            </div>
            <textarea :placeholder="$t('publishedQuestion')" v-model="body"></textarea>
            <p>{{$t('publishedQuestion')}}</p>
            <div class="buttons">
                <button @click="sendQuestion">
                    <svg-icon class="loading" v-if="loader" :icon="'#loading'"></svg-icon>
                    <svg-icon v-else :icon="'#question'"></svg-icon>
                    {{$t('send')}}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "AllQuestion",
    components: {SvgIcon},
    props:['parentId','post'],
    data(){
        return{
            body: '',
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
        sendQuestion(){
            this.loader = true;
            const url = '/send-question'
            axios.post(url, {
                post: this.post,
                body: this.body,
                parentId: this.parentId,
            })
                .then(response=>{
                    this.loader = false;
                    if(response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$toast.success('انجام شد', 'با موفقیت ثبت شد', this.notificationSystem.options.success);
                        this.$emit('sendClose');
                    }
                })
                .catch(err =>{
                    this.loader = false;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    }
}
</script>

<style scoped>

</style>
