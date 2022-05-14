<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list></user-list>
            </div>
            <div class="allCreatePost">
                <div class="allPostPanelTop">
                    <h1>افزودن محصول</h1>
                    <div class="allPostTitle">
                        <inertia-link href="/">خانه</inertia-link>
                        <span>/</span>
                        <inertia-link href="/profile/product">همه محصول ها</inertia-link>
                        <span>/</span>
                        <inertia-link href="/profile/product/create">افزودن محصول</inertia-link>
                    </div>
                </div>
                <div class="allCreatePostData">
                    <div class="allCreatePostSubject">
                        <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                            <span>
                                {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                        </div>
                        <div class="allCreatePostItemShow">
                            <div class="allCreatePostItemTabs">
                                <div class="allCreatePostItemTab" @click="showLang1 = 0">
                                    <button class="active" v-if="showLang1 == 0">فارسی</button>
                                    <button v-else>فارسی</button>
                                </div>
                                <div class="allCreatePostItemTab" @click="showLang1 = 1">
                                    <button class="active" v-if="showLang1 == 1">انگلیسی</button>
                                    <button v-else>انگلیسی</button>
                                </div>
                            </div>
                            <div class="allCreatePostItem" v-if="showLang1 == 0">
                                <label>توضیح اجمالی :</label>
                                <textarea placeholder="توضیح را وارد کنید" v-model="form.summery"></textarea>
                            </div>
                            <div class="allCreatePostItem" v-if="showLang1 == 1">
                                <label>توضیح اجمالی انگلیسی :</label>
                                <textarea placeholder="توضیح را وارد کنید" v-model="form.summeryEn"></textarea>
                            </div>
                        </div>
                        <div class="allCreatePostItemShow">
                            <div class="allCreatePostItemTabs">
                                <div class="allCreatePostItemTab" @click="showLang2 = 0">
                                    <button class="active" v-if="showLang2 == 0">فارسی</button>
                                    <button v-else>فارسی</button>
                                </div>
                                <div class="allCreatePostItemTab" @click="showLang2 = 1">
                                    <button class="active" v-if="showLang2 == 1">انگلیسی</button>
                                    <button v-else>انگلیسی</button>
                                </div>
                            </div>
                            <div class="allCreatePostItem" v-if="showLang2 == 0">
                                <label>توضیحات :</label>
                                <CKEditor :editor="editor" @ready="onReady" :config="editorConfig" v-model="form.body"></CKEditor>
                            </div>
                            <div class="allCreatePostItem" v-if="showLang2 == 1">
                                <label>توضیحات انگلیسی :</label>
                                <CKEditor :editor="editor" @ready="onReady" :config="editorConfig" v-model="form.bodyEn"></CKEditor>
                            </div>
                        </div>
                        <div class="sendGallery">
                            <div class="sendFileItem">
                                <dropzone ref="myVueDropzone3" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true v-on:vdropzone-success="uploadAllFile">
                                    <div class="dropzone-custom-content">
                                        <h3 class="dropzone-custom-title">برای بارگذاری فایل ، بکشید و رها کنید!</h3>
                                        <div class="subtitle">یا برای انتخاب از رایانه خود کلیک کنید ...</div>
                                    </div>
                                </dropzone>
                            </div>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>ویژگی‌های محصول</label>
                                <i @click="addAbility">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>ویژگی‌های محصول</th>
                                    <th>ویژگی‌های انگلیسی محصول</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.abilities" :key="index">
                                    <td>
                                        <input type="text" placeholder="ویژگی‌ را وارد کنید" v-model="item.name">
                                    </td>
                                    <td>
                                        <input type="text" placeholder="ویژگی‌ را وارد کنید" v-model="item.nameEn">
                                    </td>
                                    <td>
                                        <i @click="deleteAbility(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>امتیاز به ویژگی‌</label>
                                <i @click="addRate">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>ویژگی‌</th>
                                    <th>ویژگی انگلیسی‌</th>
                                    <th>امتیاز ( 0 , 4 )</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.rates" :key="index">
                                    <td>
                                        <input type="text" v-model="item.name" placeholder="ویژگی‌ را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.nameEn" placeholder="ویژگی‌ را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="range" v-model="item.rate" min="0" max="4">
                                    </td>
                                    <td>
                                        <i @click="deleteRate(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>مشخصات‌</label>
                                <i @click="addProperties">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>مشخصات‌</th>
                                    <th>توضیح</th>
                                    <th>مشخصات انگلیسی‌</th>
                                    <th>توضیح انگلیسی‌</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.properties" :key="index">
                                    <td>
                                        <input type="text" v-model="item.title" placeholder="مشخصات‌ را وارد کنید">
                                    </td>
                                    <td>
                                        <textarea v-model="item.body" placeholder="توضیح را وارد کنید"></textarea>
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.titleEn" placeholder="مشخصات‌ را وارد کنید">
                                    </td>
                                    <td>
                                        <textarea v-model="item.bodyEn" placeholder="توضیح را وارد کنید"></textarea>
                                    </td>
                                    <td>
                                        <i @click="deleteProperties(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>رنگ</label>
                                <i @click="addColor">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>نام رنگ</th>
                                    <th>نام انگلیسی</th>
                                    <th>کد رنگ</th>
                                    <th>تعداد</th>
                                    <th>افزودن قیمت (تومان)</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.colors" :key="index">
                                    <td>
                                        <input type="text" v-model="item.name" placeholder="نام را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.nameEn" placeholder="نام را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.color" placeholder="کد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.count" placeholder="تعداد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.price" placeholder="قیمت را وارد کنید">
                                    </td>
                                    <td>
                                        <i @click="deleteColor(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <div class="abilityPostToolTip">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p>برای اضافه نشدن قیمت به قیمت اصلی عدد صفر را وارد کنید</p>
                            </div>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>سایز</label>
                                <i @click="addSize">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>سایز</th>
                                    <th>سایز انگلیسی</th>
                                    <th>تعداد</th>
                                    <th>افزودن قیمت (تومان)</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.sizes" :key="index">
                                    <td>
                                        <input type="text" v-model="item.name" placeholder="سایز را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.nameEn" placeholder="سایز را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.count" placeholder="تعداد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.price" placeholder="قیمت را وارد کنید">
                                    </td>
                                    <td>
                                        <i @click="deleteSize(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <div class="abilityPostToolTip">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p>برای اضافه نشدن قیمت به قیمت اصلی عدد صفر را وارد کنید</p>
                            </div>
                        </div>
                        <button class="button" @click="sendData">ارسال اطلاعات</button>
                    </div>
                    <div class="allCreatePostDetails">
                        <div class="allCreatePostDetail">
                            <div class="allCreatePostDetailItemsTitle" @click="checkShowDetail(1)">
                                جزییات
                                <svg-icon :icon="'#up'" v-if="showDetail == 1"></svg-icon>
                                <svg-icon :icon="'#down'" v-else></svg-icon>
                            </div>
                            <transition name="slide-fade">
                                <div class="allCreatePostDetailItems" v-if="showDetail == 1">
                                    <div class="allCreatePostDetailItem">
                                        <label>عنوان :</label>
                                        <input type="text"  placeholder="عنوان را وارد کنید" v-model="form.title">
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label>عنوان انگلیسی :</label>
                                        <input type="text"  placeholder="عنوان را وارد کنید" v-model="form.titleEn">
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label>درصد تخفیف(50) :</label>
                                        <input type="text" v-model="form.off" placeholder="تخفیف را وارد کنید">
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label>قیمت(تومان) :</label>
                                        <input type="text" v-model="form.price" placeholder="قیمت را وارد کنید">
                                    </div>
                                </div>
                            </transition>
                        </div>
                        <div class="allCreatePostDetail">
                            <div class="allCreatePostDetailItemsTitle" @click="checkShowDetail(3)">
                                اطلاعات بیشتر
                                <svg-icon :icon="'#up'" v-if="showDetail == 3"></svg-icon>
                                <svg-icon :icon="'#down'" v-else></svg-icon>
                            </div>
                            <transition name="slide-fade">
                                <div class="allCreatePostDetailItems" v-if="showDetail == 3">
                                    <div class="allCreatePostDetailItem">
                                        <label>تعداد :</label>
                                        <input type="text" v-model="form.count" placeholder="تعداد را وارد کنید">
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label for="s2d" class="allCreatePostDetailItemData">
                                            اصل
                                            <input id="s2d" type="checkbox" class="switch" v-model="form.original">
                                        </label>
                                        <label for="s3d" class="allCreatePostDetailItemData">
                                            کارکرده
                                            <input id="s3d" type="checkbox" class="switch" v-model="form.used">
                                        </label>
                                    </div>
                                </div>
                            </transition>
                        </div>
                        <div class="allCreatePostDetail">
                            <div class="allCreatePostDetailItemsTitle">
                                تاکسونامی
                            </div>
                            <transition name="slide-fade">
                                <div class="allCreatePostDetailItems">
                                    <div class="allCreatePostDetailItem">
                                        <label>دسته بندی :</label>
                                        <post-taxonami add="1" :taxes="categories" :taxRoute="'دسته بندی'" :tax="['0']"  v-on:sendTax="getCat"></post-taxonami>
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label>برند :</label>
                                        <post-taxonami add="1" :taxes="brands" :taxRoute="'برند'" :tax="['0']"  v-on:sendTax="getBrand"></post-taxonami>
                                    </div>
                                    <div class="allCreatePostDetailItem">
                                        <label>گارانتی :</label>
                                        <post-taxonami add="1" :taxes="guarantees" :taxRoute="'گارانتی'" :tax="['0']"  v-on:sendTax="getGuarantees"></post-taxonami>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from '../../Svg/SvgIcon.vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-decoupled-document';
import CKEditor from '@ckeditor/ckeditor5-vue2'
import PostTaxonami from './PostTaxonami.vue';
import HomeLayout from "../../../components/layout/HomeLayout";
import Dropzone from 'vue2-dropzone';
import UserList from "./UserList";
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
export default {
    name: "PostCreate",
    props : ['categories','brands','guarantees','allow','userData','errors'],
    components: {UserList, Dropzone , HomeLayout, SvgIcon ,CKEditor: CKEditor.component, PostTaxonami },
    metaInfo: {
        title: 'افزودن پست',
    },
    data(){
        return{
            showDetail: -1,
            showStatus:false,
            showImage: false,
            showLang1: 0,
            showLang2: 0,
            showLang3: 0,
            form:{
                summery : null,
                summeryEn : null,
                price : null,
                count : null,
                title : null,
                titleEn : null,
                body : null,
                bodyEn : null,
                off : '',
                images : [],
                used: 0,
                original: 0,
                image : [],
                allAbility : [],
                allRate : [],
                allProperty : [],
                allCategory: null,
                allSize: [],
                allColor: [],
                allBrand: null,
                allGuarantee: [],
                abilities: [],
                rates: [],
                properties:[],
                sizes:[],
                colors:[],
            },
            dropzoneOptions: {
                url: '/upload-image',
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
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            editor: ClassicEditor,
            editorConfig: {
                extraPlugins: [ MyCustomUploadAdapterPlugin ],
            },
        }
    },
    methods:{
        uploadAllFile(file , response){
            this.form.images.push(response.url);
        },
        sendData(){
            this.form.image = JSON.stringify(this.form.images);
            this.form.allAbility = JSON.stringify(this.form.abilities);
            this.form.allRate = JSON.stringify(this.form.rates);
            this.form.allProperty = JSON.stringify(this.form.properties);
            this.form.allColor = JSON.stringify(this.form.colors);
            this.form.allSize = JSON.stringify(this.form.sizes);
            const url = `/profile/product/create`;
            this.$inertia.post(url , this.form)
                .then(response=>{
                    if (Object.keys(this.errors).length <= 0){
                        this.$eventHub.emit('deleteTax');
                        this.form.summery = null;
                        this.form.summeryEn = null;
                        this.form.price = null;
                        this.form.abilities = [];
                        this.form.size = [];
                        this.form.colors = [];
                        this.form.rates = [];
                        this.form.properties = [];
                        this.form.title = null;
                        this.form.body = '';
                        this.form.bodyEn = '';
                        this.form.used = 0;
                        this.form.original = 0;
                        this.form.titleEn = null;
                        this.form.images = [];
                        this.form.off = null;
                        this.form.count = null;
                        this.form.allCategory = [];
                        this.form.allSize = [];
                        this.form.allBrand = [];
                        this.form.allColor = [];
                        this.form.allGuarantee = [];
                    }
                })
        },
        checkShowDetail(number){
            if(this.showDetail == number){
                this.showDetail = 0;
            }else{
                this.showDetail = number;
            }
        },
        deleteRate(index){
            this.form.rates.splice(index,1);
        },
        deleteSize(index){
            this.form.sizes.splice(index,1);
        },
        deleteColor(index){
            this.form.colors.splice(index,1);
        },
        deleteProperties(index){
            this.form.properties.splice(index,1);
        },
        deleteAbility(index){
            this.form.abilities.splice(index,1);
        },
        addAbility() {
            this.form.abilities.push({
                name:'',
                nameEn:'',
            });
        },
        addRate() {
            this.form.rates.push({
                name:'',
                nameEn:'',
                rate:2,
            });
        },
        addProperties() {
            this.form.properties.push({
                title:'',
                body:'',
                titleEn:'',
                bodyEn:'',
            });
        },
        addColor() {
            this.form.colors.push({
                name:'',
                nameEn:'',
                color:'',
                price:'',
            });
        },
        addSize() {
            this.form.sizes.push({
                name:'',
                nameEn:'',
                price:'',
            });
        },
        sidebars(){
            this.$eventHub.emit('sidebar' , '6');
        },
        sendStatus(number){
            this.form.status = number;
            this.showStatus = false;
        },
        getClose(){
            this.showImage = false;
        },
        getUrl(url){
            this.form.images.push(url);
        },
        getCat(cat){
            this.form.allCategory = cat;
        },
        getBrand(brand){
            this.form.allBrand = brand;
        },
        getGuarantees(guarantee){
            this.form.allGuarantee = guarantee;
        },
        getColor(Color){
            this.form.allColor = Color;
        },
        getSize(Size){
            this.form.allSize = Size;
        },
        deleteImage(index){
            this.form.images.splice(index , 1);
        },
        onReady( editor )  {
            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        },
    },
    mounted(){
        this.sidebars();
    }
}
class MediaUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(uploadedFile => {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('image', uploadedFile);

                axios.post('/admin/gallery/create-image', formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data;',
                            '_token': document.head.querySelector('meta[name="csrf-token"]'),
                        }
                    }
                ).then(response => {
                    if (response.status == 201) {
                        resolve( {
                            default: response.data.url
                        } );
                    } else {
                        reject(response.data.message);
                    }
                }).catch(error => {
                    console.log(error.response.status, error.response.data.message);
                });
            });
        });
    }

    abort() {
    }
}
function MyCustomUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new MediaUploadAdapter( loader );
    };
}
</script>

<style scoped>

</style>
