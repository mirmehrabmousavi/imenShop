<template>
    <div class="allUserIndexLists">
        <div class="allUserIndexList">
            <div class="allUserIndexListsUser">
                <div class="allUserIndexListsUserPic">
                    <div class="pic">
                        <i>
                            <dropzone ref="myVueDropzone3" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true v-on:vdropzone-success="uploadAllFile">
                                <svg-icon :icon="'#pencil'"></svg-icon>
                            </dropzone>
                        </i>
                        <img v-if="profile == null" src="/img/user.png" :alt="$page.userData.name">
                        <img v-else :src="profile" :alt="$page.userData.name">
                    </div>
                </div>
                <div class="allUserIndexListsUserItem">
                    <div class="allUserIndexListsUserName">{{ $page.userData.name }}</div>
                    <div class="allUserIndexListsUserScore" v-if="$i18n.locale == 'fa'">
                        <i>
                            <svg-icon :icon="'#score'"></svg-icon>
                        </i>
                        <span v-if="$i18n.locale == 'fa'">{{ $page.myScore|NumFormat }} {{$t('score')}}</span>
                        <span v-if="$i18n.locale == 'en'" class="en">{{ $page.myScore|NumFormat }} {{$t('score')}}</span>
                    </div>
                </div>
            </div>
            <div class="allUserIndexListItems">
                <inertia-link href="/profile/personal-info">{{$t('account')}}</inertia-link>
                <inertia-link href="/logout">{{$t('logout')}}</inertia-link>
            </div>
        </div>
        <div class="walletData">
            <i>
                <svg-icon :icon="'#wallet'"></svg-icon>
            </i>
            <h3>{{ $page.wallet|NumFormat }} <span>{{ $t('arz') }}</span></h3>
            <inertia-link href="/charge-increase">افزایش حساب</inertia-link>
        </div>
        <div class="allUserIndexListsItems">
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#billFront'"></svg-icon>
                </i>
                <inertia-link href="/profile/pay">{{$t('myOrders')}}</inertia-link>
                <i v-if="tab == 4">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem" v-if="$page.checkSeller == null">
                <i>
                    <svg-icon :icon="'#seller'"></svg-icon>
                </i>
                <inertia-link href="/become-seller">{{$t('BecomeSeller')}}</inertia-link>
                <i v-if="tab == 9">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem" v-if="$page.checkSeller">
                <i>
                    <svg-icon :icon="'#payList'"></svg-icon>
                </i>
                <inertia-link href="/profile/product/pay">{{$t('listSell')}}</inertia-link>
                <i v-if="tab == 8">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem" v-if="$page.checkSeller">
                <i>
                    <svg-icon :icon="'#checkout'"></svg-icon>
                </i>
                <inertia-link href="/profile/checkout">{{$t('listCheckout')}}</inertia-link>
                <i v-if="tab == 10">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem" v-if="$page.checkSeller">
                <i>
                    <svg-icon :icon="'#post'"></svg-icon>
                </i>
                <inertia-link href="/profile/all-products">افزودن تنوع</inertia-link>
                <i v-if="tab == 11">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#unlike'"></svg-icon>
                </i>
                <inertia-link href="/profile/like">{{$t('favorites')}}</inertia-link>
                <i v-if="tab == 3">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#unbookmark'"></svg-icon>
                </i>
                <inertia-link href="/profile/bookmark">{{$t('mySigns')}}</inertia-link>
                <i v-if="tab == 1">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#comment'"></svg-icon>
                </i>
                <inertia-link href="/profile/comment">{{$t('myComments')}}</inertia-link>
                <i v-if="tab == 2">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#message'"></svg-icon>
                </i>
                <inertia-link href="/profile/ticket">{{$t('myTickets')}}</inertia-link>
                <i v-if="tab == 5">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#recent'"></svg-icon>
                </i>
                <inertia-link href="/profile/recently">{{$t('recentVisits')}}</inertia-link>
                <i v-if="tab == 7">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
            <div class="userProducts" v-if="$page.checkSeller">
                <div class="allUserIndexListsItem" @click="btnShow(1)">
                    <i>
                        <svg-icon :icon="'#post'"></svg-icon>
                    </i>
                    <h3>{{$t('myProduct')}}</h3>
                    <i>
                        <svg-icon v-if="showData == 1" class="active" :icon="'#down'"></svg-icon>
                        <svg-icon v-else :icon="'#down'"></svg-icon>
                    </i>
                </div>
                <transition name="slide-fade">
                    <ul v-if="showData == 1">
                        <li>
                            <inertia-link href="/profile/product/create">{{$t('addProduct')}}</inertia-link>
                        </li>
                        <li>
                            <inertia-link href="/profile/product">{{$t('allProduct')}}</inertia-link>
                        </li>
                    </ul>
                </transition>
            </div>
            <div class="allUserIndexListsItem">
                <i>
                    <svg-icon :icon="'#home2'"></svg-icon>
                </i>
                <inertia-link href="/profile">{{$t('manageAccount')}}</inertia-link>
                <i v-if="tab == 0">
                    <svg-icon :icon="'#left'"></svg-icon>
                </i>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
export default {
    name: "UserList",
    props:['tab'],
    components:{
        SvgIcon,
        Dropzone,
    },
    data(){
        return{
            showData: 0,
            profile: this.$page.userData.profile,
            dropzoneOptions: {
                url: '/change-profile',
                timeout: 999999999999999999999999999999999999,
                maxFilesize: 1,
                addRemoveLinks: true,
                parallelUploads: 100,
                maxFiles: 100,
                maxThumbnailFilesize: 100,
                dictDefaultMessage: 'یک فایل PDF را در اینجا رها کنید یا برای انتخاب پرونده برای بارگذاری کلیک کنید.',
                dictFileTooBig: 'فایل ارسالی باید کمتر از 1 مگابایت باشد',
                dictFallbackMessage: 'مرورگر شما از بارگذاری پرونده drag\'n\'drop پشتیبانی نمی کند.',
                dictFallbackText: 'لطفاً برای بارگذاری پرونده های خود مانند روزهای گذشته از فرم بازگشت به پایین استفاده کنید.',
                dictInvalidFileType: 'نمی توانید پرونده هایی از این نوع را بارگذاری کنید.',
                dictResponseError: 'سرور با کد {{statusCode}} پاسخ داد.',
                dictCancelUpload: 'لغو بارگذاری',
                dictCancelUploadConfirmation: 'آیا مطمئن هستید که می خواهید این بارگذاری را لغو کنید؟',
                dictRemoveFile: 'حذف فایل',
                dictMaxFilesExceeded : 'دیگر نمی توانید پرونده بارگذاری کنید.',
                headers: {'X-CSRF-TOKEN': document.head.querySelector("[name=csrf-token]").content}
            },
        }
    },
    methods:{
        uploadAllFile(file , response){
            this.profile = response.url;
        },
        btnShow(num){
            if(this.showData == num){
                this.showData = 0;
            }else{
                this.showData = num;
            }
        }
    }
}
</script>

<style scoped>

</style>
