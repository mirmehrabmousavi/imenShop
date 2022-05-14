<template>
    <admin-layout>
        <div class="allTaxonami">
            <div class="allTaxonamiPanelTop">
                <h1>{{name}}</h1>
                <div class="allTaxonamiTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link :href="'/admin/'+ routeAddress">{{name}}</inertia-link>
                </div>
            </div>
            <div class="allTaxonamiOptions">
                <div class="allTaxonamiOptionsButtons">
                    <button @click="openCreate" v-if="adds == 1">افزودن {{ name }}</button>
                    <button v-if="value.length && deletes == 1" @click="btnRemoveArray('')">حذف</button>
                </div>
                <div class="filterItems">
                    <div class="ContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
                        <svg-icon :icon="'#filter'"></svg-icon>
                        فیلتر اطلاعات
                    </div>
                    <transition name="bounce">
                        <div class="filterContent" v-if="showFilter">
                            <div class="filterContentItem">
                                <label>فیلتر عنوان یا آیدی</label>
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
                        </div>
                    </transition>
                </div>
            </div>
            <div class="pages">
                <paginate-panel :link="taxes.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="createTaxonamiPanel" v-if="show || taxEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <p>توضیحات {{name}} و اطلاعات را اینجا وارد کنید</p>
                    <div class="allCreateTaxonamiItems">
                        <div class="allCreateTaxonamiItem">
                            <label v-if="name != 'کد تخفیف'">{{name}} :</label>
                            <label v-else>عنوان :</label>
                            <input type="text" placeholder="نام را وارد کنید ..." v-model="form.name">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name != 'کد تخفیف'">
                            <label>نام انگلیسی :</label>
                            <input type="text" placeholder="نام انگلیسی را وارد کنید ..." v-model="form.nameEn">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'زمان'">
                            <label>بازه زمانی ارسال (روز) :</label>
                            <select v-model="form.day">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                            </select>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'زمان'">
                            <label>از چه ساعت :</label>
                            <select v-model="form.from">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                            </select>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'زمان'">
                            <label>تا چه ساعت :</label>
                            <select v-model="form.to">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                            </select>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'حامل'">
                            <label>قیمت(تومان) :</label>
                            <input type="text" placeholder="قیمت را وارد کنید ..." v-model="form.price">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'حامل'">
                            <label>بیشتر از قیمت حامل رایگان است :</label>
                            <input type="text" placeholder="قیمت را وارد کنید ..." v-model="form.limit">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name != 'حامل' && name != 'کد تخفیف' && name != 'زمان'">
                            <label>پیوند :</label>
                            <input type="text" placeholder="پیوند را وارد کنید ..." v-model="form.slug">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>درصد تخفیف (1-100) :</label>
                            <input type="text" placeholder="درصد تخفیف را وارد کنید ..." v-model="form.percent">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>کد :</label>
                            <input type="text" placeholder="کد را وارد کنید ..." v-model="form.code">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>تعداد استفاده :</label>
                            <input type="text" placeholder="تعداد استفاده را وارد کنید ..." v-model="form.count">
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'برگه'">
                            <label>موقعیت جغرافیایی :</label>
                            <div class="inputs">
                                <input type="number" placeholder="طول جغرافیایی را وارد کنید ..." v-model="form.geo[0]">
                                <input type="number" placeholder="عرض جغرافیایی را وارد کنید ..." v-model="form.geo[1]">
                            </div>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>وضعیت :</label>
                            <div class="allCategoryPanel" @click="btnShowStatus(1)">
                                <div class="categoryShow">
                                    <h4 v-if="form.status == 0">پیشنویس</h4>
                                    <h4 v-if="form.status == 1">منتشر شده</h4>
                                </div>
                                <ul v-if="showStatus == 1">
                                    <li @click.stop="sendStatus(0)">پیشنویس</li>
                                    <li @click.stop="sendStatus(1)">منتشر شده</li>
                                </ul>
                            </div>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>تاریخ انقضا :</label>
                            <div class="allCreateTaxonamiItemTimer">
                                <date-picker
                                    v-model="form.day"
                                    type="datetime"
                                    format="YYYY-MM-DD"
                                    display-format="jYYYY-jMM-jDD"
                                    placeholder="تاریخ را وارد کنید"
                                    :timezone="true"
                                />
                                <i @click.stop="form.day = ''" v-if="form.day">
                                    <svg-icon :icon="'#cancel'"></svg-icon>
                                </i>
                            </div>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'کد تخفیف'">
                            <label>محصول :</label>
                            <div class="allCategoryPanel" @click.stop="btnShowStatus(2)">
                                <div class="categoryShow">
                                    <h4 v-if="form.postData == ''">محصول مورد نظر را انتخاب کنید</h4>
                                    <h4 v-else>{{form.postData}}</h4>
                                </div>
                                <ul v-if="showStatus == 2">
                                    <li v-for="(item,index) in posts" :key="index" @click.stop="sendPost(item.title , item.id)">{{item.title}}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'دسته بندی'">
                            <label>نوع دسته بندی :</label>
                            <select v-model="form.status">
                                <option value="0">محصول</option>
                                <option value="1">خبر</option>
                            </select>
                        </div>
                        <div class="checkTax" v-if="name == 'برگه'">
                            <label for="s1d" class="allCreatePostDetailItemData">
                                نمایش فرم تماس :
                                <input id="s1d" type="checkbox" class="switch" v-model="form.form">
                            </label>
                        </div>
                        <div class="allCreateTaxonamiItem" v-if="name == 'دسته بندی'">
                            <label>زیرمجموعه :</label>
                            <post-taxonami :taxes="taxesSend" :taxRoute="'دسته بندی'" :tax="form.allCategory"  v-on:sendTax="getCat"></post-taxonami>
                        </div>
                        <div class="sendGallery" v-if="name == 'برند' || name == 'دسته بندی'">
                            <show-image v-on:sendClose="getClose" v-if="showImage" v-on:sendUrl="getUrl"></show-image>
                            <div class="getImageItem" @click="showImage = !showImage">
                                <div class="getImagePic" v-if="form.image">
                                    <img :src="form.image">
                                </div>
                                <span v-else>تصویر شاخص خود را وارد کنید</span>
                            </div>
                        </div>
                        <div class="allCreateTaxonamiItemBody" v-if="name == 'برگه'">
                            <label>توضیحات :</label>
                            <CKEditor :editor="editor" @ready="onReady" :config="editorConfig" v-model="form.body"></CKEditor>
                        </div>
                        <div class="allCreateTaxonamiItemBody" v-if="name == 'برگه'">
                            <label>توضیحات انگلیسی :</label>
                            <CKEditor :editor="editor" @ready="onReady" :config="editorConfig" v-model="form.bodyEn"></CKEditor>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="sendCreate">ارسال اطلاعات</button>
                        <button @click="btnCancel">انصراف</button>
                    </div>
                </div>
            </transition>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <all-table :table="taxes.data" :labels="labels" :deletes="deletes" :shows="0" :edits="edits" v-on:sendCheck="getCheck" v-on:sendEdit="openEdit" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="taxes.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import ClassicEditor from '@ckeditor/ckeditor5-build-decoupled-document';
