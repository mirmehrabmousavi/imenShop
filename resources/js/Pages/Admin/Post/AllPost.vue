<template>
    <admin-layout>
        <div class="allPostPanel">
            <div class="allPostPanelTop">
                <h1>همه پست ها</h1>
                <div class="allPostTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/post">همه پست ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length && deletes == 1">حذف</button>
                        <button class="button" @click="openEdit(value[0])" v-if="value.length == 1 && edits == 1">ویرایش سریع</button>
                        <button class="button" @click="btnShow(value[0])" v-if="value.length == 1">نمایش سریع</button>
                        <inertia-link class="button" :href="'/admin/post/create?copy='+value[0]" v-if="value.length == 1">کپی محصول</inertia-link>
                    </div>
                    <div class="allTopTableItem">
                        <div class="filterItems">
                            <div class="filterTitle" @click="showFilter = !showFilter">
                                <i>
                                    <svg-icon :icon="'#filter'"></svg-icon>
                                </i>
                                فیلتر اطلاعات
                            </div>
                            <transition name="bounce">
                                <div class="filterContent" v-if="showFilter">
                                    <div class="filterContentItem">
                                        <label>فیلتر عنوان و آیدی</label>
                                        <input type="text" v-model="search"  placeholder="عنوان یا آیدی را وارد کنید" @keypress.enter="btnSearch(0)">
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر تاریخ</label>
                                        <div class="allTicketPanelTitleDate">
                                            <date-picker
                                                v-model="date"
                                                type="datetime"
                                                format="YYYY-MM-DD"
                                                display-format="jYYYY-jMM-jDD"
                                                @close="btnSearch(0)"
                                                placeholder="تاریخ را وارد کنید"
                                                :timezone="true"
                                            />
                                            <i @click.stop="btnSearch(1)" v-if="date">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر دسته بندی</label>
                                        <div class="allCategoryPanel">
                                            <div class="categoryShow" @click="showAllCat = !showAllCat">
                                                <h4 v-if="sortCat == ''">همه</h4>
                                                <h4 v-else>{{sortCat}}</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showAllCat">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li @click="sendSortCat('')">همه</li>
                                                    <li v-for="item in allCategories" @click="sendSortCat(item.name)">{{item.name}}</li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>فیلتر وضعیت</label>
                                        <div class="allCategoryPanel" @click="showSort = !showSort">
                                            <div class="categoryShow">
                                                <h4 v-if="sort == 0">همه</h4>
                                                <h4 v-if="sort == 1">پیشنویس</h4>
                                                <h4 v-if="sort == 2">منتشر شده</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showSort">
                                                <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                                <li @click="sort = 1" v-on:click="btnSearch(0)">پیشنویس</li>
                                                <li @click="sort = 2" v-on:click="btnSearch(0)">منتشر شده</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="filterContentItem">
                                        <label>تعداد نمایش</label>
                                        <div class="allCategoryPanel" @click="showPage = !showPage">
                                            <div class="categoryShow">
                                                <h4 v-if="getPage == 25">25</h4>
                                                <h4 v-if="getPage == 50">50</h4>
                                                <h4 v-if="getPage == 70">70</h4>
                                                <h4 v-if="getPage == 100">100</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showPage">
                                                <li @click="getPage = 25" v-on:click="btnSearch(0)">25</li>
                                                <li @click="getPage = 50" v-on:click="btnSearch(0)">50</li>
                                                <li @click="getPage = 70" v-on:click="btnSearch(0)">70</li>
                                                <li @click="getPage = 100" v-on:click="btnSearch(0)">100</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
                <div class="paginate">
                    <paginate-panel :link="posts.links"></paginate-panel>
                </div>
                <transition name="slide-fade">
                    <div class="allTableContainer" v-if="getPosts == ''">
                        <div class="postItem" v-for="item in posts.data" @click="getCheck(item.id)">
                            <div class="postTop">
                                <div class="postPic">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                </div>
                                <div class="postTitle">
                                    <h3>{{item.title}}</h3>
                                    <h3 v-if="item.titleEn">{{item.titleEn}}</h3>
                                    <h3 v-else>{{item.title}}</h3>
                                </div>
                                <div class="postOptions">
                                    <inertia-link v-if="edits" title="ویرایش محصول" :href="'/admin/post/' + item.id + '/edit'">
                                        <svg-icon :icon="'#edit'"></svg-icon>
                                    </inertia-link>
                                    <inertia-link :href="'/admin/variety/' + item.id + '/create'" title="افزودن تنوع">
                                        <svg-icon :icon="'#graph'"></svg-icon>
                                    </inertia-link>
                                    <inertia-link :href="'/admin/post/' + item.id + '/show'" title="نمایش محصول">
                                        <svg-icon :icon="'#eye'"></svg-icon>
                                    </inertia-link>
                                    <i v-if="deletes" @click="btnRemoveArray(item.id)" title="حذف محصول"><svg-icon
                                        :icon="'#trash'"></svg-icon></i>
                                </div>
                            </div>
                            <div class="postBot">
                                <ul>
                                    <li>
                                        <span>گروه :</span>
                                        <span v-if="item.category.length">{{item.category[0].name}}</span>
                                        <span v-else>بدون گروه</span>
                                    </li>
                                    <li>
                                        <span>قیمت محصول :</span>
                                        <span>{{ item.offPrice|NumFormat }} تومان</span>
                                    </li>
                                    <li>
                                        <span>تعداد تنوع فعال :</span>
                                        <span>{{ item.post_count }}</span>
                                    </li>
                                    <li>
                                        <span>وضعیت محصول :</span>
                                        <span v-if="item.status == 0">پیشنویس</span>
                                        <span v-if="item.status == 1">منتشر شده</span>
                                    </li>
                                </ul>
                                <i>
                                    <svg-icon v-for="(values , index2) in value" :key="index2" v-if="values == item.id" :icon="'#check'"></svg-icon>
                                    <svg-icon :icon="'#uncheck'"></svg-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </transition>
                <transition name="slide-fade">
                    <div class="createPostPanel" v-if="getPosts">
                        <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                            <span>
                                {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                        </div>
                        <p>توضیحات پست و اطلاعات را اینجا وارد کنید</p>
                        <div class="allCreatePostItems">
                            <div class="allCreatePostItem">
                                <label>عنوان :</label>
                                <input type="text" placeholder="عنوان را وارد کنید ..." v-model="form.title">
                            </div>
                            <div class="allCreatePostItem">
                                <label>عنوان انگلیسی :</label>
                                <input type="text" placeholder="عنوان را وارد کنید ..." v-model="form.titleEn">
                            </div>
                            <div class="allCreatePostItem">
                                <label>پیوند :</label>
                                <input type="text" placeholder="پیوند را وارد کنید ..." v-model="form.slug">
                            </div>
                            <div class="allCreatePostItem">
                                <label>تعداد موجود</label>
                                <input v-model="form.count" type="text" placeholder="تعداد موجود را وارد کنید">
                            </div>
                            <div class="allCreatePostItem">
                                <label>درصد تخفیف</label>
                                <input v-model="form.off" type="text" placeholder="درصد تخفیف را وارد کنید">
                            </div>
                            <div class="allCreatePostItem">
                                <label>قیمت</label>
                                <input v-model="form.price" type="text" placeholder="قیمت را وارد کنید">
                            </div>
                            <div class="allCreatePostItem">
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
                            <div class="allCreatePostItem">
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
                            <div class="allCreatePostItem">
                                <label>خلاصه :</label>
                                <textarea placeholder="خلاصه را وارد کنید ..." v-model="form.summery"></textarea>
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
                            <div class="allCreatePostItemBody">
                                <label>توضیحات :</label>
                                <ckeditor :editor="editor" @ready="onReady" :config="editorConfig" v-model="form.body"></ckeditor>
                            </div>
                        </div>
                        <div class="buttons">
                            <div class="buttonOption" @click="sendUpdate">
                                <div class="overs">
                                    <div class="over"></div>
                                    <div class="over"></div>
                                </div>
                                <button>ارسال اطلاعات</button>
                            </div>
                            <button @click="btnCancel">انصراف</button>
                        </div>
                    </div>
                </transition>
                <div class="paginate">
                    <paginate-panel :link="posts.links"></paginate-panel>
                </div>
            </div>
            <div class="allShowFastPost" v-if="showPost">
                <div class="allShowFastPostPanel">
                    <div class="allShowFastPostPic">
                        <img :src="JSON.parse(showPost.image)[0]">
                    </div>
                    <div class="allShowFastPostItems">
                        <div class="allShowFastPostItemsTitle">
                            <h3>اطلاعات اصلی پست</h3>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>عنوان :</label>
                            <h4>{{showPost.title}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>بازدید :</label>
                            <h4>{{showPost.view_count}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>دیدگاه :</label>
                            <h4>{{showPost.comments_count}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>تعداد موجود :</label>
                            <h4>{{showPost.count}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>وضعیت :</label>
                            <h4 v-if="showPost.status == 0">پیشنویس</h4>
                            <h4 v-else>منتشر شده</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>درصد تخفیف :</label>
                            <h4>{{showPost.off}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>قیمت :</label>
                            <h4>{{showPost.price|NumFormat}}تومان</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>شناسه :</label>
                            <h4>{{showPost.code}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>نویسنده :</label>
                            <h4>{{showPost.user.name}}</h4>
                        </div>
                        <div class="allShowFastPostItem">
                            <label>تاریخ ثبت :</label>
                            <h4>{{showPost.created_at}}</h4>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="showPost = ''">انصراف</button>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import DecoupledEditor from "@ckeditor/ckeditor5-build-decoupled-document";
import ShowImage from "../ShowImage";
import AllTable from '../Table/AllTable.vue';
import PaginatePanel from "../PaginatePanel";
export default {
    name: "AllPost",
    props : ['posts','errors','shows','categories','showPosts','getPost','labels','deletes','edits'],
    metaInfo: {
        title: 'همه پست ها',
    },
    components:{
        PaginatePanel,
        AdminLayout,
        SvgIcon,
        ShowImage,
        datePicker: VuePersianDatetimePicker,
        VuePerfectScrollbar,
        AllTable,
    },
    data() {
        return {
            show: false,
            showSort: false,
            showPage: false,
            showSortType: false,
            showStatus: false,
            showImage: false,
            showFilter: false,
            showAllCat: false,
            allCategories: this.categories,
            search : '',
            sortCat : '',
            getPosts: '',
            showPost: '',
            date : '',
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                title : '',
                titleEn : '',
                slug : '',
                summery : '',
                price : '',
                count : '',
                guarantee : '',
                image : [],
                images : [],
                status : '',
                suggest : '',
                off : '',
                body : '',
                postId : '',
            },
            sort : 0,
            i : 0,
            getPage : 25,
            value : [],
            editor: DecoupledEditor,
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            editorConfig: {
                extraPlugins: [ MyCustomUploadAdapterPlugin ],
            },
        }
    },
    methods:{
        sendSortCat(name){
            this.sortCat = name;
            const url = `/admin/post`;
            this.$inertia.post(url , {
                category : this.sortCat,
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        sendUpdate(){
            this.form.image = JSON.stringify(this.form.images);
            const url = `/admin/post`;
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (this.errors.title || this.errors.price){
                    }else{
                        this.getPosts = '';
                        this.form.title = '';
                        this.form.image = [];
                        this.form.images = [];
                        this.form.price = '';
                        this.form.slug = '';
                        this.form.suggest = '';
                        this.form.status = '';
                        this.form.body = '';
                        this.form.guarantee = '';
                        this.form.count = '';
                        this.form.summery = '';
                        this.form.postId = '';
                    }
                })
        },
        btnShow(id){
            const url = `/admin/post`;
            this.$inertia.post(url,{
                postShow: id
            })
                .then(response=>{
                    this.showPost = this.showPosts;
                })
        },
        deleteImage(index){
            this.form.images.splice(index , 1);
        },
        getClose(){
            this.showImage = false;
        },
        getUrl(url){
            this.form.images.push(url);
        },
        openEdit(id){
            const url = `/admin/post`;
            this.$inertia.post(url,{
                postEdit: id
            })
                .then(response=>{
                    this.getPosts = this.getPost;
                    this.form.title = this.getPosts.title;
                    this.form.images = JSON.parse(this.getPosts.image);
                    this.form.price = this.getPosts.price;
                    this.form.off = this.getPosts.off;
                    this.form.slug = this.getPosts.slug;
                    this.form.suggest = this.getPosts.suggest;
                    this.form.status = this.getPosts.status;
                    this.form.body = this.getPosts.review[0].body;
                    this.form.titleEn = this.getPosts.titleEn;
                    this.form.count = this.getPosts.count;
                    this.form.summery = this.getPosts.body;
                    this.form.postId = this.getPosts.id;
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/post`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                category : this.sortCat,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        getCheck(id) {
            for (this.i; this.i < this.value.length; this.i++) {
                if (this.value[this.i] == id) {
                    this.value.splice(this.i, 1);
                    this.i = 100;
                }
            }
            if (this.i != 101) {
                this.value.push(id);
            }
            this.i = 0;
        },
        btnCancel(){
            this.getPosts = '';
            this.form.title = '';
            this.form.name = '';
            this.form.image = [];
            this.form.images = [];
            this.form.slug = '';
            this.form.type = '';
            this.form.status = '';
            this.form.body = '';
            this.form.budget = '';
            this.form.sell = '';
            this.form.postId = '';
            this.form.summery = '';
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '6');
        },
        sendType(number){
            this.form.type = number;
            this.showType = -1;
        },
        sendStatus(number){
            this.form.status = number;
            this.showType = -1;
        },
        btnRemoveArray(id){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل حذف شده برگشتی ندارد!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    if(id){
                        this.value = [id]
                    }
                    const url = `/admin/post`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        onReady( editor )  {
            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        },
    },
    mounted(){
        this.sidebar();
    }
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
