<template>
    <admin-layout>
        <div class="allPostPanel">
            <div class="allPostPanelTop">
                <h1>همه خبر ها</h1>
                <div class="allPostTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/news">همه خبر ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <div class="button" @click="btnRemove('')" v-if="value.length && deletes == 1">حذف</div>
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
                <div class="paginate" v-if="getPosts == ''">
                    <paginate-panel :link="posts.links"></paginate-panel>
                </div>
                <transition name="slide-fade">
                    <div class="allTableContainer" v-if="getPosts == ''">
                        <all-table :table="posts.data" :nameTable="'news'" :labels="labels" :deletes="deletes" :shows="0" v-on:sendCheck="getCheck" :edits="edits" v-on:sendDelete="btnRemove"></all-table>
                    </div>
                </transition>
                <div class="paginate" v-if="getPosts == ''">
                    <paginate-panel :link="posts.links"></paginate-panel>
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
import PaginatePanel from '../PaginatePanel.vue';
import AllTable from "../Table/AllTable";
export default {
    name: "AllPost",
    props : ['posts','errors','labels','categories','getPost','showPosts','deletes','edits'],
    metaInfo: {
      title: 'همه خبر ها',
    },
    components:{
        AllTable,
        AdminLayout,
        SvgIcon,
        ShowImage,
        datePicker: VuePersianDatetimePicker,
        VuePerfectScrollbar,
        PaginatePanel,
    },
    data() {
        return {
            show: false,
            showSort: false,
            showPage: false,
            showSortType: false,
            showStatus: false,
            showImage: false,
            showPost: null,
            showType: 0,
            sortType:0,
            showFilter: false,
            showAllCat: false,
            allCategories: this.categories,
            search : '',
            sortCat : '',
            getPosts: '',
            date : '',
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                title : '',
                slug : '',
                summary : '',
                sell : '',
                budget : '',
                name : '',
                image : '',
                status : '',
                type : '',
                suggest : '',
                body : '',
                postId : '',
            },
            sort : 0,
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
            const url = `/admin/news`;
            this.$inertia.post(url , {
                category : this.sortCat,
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/news`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                category : this.sortCat,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        checkAll(){
            this.i = 0;
            if(this.posts.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.posts.data.length; this.i++) {
                    this.value.push(this.posts.data[this.i].id);
                }
                this.i = 0;
            }
        },
        getCheck(id){
            for ( this.i ; this.i <  this.value.length; this.i++) {
                if (this.value[this.i] == id){
                    this.value.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.value.push(id);
            }
            this.i = 0;
        },
        btnRemove(id){
            if(id){
                this.value = [id]
            }
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
                    this.value = [];
                    this.value.push(id);
                    const url = `/admin/news`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        sendType(number){
            this.form.type = number;
            this.showType = -1;
        },
        sidebars(){
            this.$eventHub.emit('sidebar' , 10);
        },
        sendStatus(number){
            this.form.status = number;
            this.showType = -1;
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
