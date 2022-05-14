<template>
    <admin-layout>
        <div class="allCreatePost">
            <div class="allPostPanelTop">
                <h1>ویرایش فایل</h1>
                <div class="allPostTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/file">همه فایل ها</inertia-link>
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
                        <show-image v-on:sendClose="getClose" v-if="showImage" v-on:sendUrl="getUrl"></show-image>
                        <div class="getImageItem" @click="showImage = !showImage">
                            <span v-if="form.images.length == 0">تصویر شاخص خود را وارد کنید</span>
                            <div class="getImagePic" v-else v-for="(item , index) in form.images" :key="index">
                                <i @click.stop="deleteImage(index)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                                <img :src="item">
                            </div>
                        </div>
                    </div>
                    <div class="sendGallery">
                        <show-image v-on:sendClose="getClose" v-if="showFile" v-on:sendUrl="getUrlFile"></show-image>
                        <div class="getImageItem" @click="showFile = !showFile">
                            <span v-if="form.file == ''">فایل شاخص خود را وارد کنید</span>
                            <div class="getImagePic" v-else>
                                <img src="/img/zip.png">
                            </div>
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
                    <div class="abilityPost hidden">
                    </div>
                    <div class="abilityPost hidden">
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
                                    <label>پیوند(slug) :</label>
                                    <input type="text"  placeholder="پیوند را وارد کنید" v-model="form.slug">
                                </div>
                                <div class="allCreatePostDetailItem">
                                    <label>وضعیت :</label>
                                    <div class="allCategoryPanel" @click.stop="showStatus = !showStatus">
                                        <div class="categoryShow">
                                            <h4 v-if="form.status == null">وضعیت را وارد کنید ...</h4>
                                            <h4 v-if="form.status == 0">پیشنویس</h4>
                                            <h4 v-if="form.status == 1">منتشر شده</h4>
                                        </div>
                                        <ul v-if="showStatus">
                                            <li @click.stop="sendStatus(0)">پیشنویس</li>
                                            <li @click.stop="sendStatus(1)">منتشر شده</li>
                                        </ul>
                                    </div>
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
                                    <label>پیشنهاد</label>
                                    <div class="timerItem">
                                        <date-picker
                                            v-model="form.suggest"
                                            type="datetime"
                                            format="YYYY-MM-DD HH:mm"
                                            display-format="jYYYY-jMM-jDD HH:mm"
                                            :timezone="true"
                                        />
                                        <i @click="form.suggest = ''" v-if="form.suggest">
                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                        </i>
                                    </div>
                                </div>
                                <div class="allCreatePostDetailItem">
                                    <label for="s1d" class="allCreatePostDetailItemData">
                                        در ویترین آرشیو
                                        <input id="s1d" type="checkbox" class="switch" v-model="form.showcase">
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
                                    <post-taxonami :taxes="categories" :taxRoute="'دسته بندی'" :tax="posts.category"  v-on:sendTax="getCat"></post-taxonami>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import ShowImage from "../ShowImage";
import SvgIcon from "../../Svg/SvgIcon";
import ClassicEditor from '@ckeditor/ckeditor5-build-decoupled-document';
import CKEditor from '@ckeditor/ckeditor5-vue2'
import PostTaxonami from "../../Home/User/PostTaxonami";
export default {
    name: "EditPost",
    props : ['categories','guarantees','brands','posts','times','allow','userData','errors'],
    components:{
        PostTaxonami,
        AdminLayout,
        CKEditor: CKEditor.component,
        ShowImage,
        SvgIcon,
    },
    metaInfo: {
      title: 'ویرایش پست'
    },
    data(){
        return{
            showDetail: -1,
            showStatus:false,
            showImage: false,
            showFile: false,
            showLang1: 0,
            showLang2: 0,
            showLang3: 0,
            form:{
                summery : this.posts.body,
                summeryEn : this.posts.bodyEn,
                price : this.posts.offPrice,
                count : this.posts.count,
                slug : this.posts.slug,
                status : this.posts.status,
                title : this.posts.title,
                file : this.posts.file,
                titleEn : this.posts.titleEn,
                body : this.posts.review[0].body,
                bodyEn : this.posts.review[0].bodyEn,
                suggest : this.posts.suggest,
                off : this.posts.off,
                images : JSON.parse(this.posts.image),
                used: this.posts.used,
                original: this.posts.original,
                showcase: this.posts.showcase,
                image : [],
                allAbility : [],
                allRate : [],
                allProperty : [],
                allCategory: null,
                allSize: [],
                allColor: [],
                allGuarantee: [],
                allBrand: null,
                abilities: JSON.parse(this.posts.review[0].ability),
                rates: JSON.parse(this.posts.review[0].rate),
                properties:JSON.parse(this.posts.review[0].specifications),
                sizes: JSON.parse(this.posts.review[0].size),
                colors: JSON.parse(this.posts.review[0].colors),
            },
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            editor: ClassicEditor,
            editorConfig: {
                extraPlugins: [ MyCustomUploadAdapterPlugin ],
            },
        }
    },
    methods:{
        sendData(){
            this.form.image = JSON.stringify(this.form.images);
            this.form.allAbility = JSON.stringify(this.form.abilities);
            this.form.allRate = JSON.stringify(this.form.rates);
            this.form.allProperty = JSON.stringify(this.form.properties);
            this.form.allColor = JSON.stringify(this.form.colors);
            this.form.allSize = JSON.stringify(this.form.sizes);
            const url = `/admin/file/${this.posts.id}/edit`;
            this.$inertia.post(url , this.form);
        },
        checkShowDetail(number){
            if(this.showDetail == number){
                this.showDetail = 0;
            }else{
                this.showDetail = number;
            }
        },
        getUrlFile(url){
            this.form.file = url;
        },
        getGuarantees(guarantee){
            this.form.allGuarantee = guarantee;
        },
        getTime(time){
            this.form.allTime = time;
        },
        deleteRate(index){
            this.form.rates.splice(index,1);
        },
        deleteProperties(index){
            this.form.properties.splice(index,1);
        },
        deleteAbility(index){
            this.form.abilities.splice(index,1);
        },
        deleteSize(index){
            this.form.sizes.splice(index,1);
        },
        deleteColor(index){
            this.form.colors.splice(index,1);
        },
        addAbility() {
            this.form.abilities.push({
                name:'',
                nameEn:'',
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
        getColor(Color){
            this.form.allColor = Color;
        },
        getSize(Size){
            this.form.allSize = Size;
        },
        deleteImage(index){
            this.form.images.splice(index , 1);
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '6');
        },
        onReady( editor )  {
            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        },
    },
    mounted() {
        this.sidebar();
    },
}
class MyUploadAdapter {
    constructor( loader ) {
        this.loader = loader;
    }
    upload() {
        return this.loader.file
            .then( file => new Promise( ( resolve, reject ) => {
                this._initRequest();
                this._initListeners( resolve, reject, file );
                this._sendRequest( file );
            } ) );
    }
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();
        xhr.open( 'POST', '/admin/gallery', true );
        xhr.responseType = 'json';
    }
    _initListeners( resolve, reject, file ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${ file.name }.`;
        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {
            const response = xhr.response;
            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }
            resolve( {
                default: response.url
            } );
        } );
        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }
    _sendRequest( file ) {
        const data = new FormData();
        data.append( 'image', file );
        this.xhr.send( data );
    }
}
function MyCustomUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new MyUploadAdapter( loader );
    };
}
</script>

<style scoped>

</style>
