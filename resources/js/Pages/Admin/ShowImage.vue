<template>
    <section class="allShowGallery">
        <div class="panelImage">
            <div class="showAllImage">
                <div class="topImage">
                    <div class="titleImage">
                        <h3>
                            <i>
                                <svg-icon :icon="'#align'"></svg-icon>
                            </i>
                            مدیریت فایل
                        </h3>
                        <div class="address">
                            <a href="/">خانه</a>
                            <span>/</span>
                            <a href="/admin">داشبورد</a>
                            <span>/</span>
                            <span>مدیریت فایل</span>
                        </div>
                    </div>
                    <div class="buttonsImage">
                        <i class="icon-002-image-file" v-if="index == 1" @click.prevent="getImage">
                            <svg-icon :icon="'#images'"></svg-icon>
                        </i>
                        <i class="icon-plus" @click.prevent="index = 1" v-if="index == 0">
                            <svg-icon :icon="'#plus'"></svg-icon>
                        </i>
                        <input v-model="search" type="text" placeholder="جستجو..." @keyup="btnSearch">
                        <button @click.prevent="btnSendClose">
                            <svg-icon :icon="'#cancel'"></svg-icon>
                        </button>
                    </div>
                </div>
                <div class="botNav">
                    <div class="images" v-if="index == 0">
                        <div class="loader" v-if="showLoader">
                            <img src="/img/loaderHome.gif" alt="">
                        </div>
                        <div class="itemImages" v-if="!user">
                            <select v-model="show">
                                <option value="0" @click="choice(show = 0)">همه</option>
                                <option value="1" @click="choice(show = 1)">تصاویر</option>
                                <option value="2" @click="choice(show = 2)">موزیک</option>
                                <option value="3" @click="choice(show = 3)">ویدیو</option>
                            </select>
                        </div>
                        <div class="allImages">
                            <ul>
                                <li v-for="item in galleries" @click.prevent="sendUrl(item.url)">
                                    <img v-if="item.type == 'zip' || item.type == 'rar'" src="/img/zip.png" alt="">
                                    <img v-else :src="item.url" alt="">
                                    <div class="subject">
                                        <i>
                                            <svg-icon :icon="'#images'"></svg-icon>
                                        </i>
                                        <span>{{item.name}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="createImage" v-if="index == 1 && user">
                        <dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptionsUser" :useCustomSlot=true>
                            <div class="dropzone-custom-content">
                                <h3 class="dropzone-custom-title">برای بارگذاری محتوا ، بکشید و رها کنید!</h3>
                                <div class="subtitle">یا برای انتخاب پرونده از رایانه خود کلیک کنید ...</div>
                            </div>
                        </dropzone>
                    </div>
                    <div class="createImage" v-if="index == 1 && !user">
                        <dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true>
                            <div class="dropzone-custom-content">
                                <h3 class="dropzone-custom-title">برای بارگذاری محتوا ، بکشید و رها کنید!</h3>
                                <div class="subtitle">یا برای انتخاب پرونده از رایانه خود کلیک کنید ...</div>
                            </div>
                        </dropzone>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import SvgIcon from "../Svg/SvgIcon";
    import Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import PaginatePanel from "./PaginatePanel";
    export default {
        name: "ShowImage",
        props:['user'],
        components: {
            PaginatePanel,
            SvgIcon,
            Dropzone
        },
        data(){
            return{
                dropzoneOptions: {
                    url: '/admin/gallery/create',
                    thumbnailWidth: 150,
                    maxFilesize: 12000000,
                    timeout: 999999999999999999999999999999999999,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'یک فایل PDF را در اینجا رها کنید یا برای انتخاب پرونده برای بارگذاری کلیک کنید.',
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
                dropzoneOptionsUser: {
                    url: '/upload-image',
                    thumbnailWidth: 150,
                    maxFilesize: 12000000,
                    timeout: 999999999999999999999999999999999999,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'یک فایل PDF را در اینجا رها کنید یا برای انتخاب پرونده برای بارگذاری کلیک کنید.',
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
                settings: {
                    maxScrollbarLength: 70,
                },
                showLoader:false,
                showText1 : false,
                showTax : false,
                search : '',
                show : 0,
                index : 0,
                image : [],
                galleries : [],
                folders : [],
            }
        },
        methods:{
            choice() {
                this.showLoader = true;
                if (this.show == 0){
                    this.getImage();
                }else{
                    const url = '/admin/show-choice';
                    axios.post(url,{
                        show : this.show
                    })
                        .then(response=>{
                            this.showLoader = false;
                            this.galleries = response.data;
                        });
                }
            },
            getImage() {
                this.show = 0;
                this.index = 0;
                this.showLoader = true;
                if(this.user){
                  const url = '/profile/gallery';
                  axios.get(url)
                      .then(response=>{
                        this.showLoader = false;
                        this.galleries = response.data;
                      });
                }else{
                  const url = '/admin/get-image';
                  axios.get(url)
                      .then(response=>{
                        this.showLoader = false;
                        this.galleries = response.data;
                      });
                }
            },
            btnSendClose(){
                this.$emit('sendClose')
            },
            btnSearch(){
                this.show = 0;
                if (this.search == ''){
                    this.getImage();
                }else{
                    this.showLoader = true;
                    const url = '/admin/search-gallery';
                    axios.post(url ,{
                        search: this.search
                    })
                        .then(response=>{
                            this.showLoader = false;
                            this.galleries = response.data;
                        })
                }
            },
            btnShowText1(index) {
                if (this.showText1 === index){
                    this.showText1 = false
                }else{
                    this.showText1 = index;
                }
            },
            sendUrl(url){
                this.$emit('sendUrl' , url)
                this.$emit('sendClose')
            },
            getPage(page){
                this.galleries = page;
            },
        },
        mounted() {
            this.getImage();
        }
    }
</script>

<style scoped>

</style>