import CKEditor from '@ckeditor/ckeditor5-vue2'
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import PaginatePanel from '../PaginatePanel.vue';
import AllTable from '../Table/AllTable.vue';
import PostTaxonami from '../Post/PostTaxonami.vue';
import ShowImage from "../ShowImage";
import AdminLayout from "../../../components/layout/AdminLayout";

export default {
    name: "AllTaxonami",
    props: ['name', 'routeAddress', 'sidebar','posts', 'errors', 'taxesSend', 'categories', 'taxEdit', 'labels', 'adds', 'shows', 'taxes', 'deletes', 'edits'],
    metaInfo: {
        title: 'تاکسونامی',
    },
    components: {
        AdminLayout,
        ShowImage,
        SvgIcon,
        VuePerfectScrollbar,
        CKEditor: CKEditor.component,
        PaginatePanel,
        AllTable,
        PostTaxonami
    },
    data() {
        return {
            showFilter: false,
            showStatus: false,
            show: false,
            showCats: false,
            showImage: false,
            value: [],
            taxEditData: '',
            date: '',
            search: '',
            form: {
                name: '',
                price: '',
                form: 0,
                status: 1,
                nameEn: '',
                postData: '',
                image: '',
                to: 1,
                from: 1,
                slug: '',
                percent: '',
                code: '',
                count: '',
                day: '',
                body: '',
                bodyEn: '',
                allCategory: '',
                postId: '',
                taxId: '',
                geo: ['', ''],
            },
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            editor: ClassicEditor,
            editorConfig: {
                extraPlugins: [MyCustomUploadAdapterPlugin],
            },
        }
    },
    methods: {
        sendPost(title , id){
            this.form.postData = title;
            this.form.postId = id;
            this.showStatus = 0;
        },
        btnShowStatus(index){
            if (this.showStatus == index){
                this.showStatus = 0;
            }else{
                this.showStatus = index;
            }
        },
        sendStatus(num){
            this.form.status = num;
            this.showStatus = 0;
        },
        getCat(cat) {
            this.form.allCategory = cat;
        },
        getClose() {
            this.showImage = false;
        },
        getUrl(url) {
            this.form.image = url;
        },
        openEdit(id) {
            this.form.name = '';
            this.form.to = 1;
            this.form.from = 1;
            this.form.status = 0;
            this.form.price = '';
            this.form.limit = '';
            this.form.nameEn = '';
            this.form.slug = '';
            this.form.body = '';
            this.form.bodyEn = '';
            this.form.image = '';
            this.form.code = '';
            this.form.percent = '';
            this.form.count = '';
            this.form.day = '';
            this.form.form = 0;
            this.form.postId = '';
            this.form.postData = '';
            this.form.taxId = id;
            this.form.geo = ['',''];
            this.form.allCategory = [];
            this.show = !this.show;
            const url = `/admin/${this.routeAddress}`;
            this.$inertia.post(url, {
                taxId: id
            })
                .then(response => {
                    this.taxEditData = this.taxEdit;
                    if (this.name == 'دسته بندی') {
                        this.$eventHub.emit('getCats', this.categories);
                    }
                    this.form.name = this.taxEditData.name;
                    this.form.price = this.taxEditData.price;
                    this.form.nameEn = this.taxEditData.nameEn;
                    if (this.name == 'برگه' || this.name == 'کد تخفیف') {
                        this.form.name = this.taxEditData.title;
                        this.form.nameEn = this.taxEditData.titleEn;
                        this.form.form = this.taxEditData.form;
                        this.form.percent = this.taxEditData.percent;
                        if (this.taxEditData.map){
                            this.form.geo= [JSON.parse(this.taxEditData.map).lng , JSON.parse(this.taxEditData.map).lat];
                        }
                        if(this.taxEditData.post){
                            this.form.postId = this.taxEditData.post.id;
                            this.form.postData = this.taxEditData.post.title;
                        }
                    }
                    this.form.slug = this.taxEditData.slug;
                    this.form.body = this.taxEditData.body;
                    this.form.bodyEn = this.taxEditData.bodyEn;
                    this.form.code = this.taxEditData.code;
                    this.form.image = this.taxEditData.image;
                    this.form.limit = this.taxEditData.limit;
                    this.form.to = this.taxEditData.to;
                    this.form.from = this.taxEditData.from;
                    this.form.day = this.taxEditData.day;
                    this.form.count = this.taxEditData.count;
                    if (this.name == 'دسته بندی') {
                        this.form.status = this.taxEditData.type;
                    }
                })
        },
        getCheck(id) {
            this.value = id;
        },
        sendCreate() {
            const url = `/admin/${this.routeAddress}`;
            this.$inertia.post(url, this.form)
                .then(response => {
                    if (Object.keys(this.errors).length == 0) {
                        this.form.form = 0;
                        this.form.name = '';
                        this.form.to = 1;
                        this.form.from = 1;
                        this.form.status = 0;
                        this.form.day = '';
                        this.form.price = '';
                        this.form.limit = '';
                        this.form.nameEn = '';
                        this.form.geo = ['',''];
                        this.form.slug = '';
                        this.form.body = '';
                        this.form.postId = '';
                        this.form.postData = '';
                        this.form.bodyEn = '';
                        this.form.image = '';
                        this.form.allCategory = [];
                        this.form.code = '';
                        this.form.count = '';
                        this.form.percent = '';
                        this.show = false;
                        this.$eventHub.emit('deleteTax');
                        this.taxEditData = '';
                    }
                })
        },
        btnCancel() {
            this.form.name = '';
            this.form.form = 0;
            this.form.to = 1;
            this.form.from = 1;
            this.form.status = 0;
            this.form.day = '';
            this.form.code = '';
            this.form.geo = ['',''];
            this.form.count = '';
            this.form.percent = '';
            this.form.price = '';
            this.form.limit = '';
            this.form.postId = '';
            this.form.postData = '';
            this.form.nameEn = '';
            this.form.slug = '';
            this.form.image = '';
            this.form.bodyEn = '';
            this.form.body = '';
            this.taxEditData = '';
            this.form.code = '';
            this.show = false;
        },
        btnRemoveArray(id) {
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
                    if (id) {
                        this.value = [id]
                    }
                    const url = `/admin/${this.routeAddress}`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', this.sidebar);
        },
        openCreate() {
            this.show = true;
            this.form.name = '';
            this.form.to = 1;
            this.form.status = 0;
            this.form.from = 1;
            this.form.day = '';
            this.form.code = '';
            this.form.count = '';
            this.form.percent = '';
            this.form.postId = '';
            this.form.postData = '';
            this.form.price = '';
            this.form.limit = '';
            this.form.geo = ['',''];
            this.form.nameEn = '';
            this.form.slug = '';
            this.form.code = '';
            this.form.image = '';
            this.form.body = '';
            this.form.form = 0;
            this.form.bodyEn = '';
            this.taxEditData = '';
            this.$eventHub.emit('deleteTax');
            this.form.taxId = '';
        },
        btnSearch(number) {
            if (number == 1) {
                this.date = '';
            }
            const url = `/admin/${this.routeAddress}`;
            this.$inertia.post(url, {
                date: this.date,
                search: this.search
            })
        },
        onReady(editor) {
            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        },
    },
    mounted() {
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
                        resolve({
                            default: response.data.url
                        });
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

function MyCustomUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        // Configure the URL to the upload script in your back-end here!
        return new MediaUploadAdapter(loader);
    };
}
</script>

<style>

</style>
