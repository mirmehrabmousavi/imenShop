<template>
    <admin-layout>
        <div class="allGalleryPanel">
            <div class="allGalleryTop">
                <h1>گالری</h1>
                <div class="allGalleryTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/gallery">گالری</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/gallery" v-if="container == 0">فایل های اخیر</inertia-link>
                    <inertia-link href="/admin/gallery" v-if="container == 1">تصاویر</inertia-link>
                    <inertia-link href="/admin/gallery" v-if="container == 2">فایل ها</inertia-link>
                    <inertia-link href="/admin/gallery" v-if="container == 3">ویدیو ها</inertia-link>
                    <inertia-link href="/admin/gallery" v-if="container == 4">سطل آشغال</inertia-link>
                </div>
            </div>
            <div class="allGalleryPanelFiles" v-if="fileDetail == null">
                <div class="allGalleryDrop">
                    <dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true v-on:vdropzone-queue-complete="uploadAllFiles">
                        <div class="dropzone-custom-content">
                            <h3 class="dropzone-custom-title">برای بارگذاری محتوا ، بکشید و رها کنید!</h3>
                            <div class="subtitle">یا برای انتخاب از رایانه خود کلیک کنید ...</div>
                        </div>
                    </dropzone>
                </div>
                <div class="allGalleryCategories">
                    <div class="allGalleryCategoriesItem" @click="changeShow(0)">
                        <div class="allGalleryCategoriesItemOver">
                            <svg-icon :icon="'#right'"></svg-icon>
                        </div>
                        <svg-icon :icon="'#recently'"></svg-icon>
                        <div class="allGalleryCategoriesItemSubject">
                            <h3>اخیرا</h3>
                            <span>{{counts[0]|NumFormat}} عدد</span>
                        </div>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </div>
                    <div class="allGalleryCategoriesItem" @click="changeShow(1)">
                        <div class="allGalleryCategoriesItemOver">
                            <svg-icon :icon="'#right'"></svg-icon>
                        </div>
                        <svg-icon :icon="'#image'"></svg-icon>
                        <div class="allGalleryCategoriesItemSubject">
                            <h3>تصاویر</h3>
                            <span>{{counts[1]|NumFormat}} عدد</span>
                        </div>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </div>
                    <div class="allGalleryCategoriesItem" @click="changeShow(2)">
                        <div class="allGalleryCategoriesItemOver">
                            <svg-icon :icon="'#right'"></svg-icon>
                        </div>
                        <svg-icon :icon="'#music'"></svg-icon>
                        <div class="allGalleryCategoriesItemSubject">
                            <h3>موزیک ها</h3>
                            <span>{{counts[2]|NumFormat}} عدد</span>
                        </div>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </div>
                    <div class="allGalleryCategoriesItem" @click="changeShow(3)">
                        <div class="allGalleryCategoriesItemOver">
                            <svg-icon :icon="'#right'"></svg-icon>
                        </div>
                        <svg-icon :icon="'#video'"></svg-icon>
                        <div class="allGalleryCategoriesItemSubject">
                            <h3>ویدیو ها</h3>
                            <span>{{counts[3]|NumFormat}} عدد</span>
                        </div>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </div>
                    <div class="allGalleryCategoriesItem" @click="changeShow(4)">
                        <div class="allGalleryCategoriesItemOver">
                            <svg-icon :icon="'#right'"></svg-icon>
                        </div>
                        <svg-icon :icon="'#trash'"></svg-icon>
                        <div class="allGalleryCategoriesItemSubject">
                            <h3>سطل آشغال</h3>
                            <span>{{counts[4]|NumFormat}} عدد</span>
                        </div>
                        <svg-icon :icon="'#left'"></svg-icon>
                    </div>
                </div>
                <div class="allGalleryPanelFilesOptions">
                    <div class="allGalleryPanelFilesOptionsButtons">
                        <button @click="checkAll" v-if="galleries.data.length">انتخاب همه</button>
                        <button v-if="value.length" @click="btnRemoveAll('')">حذف</button>
                        <button v-if="container == 4 && value.length" @click="returnImage">بازگرداندن</button>
                    </div>
                    <div class="imageContentOptionsFilter">
                        <div class="imageContentOptionsFilterButton" @click="showFilter = !showFilter">
                            <svg-icon :icon="'#filter'"></svg-icon>
                            فیلتر اطلاعات
                        </div>
                        <transition name="bounce">
                            <div class="filterContent" v-if="showFilter">
                                <div class="filterContentItem">
                                    <label>فیلتر نام</label>
                                    <input type="text" v-model="search"  placeholder="نام را وارد کنید" @keypress.enter="btnSearch(0)">
                                </div>
                                <div class="filterContentItem">
                                    <label>فیلتر تاریخ</label>
                                    <div class="allTicketPanelTitleDate">
                                        <date-picker
                                            v-model="date"
                                            type="datetime"
                                            format="YYYY-MM-DD"
                                            display-format="jYYYY-jMM-jDD"
                                            @close="btnSearch"
                                            placeholder="تاریخ را وارد کنید"
                                            :timezone="true"
                                        />
                                        <i @click.stop="btnSearch(1)" v-if="date">
                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="paginate">
                    <paginate-panel :link="galleries.links"></paginate-panel>
                </div>
                <ul>
                    <li v-for="item in galleries.data" @click="btnSendInfo(item.id)" :key="item.id">
                        <div class="checkIcon">
                            <i class="icon-check-square" v-for="values in value" :key="values.id" v-if="values == item.id">
                                <svg-icon :icon="'#check'"></svg-icon>
                            </i>
                        </div>
                        <div class="itemsPic">
                            <img v-if="item.type == 'mp3' || item.type == 'mkv'" src="/img/music.png" alt="">
                            <img v-else-if="item.type == 'zip' || item.type == 'rar'" src="/img/zip.png" alt="">
                            <img v-else :src="item.url">
                        </div>
                        <h3>
                            {{item.name}}
                        </h3>
                        <span>{{item.size}}</span>
                        <div class="imageDataOver">
                            <h3>{{item.name}}</h3>
                            <div class="imageDataOverOption">
                                <div class="imageDataOverOptionItem">
                                    <svg-icon :icon="'#recently'"></svg-icon>
                                    <span>{{item.created_at}}</span>
                                </div>
                                <div class="imageDataOverOptionItem">
                                    <svg-icon :icon="'#size'"></svg-icon>
                                    <span>{{item.size}}</span>
                                </div>
                            </div>
                            <div class="imageDataOverCats">
                                <svg-icon :icon="'#path'"></svg-icon>
                                <span>مسیر فایل :</span>
                                <h4>{{item.path}}</h4>
                            </div>
                            <div class="imageDataOverCats">
                                <svg-icon :icon="'#url'"></svg-icon>
                                <span>آدرس فایل :</span>
                                <h4>{{item.url}}</h4>
                            </div>
                            <div class="imageDataOverCats">
                                <svg-icon :icon="'#type'"></svg-icon>
                                <span>نوع فایل :</span>
                                <h4>{{item.type}}</h4>
                            </div>
                        </div>
                        <div class="imageDataOver2">
                            <div class="imageDataOver2Items">
                                <div class="imageDataOver2Item">
                                    <i title="ویرایش" @click.stop="btnShow(item.id)">
                                        <svg-icon :icon="'#edit'"></svg-icon>
                                    </i>
                                    <i title="حذف" @click.stop="btnRemoveAll(item.id)">
                                        <svg-icon :icon="'#trash'"></svg-icon>
                                    </i>
                                </div>
                                <div class="imageDataOver2Item">
                                    <div class="checks">
                                        <i v-for="values in value" :key="values.id" v-if="values == item.id">
                                            <svg-icon :icon="'#check'"></svg-icon>
                                        </i>
                                        <i>
                                            <svg-icon :icon="'#uncheck'"></svg-icon>
                                        </i>
                                    </div>
                                    <i title="برش دادن" @click.stop="btnCrop(item.id)">
                                        <svg-icon :icon="'#crop'"></svg-icon>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="paginate">
                    <paginate-panel :link="galleries.links"></paginate-panel>
                </div>
            </div>

            <div class="allGaleryShowImage" v-else>
                <div class="allGaleryShowImagePic"><img :src="fileDetail.url"></div>
                <div class="allGaleryShowImageSubject">
                    <div class="allGaleryShowImageSubjectItems">
                        <div class="allGaleryShowImageSubjectItem">
                            <label>نام :</label>
                            <h4>{{fileDetail.name}}</h4>
                        </div>
                        <div class="allGaleryShowImageSubjectItem">
                            <label>آدرس فایل :</label>
                            <h4>{{fileDetail.url}}</h4>
                        </div>
                        <div class="allGaleryShowImageSubjectItem">
                            <label>مسیر فایل :</label>
                            <h4>{{fileDetail.path}}</h4>
                        </div>
                        <div class="allGaleryShowImageSubjectItem">
                            <label>حجم :</label>
                            <h4>{{fileDetail.size}}</h4>
                        </div>
                    </div>
                    <div class="allGaleryShowImageSubjectChange">
                        <h3>امکانات و ویژگی ها</h3>
                        <div class="allGaleryShowImageSubjectChangeItem">
                            <label>نام :</label>
                            <input type="text" placeholder="نام را وارد کنید" v-model="form.name">
                        </div>
                        <div class="allGaleryShowImageSubjectChangeItem">
                            <label>طول :</label>
                            <input type="text" placeholder="طول را وارد کنید" v-model="form.height">
                        </div>
                        <div class="allGaleryShowImageSubjectChangeItem">
                            <label>عرض :</label>
                            <input type="text" placeholder="عرض را وارد کنید" v-model="form.width">
                        </div>
                        <div class="allGaleryShowImageSubjectChangeItem">
                            <label>کیفیت (0 , 100) :</label>
                            <input type="text" placeholder="کیفیت را وارد کنید" v-model="form.quality">
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="changeFile">ایجاد تغییر</button>
                        <button @click="fileDetail = null">انصراف</button>
                    </div>
                </div>
            </div>
            <div class="allCrop" :style="{'visibility': visible}" v-if="showCrop">
                <div id="crop2"></div>
                <div id="upload-wrapper">
                    <div class="modal">
                        <div class="buttons">
                            <button v-on:click="uploadImage">
                                ایجاد تصویر
                            </button>
                            <button v-on:click="cancelImage">
                                انصراف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import AdminLayout from "../../../components/layout/AdminLayout";
