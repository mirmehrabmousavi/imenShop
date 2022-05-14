<template>
    <admin-layout>
        <div class="allExcelPanel">
            <div class="allExcelPanelTop">
                <h1>ایجاد محصول با وارد کردن اکسل</h1>
                <div class="allExcelPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/import/product">ایجاد محصول با وارد کردن اکسل</inertia-link>
                </div>
            </div>
            <div class="importData">
                <dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true>
                    <div class="dropzone-custom-content">
                        <h3 class="dropzone-custom-title">برای بارگذاری فایل اکسل ، بکشید و رها کنید!</h3>
                        <div class="subtitle">یا برای انتخاب از رایانه خود کلیک کنید ...</div>
                    </div>
                </dropzone>
            </div>
            <div class="infoImport">
                <ul>
                    <li>مقدار اول فایل اکسل شما باید عنوان محصول</li>
                    <li>مقدار دوم فایل اکسل شما باید قیمت محصول شما باشد</li>
                    <li>مقدار سوم فایل اکسل شما باید کد محصول شما باشد</li>
                    <li>مقدار چهارم فایل اکسل شما باید تعداد محصول شما باشد</li>
                    <li>مقدار پنجم فایل اکسل شما باید آدرس تصویر محصول شما باشد</li>
                    <li>مقدار ششم فایل اکسل شما باید توضیح محصول شما باشد</li>
                </ul>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import AdminLayout from "../../../components/layout/AdminLayout";
export default {
    name: "ExcelCreate",
    components: {AdminLayout,Dropzone},
    metaInfo: {
        title: 'وارد کردن اکسل'
    },
    data() {
        return{
            dropzoneOptions: {
                url: '/admin/import-excel',
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
        }
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , 11);
        }
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
