<template>
    <admin-layout>
        <div class="allUserPanel">
            <div class="allUserPanelTop">
                <h1>کاربران</h1>
                <div class="allUserTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/user">کاربران</inertia-link>
                </div>
            </div>
            <div class="allUserOptions">
                <div class="allUserOptionsButtons">
                    <button @click="openCreate">افزودن کاربر</button>
                    <button v-if="value.length" @click="btnRemoveArray('')">حذف</button>
                </div>
                <div class="filterItems">
                    <div class="imageContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
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
                            <div class="filterContentItem">
                                <label>فیلتر مقام</label>
                                <div class="allCategoryPanel" @click="showAllRole = !showAllRole">
                                    <div class="categoryShow">
                                        <h4 v-if="sortRole == ''">همه</h4>
                                        <h4 v-else>{{sortRole}}</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showAllRole">
                                        <VuePerfectScrollbar class="scroll-area" v-once :settings="settings">
                                            <li @click="sendSortRole('')">همه</li>
                                            <li v-for="item in roles" @click="sendSortRole(item.name)">{{item.name}}</li>
                                        </VuePerfectScrollbar>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <transition name="slide-fade">
                <div class="createUserPanel" v-if="show || userEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <p v-if="form.userId">رمز عبور در صورت تغییر وارد شود</p>
                    <p v-else>توضیحات کاربر و اطلاعات را اینجا وارد کنید</p>
                    <div class="allCreateUserItems">
                        <div class="allCreateUserItem">
                            <label>نام :</label>
                            <input type="text" placeholder="نام را وارد کنید ..." v-model="form.name">
                        </div>
                        <div class="allCreateUserItem">
                            <label>شماره تماس :</label>
                            <input type="text" placeholder="شماره تماس را وارد کنید ..." v-model="form.number">
                        </div>
                        <div class="allCreateUserItem">
                            <label>ایمیل :</label>
                            <input type="email" placeholder="ایمیل را وارد کنید ..." v-model="form.email">
                        </div>
                        <div class="allCreateUserItem">
                            <label>رمز عبور :</label>
                            <input type="password" placeholder="رمز عبور را وارد کنید ..." v-model="form.password">
                        </div>
                        <div class="allCreateUserItem">
                            <label>مقام :</label>
                            <div class="allCategoryPanel" @click="showRole = !showRole">
                                <div class="categoryShow">
                                    <h4 v-if="form.role == ''">مقام را وارد کنید ...</h4>
                                    <h4 v-else>{{form.role}}</h4>
                                </div>
                                <ul v-if="showRole">
                                    <VuePerfectScrollbar class="scroll-area" v-once :settings="settings">
                                        <li v-for="item in roles" @click="sendRole(item.name)">{{item.name}}</li>
                                    </VuePerfectScrollbar>
                                </ul>
                            </div>
                        </div>
                        <div class="allCreateUserItem">
                            <label>دسترسی خاص :</label>
                            <div class="allPermissionItems">
                                <div class="getPermissions" @click="showPermission = !showPermission">
                                    <h4 v-if="form.permission.length == 0">دسترسی را وارد کنید ...</h4>
                                    <ul>
                                        <li v-for="(item , index) in form.permission">
                                            <i @click.stop="removePermission(index)">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                            <span>{{item}}</span>
                                        </li>
                                    </ul>
                                </div>
                                <ul class="getAllPermissions" v-if="showPermission">
                                    <VuePerfectScrollbar class="scroll-area" v-once :settings="settings">
                                        <li v-for="(item , index) in permissions" @click.stop="sendPermission(item.name , index)">{{item.name}}</li>
                                    </VuePerfectScrollbar>
                                </ul>
                            </div>
                        </div>
                        <div class="sendGallery">
                            <show-image v-on:sendClose="getClose" v-if="showImages" v-on:sendUrl="getUrl"></show-image>
                            <div class="getImageItem" @click="showImages = !showImages">
                                <div class="getImagePic" v-if="form.image">
                                    <img :src="form.image">
                                </div>
                                <span v-else>تصویر شاخص خود را وارد کنید</span>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="sendCreate">ارسال اطلاعات</button>
                        <button @click="btnCancel">انصراف</button>
                    </div>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="allUser.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <div class="postItem" v-for="item in allUser.data" @click="getCheck(item.id)">
                        <div class="postTop">
                            <div class="postPic">
                                <img v-if="item.profile" :src="item.image">
                                <img v-else src="/img/user.png">
                            </div>
                            <div class="postTitle">
                                <ul>
                                    <li>
                                        <span>نام کاربری :</span>
                                        <span>{{item.name}}</span>
                                    </li>
                                    <li>
                                        <span>شماره تماس :</span>
                                        <span v-if="item.number">{{item.number}}</span>
                                        <span v-else>نامشخص</span>
                                    </li>
                                    <li>
                                        <span>ایمیل :</span>
                                        <span v-if="item.email">{{item.email}}</span>
                                        <span v-else>نامشخص</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="postOptions">
                                <i title="مشاهده کاربر" @click="openShow(item.id)">
                                    <svg-icon :icon="'#eye'"></svg-icon>
                                </i>
                                <i @click="openEdit(item.id)" v-if="edits" title="ویرایش کاربر">
                                    <svg-icon :icon="'#edit'"></svg-icon>
                                </i>
                                <i v-if="deletes" title="حذف کاربر" @click="btnRemoveArray(item.id)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </div>
                        </div>
                        <div class="postBot">
                            <ul>
                                <li>
                                    <span>نوع کاربر :</span>
                                    <span v-if="item.admin == 1">مدیر کل</span>
                                    <span v-if="item.roles.length">{{ item.roles[0].name }}</span>
                                    <span v-for="value in item.permissions" v-if="item.permissions.length && value.name == 'فروشنده'">
                                        <span>فروشنده</span>
                                    </span>
                                    <span>کاربر ساده</span>
                                </li>
                                <li>
                                    <span>زمان ثبت نام :</span>
                                    <span>{{ item.created_at }}</span>
                                </li>
                                <li>
                                    <span>آخرین بازدید :</span>
                                    <span>{{item.activity}}</span>
                                </li>
                                <li>
                                    <span>تعداد علاقه مندی :</span>
                                    <span>{{item.like_count}}</span>
                                </li>
                                <li>
                                    <span>تعداد نشانه ها :</span>
                                    <span>{{item.bookmark_count}}</span>
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
            <div class="pages">
                <paginate-panel :link="allUser.links"></paginate-panel>
            </div>
            <div class="allShowUserPanel" v-if="getUserInfo.length">
                <div class="showUserPanel">
                    <b @click="getUserInfo = []">
                        <svg-icon :icon="'#cancel'"></svg-icon>
                    </b>
                    <div class="showUserPanelUser">
                        <div class="showUserPanelUserPic">
                            <img v-if="getUserInfo[0].profile == null" src="/img/user.png">
                            <img v-else :src="getUserInfo[0].profile">
                        </div>
                        <div class="showUserPanelUserSubject">
                            <div class="showUserPanelUserSubjectItem">
                                <label>نام :</label>
                                <h4>{{getUserInfo[0].name}}</h4>
                            </div>
                            <div class="showUserPanelUserSubjectItem">
                                <label>ایمیل :</label>
                                <h4>{{getUserInfo[0].email}}</h4>
                            </div>
                            <div class="showUserPanelUserSubjectItem">
                                <label>آخرین بازدید :</label>
                                <h4>{{getUserInfo[0].activity}}</h4>
                            </div>
                            <div class="showUserPanelUserSubjectItem">
                                <label>زمان ثبت نام :</label>
                                <h4>{{getUserInfo[0].created_at}}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="showUserPanelItem" v-if="getUserInfo[2].length">
                        <label>آخرین علاقه مندی ها</label>
                        <div class="allUserIndexInfoLikeItem">
                            <ul>
                                <li v-for="(item , index) in getUserInfo[2]" :key="index">
                                    <inertia-link :href="'/product/' + item.slug">
                                        <div class="userItemPic">
                                            <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                        </div>
                                        <div class="userItemSubject">
                                            <div class="userItemSubjectTitle">{{item.title}}</div>
                                            <div class="postPriceItem">
                                                <div class="offPrice" v-if="item.off != null">
                                                    <s>{{item.price|NumFormat}} تومان</s>
                                                </div>
                                                <h3 v-if="item.off != null">{{item.offPrice|NumFormat}} تومان</h3>
                                                <h3 v-else>{{item.price|NumFormat}} تومان</h3>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="showUserPanelItem" v-if="getUserInfo[3].length">
                        <label>آخرین نشانه ها</label>
                        <div class="allUserIndexInfoLikeItem">
                            <ul>
                                <li v-for="(item , index) in getUserInfo[3]" :key="index">
                                    <inertia-link :href="'/product/' + item.slug">
                                        <div class="userItemPic">
                                            <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                        </div>
                                        <div class="userItemSubject">
                                            <div class="userItemSubjectTitle">{{item.title}}</div>
                                            <div class="postPriceItem">
                                                <div class="offPrice" v-if="item.off != null">
                                                    <s>{{item.price|NumFormat}} تومان</s>
                                                </div>
                                                <h3 v-if="item.off != null">{{item.offPrice|NumFormat}} تومان</h3>
                                                <h3 v-else>{{item.price|NumFormat}} تومان</h3>
                                            </div>
                                        </div>
                                    </inertia-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="allUserIndexComment" v-if="getUserInfo[4].length">
                        <label>آخرین دیدگاه ها و نظرات</label>
                        <div class="allUserIndexCommentItems">
                            <div class="allUserIndexCommentItem" v-for="(item , index) in getUserInfo[4]" :key="index">
                                <inertia-link :href="'/product/' + item.post.slug">
                                    <div class="allUserIndexCommentItemPic">
                                        <img :src="JSON.parse(item.post.image)[0]" :alt="item.post.title">
                                    </div>
                                    <div class="allUserIndexCommentItemSubject">
                                        <div class="allUserIndexCommentItemSubjectTitle">
                                            {{item.title}}
                                        </div>
                                        <div class="allUserIndexCommentItemSubjectStatus">
                                            <span v-if="item.status == 0">در مورد خرید این محصول مطمئن نیستم</span>
                                            <span v-if="item.status == 1">خرید این محصول را توصیه نمی‌کنم</span>
                                            <span v-if="item.status == 2">خرید این محصول را توصیه می‌کنم</span>
                                        </div>
                                        <div class="allUserIndexCommentItemSubjectBody">
                                            <p>{{item.body}}</p>
                                        </div>
                                        <div class="allUserIndexCommentItemSubjectAccept">
                                            <span v-if="item.approved == 0">در انتظار تایید</span>
                                            <span v-if="item.approved == 1" class="active">تایید شده</span>
                                        </div>
                                    </div>
                                </inertia-link>
                            </div>
                        </div>
                    </div>
                    <button @click="getUserInfo = []">انصراف</button>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import ShowImage from "../ShowImage";