import Croppie from 'croppie';
import SvgIcon from '../../Svg/SvgIcon.vue';
import PaginatePanel from "../PaginatePanel";
export default {
    name: 'GalleryPanel',
    components: {PaginatePanel, AdminLayout , Dropzone , SvgIcon },
    props:['counts' , 'galleries'  , 'imageCrop'],
    metaInfo: {
        title: 'گالری',
    },
    data(){
        return{
            container: 0,
            showOver: -1,
            showFilter: false,
            search: '',
            date: '',
            imageUrl: '',
            fileDetail: null,
            croppie: [],
            visible: 'hidden',
            showCrop: true,
            i:0,
            value: [],
            form:{
                width : '',
                height : '',
                name : '',
                quality : '',
                fileId: ''
            },
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
        }
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , '1');
        },
        returnImage(){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل حذف شده به جای قبل برمیگردد!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله برگردد',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    const url = `/admin/gallery`;
                    this.$inertia.post(url , {
                        search : this.search,
                        date : this.date,
                        container : this.container,
                        return : this.value
                    })
                        .then(response =>{
                            this.value = [];
                            this.container = 0;
                        })
                }
            })
        },
        checkAll(){
            this.i = 0;
            if(this.galleries.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.galleries.data.length; this.i++) {
                    this.value.push(this.galleries.data[this.i].id);
                }
            }
        },
        btnCrop(id){
            this.croppie = [];
            this.value = [];
            this.value.push(id);
            this.visible = 'visible'
            const url = `/admin/gallery`;
            this.$inertia.post(url , {
                search : this.search,
                date : this.date,
                container : this.container,
                crop : this.value[0]
            })
                .then(response=>{
                    this.imageUrl = this.imageCrop;
                    this.setUpCrop();
                })
        },
        cancelImage(){
            this.visible = 'hidden';
            this.showCrop = false;
            const url = `/admin/gallery`;
            this.$inertia.post(url , {
                search : this.search,
                date : this.date,
                container : this.container,
            })
                .then(response=>{
                    this.showCrop = true;
                })
        },
        uploadImage(){
            this.croppie.result({
                type : 'canvas',
                size: 'viewport',
            })
                .then(response =>{
                    const url = `/admin/gallery/crop-image`;
                    this.imageUrl = response;
                    this.$inertia.post(url , {
                        img : this.imageUrl,
                        name : this.value[0],
                    })
                        .then(response=>{
                            this.cancelImage();
                        })
                })
        },
        setUpCrop(){
            let el = document.getElementById('crop2');
            this.croppie = new Croppie(el, {
                viewport: { width: 100, height: 100 },
                boundary: { width: 300, height: 300 },
                showZoomer: true,
                enableResize: true,
                enableOrientation: true,
            });
            this.croppie.bind({
                url: this.imageUrl,
            });
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/gallery`;
            this.$inertia.post(url , {
                search : this.search,
                date : this.date,
                container : this.container,
            })
        },
        changeFile(){
            this.form.fileId = this.fileDetail.id;
            const url = `/admin/gallery`;
            this.$inertia.post(url , this.form)
                .then(response=>{
                    this.fileDetail = null;
                    this.form.width = '';
                    this.form.fileId = '';
                    this.form.height = '';
                    this.form.name = '';
                    this.form.quality = '';
                })
        },
        uploadAllFiles(){
            const url = `/admin/gallery`;
            this.$inertia.post(url , {
                search : this.search,
                date : this.date,
                container : this.container,
            })
        },
        btnSendInfo(id){
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
        btnShow(id){
            const url = "/admin/gallery/show";
            axios.post(url,{
                show : id
            })
                .then(response=>{
                    this.fileDetail = response.data;
                    this.form.name = response.data.name;
                })
        },
        changeShow(number){
            this.value = [];
            this.container = number;
            const url = `/admin/gallery`;
            this.$inertia.post(url,{
                container : this.container,
                search : this.search,
                date : this.date,
            });
        },
        btnRemoveAll(id){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل وارد سطل اشغال میشود و سپس حذف میشود!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    if(id){
                        this.value = [];
                        this.value.push(id);
                    }
                    const url = `/admin/gallery/remove`;
                    this.$inertia.post(url,{'value' : this.value})
                        .then(response =>{
                            this.value = [];
                            this.container = 0;
                        })
                }
            })
        },
    },
    mounted() {
        this.sidebar();
    },
}
</script>

<style scoped>

</style>