import AllTable from "../Table/AllTable";
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import PaginatePanel from '../PaginatePanel.vue';
export default {
    name: "UserPanel",
    props:['allUser','errors','labels','deletes','edits','shows','roles','permissions','userEdit','userInfo','adds','edits','deletes'],
    components:{
        SvgIcon,
        VuePerfectScrollbar,
        ShowImage,
        AdminLayout,
        PaginatePanel,
        AllTable
    },
    metaInfo: {
        title: 'کاربران'
    },
    data(){
        return{
            settings: {
                maxScrollbarLength: 60
            },
            showFilter: false,
            showImages: false,
            showAllRole: false,
            show: false,
            date:'',
            search: '',
            value: [],
            sortRole: '',
            showRole: false,
            showPermission: false,
            userEditData: this.userEdit,
            i: 0,
            getUserInfo: this.userInfo,
            form:{
                name : '',
                image : '',
                userId: '',
                email : '',
                password : '',
                number : '',
                permission : [],
                role : '',
            },
        }
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , '3');
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
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/user`;
            this.$inertia.post(url , {
                date : this.date,
                sortRole : this.sortRole,
                search : this.search
            })
        },
        openShow(id){
            const url = `/admin/user`;
            this.$inertia.post(url,{
                show: id
            })
                .then(response =>{
                    this.getUserInfo = this.userInfo;
                    console.log(this.getUserInfo);
                })
        },
        sendSortRole(item){
            this.sortRole = item;
            const url = `/admin/user`;
            this.$inertia.post(url , {
                search : this.search,
                sortRole : this.sortRole,
                date : this.date,
            })
        },
        getClose(){
            this.showImages = false;
        },
        getUrl(url){
            this.form.image = url;
        },
        openCreate(){
            this.show = true;
            this.form.name = '';
            this.form.userId = '';
            this.form.image = '';
            this.form.number = '';
            this.form.email = '';
            this.form.password = '';
            this.form.role = '';
            this.userEditData = '';
            this.form.permission = [];
        },
        sendCreate(){
            const url = `/admin/user`;
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (this.errors.name || this.errors.email || this.errors.password){
                    }else{
                        this.form.name = '';
                        this.form.image = '';
                        this.form.number = '';
                        this.form.email = '';
                        this.form.password = '';
                        this.show = false;
                        this.userEditData = '';
                    }
                })
        },
        openEdit(id){
            this.form.name = '';
            this.form.image = '';
            this.form.number = '';
            this.form.email = '';
            this.form.password = '';
            this.form.role = '';
            this.form.permission = [];
            this.form.userId = id;
            this.show = !this.show;
            const url = `/admin/user`;
            this.$inertia.post(url,{
                userId : id
            })
                .then(response=>{
                    this.userEditData = this.userEdit;
                    this.form.name = this.userEditData.name;
                    if (this.userEditData.profile == null){
                        this.form.image = this.userEditData.profile_photo_url;
                    }else{
                        this.form.image = this.userEditData.profile;
                    }
                    this.form.email = this.userEditData.email;
                    this.form.number = this.userEditData.number;
                    this.form.password = this.userEditData.password;
                    if (this.userEditData.roles.length){
                        this.form.role = this.userEditData.roles[0].name;
                    }
                    for ( this.i ; this.i <  this.userEditData.permissions.length; this.i++) {
                        this.form.permission.push(this.userEditData.permissions[this.i].name);
                    }
                })
        },
        removePermission(index){
            this.form.permission.splice(index , 1);
        },
        sendRole(name){
            this.form.role = name;
        },
        sendPermission(name){
            this.i = 0;
            for ( this.i ; this.i <  this.form.permission.length; this.i++) {
                if (this.form.permission[this.i] == name){
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.form.permission.push(name);
            }
            this.i = 0;
        },
        btnCancel(){
            this.show = false,
                this.userEditData = '',
                this.userId = ''
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
                    const url = `/admin/user`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
