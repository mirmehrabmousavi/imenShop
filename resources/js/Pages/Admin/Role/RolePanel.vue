<template>
    <admin-layout>
        <div class="allRole">
            <div class="allRolePanelTop">
                <h1>مقام ها</h1>
                <div class="allRoleTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/role">مقام ها</inertia-link>
                </div>
            </div>
            <div class="allRoleOptions">
                <div class="allRoleOptionsButtons">
                    <button @click="openCreate" v-if="adds">افزودن مقام</button>
                    <button v-if="value.length && deletes" @click="btnRemoveArray('')">حذف</button>
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
                        </div>
                    </transition>
                </div>
            </div>
            <transition name="slide-fade">
                <div class="createRolePanel" v-if="show || roleEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <p>توضیحات مقام و اطلاعات لازم را اینجا وارد کنید</p>
                    <div class="allCreateRoleItems">
                        <div class="allCreateRoleItem">
                            <label>نام :</label>
                            <input type="text" placeholder="نام را وارد کنید ..." v-model="form.name">
                        </div>
                        <div class="allCreateRoleItem">
                            <label>دسترسی</label>
                            <div class="allPermissionItems">
                                <div class="getPermissions" @click="showPermission = !showPermission">
                                    <h4 v-if="form.permission.length == 0">دسترسی را وارد کنید</h4>
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
                                        <li v-for="(item , index) in permissions" @click.stop="getPermission(item.name , index)">{{item.name}}</li>
                                        </VuePerfectScrollbar>
                                    </ul>
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
                <paginate-panel :link="roles.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <all-table :table="roles.data" :labels="labels" :deletes="deletes" :shows="0" :edits="edits" v-on:sendCheck="getCheck" v-on:sendEdit="openEdit" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="roles.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import PaginatePanel from "../PaginatePanel";
import AdminLayout from "../../../components/layout/AdminLayout";
import AllTable from "../Table/AllTable";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "RolePanel",
    props:['roles','labels','permissions','adds','deletes','edits','errors','roleEdit','adds','edits','deletes'],
    metaInfo: {
      title: 'مقام ها',
    },
    components:{
        SvgIcon,
        AllTable,
        AdminLayout,
        PaginatePanel,
        VuePerfectScrollbar,
    },
    data(){
        return{
            showFilter: false,
            date: '',
            search: '',
            value: [],
            show: false,
            roleEditData: this.roleEdit,
            showPermission: false,
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                permission : [],
                name : '',
                roleId: ''
            },
        }
    },
    methods:{
        getCheck(id){
            this.value = id;
        },
        openCreate(){
            this.show = true;
            this.form.name = '';
            this.roleEditData = '';
            this.form.roleId = '';
            this.form.permission = [];
        },
        getPermission(item){
            this.i = 0;
            for ( this.i ; this.i <  this.form.permission.length; this.i++) {
                if (this.form.permission[this.i] == item){
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.form.permission.push(item);
            }
            this.i = 0;
        },
        removePermission(index){
            this.form.permission.splice(index , 1);
        },
        sendCreate(){
            const url  = '/admin/role';
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (!this.errors.name){
                        this.form.name = '';
                        this.form.permission = [];
                        this.show = false;
                        this.roleEditData = '';
                    }
                })
        },
        openEdit(id){
            this.form.name = '';
            this.form.permission = [];
            this.form.roleId = id;
            this.show = !this.show;
            const url = `/admin/role`;
            this.$inertia.post(url,{
                roleId : id
            })
                .then(response=>{
                    this.roleEditData = this.roleEdit;
                    this.form.name = this.roleEditData.name;
                    this.i = 0;
                    for ( this.i ; this.i <  this.roleEditData.permissions.length; this.i++) {
                        this.form.permission.push(this.roleEditData.permissions[this.i].name);
                    }
                    this.i = 0;
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = "/admin/role";
            this.$inertia.post(url ,{
                'search' : this.search,
                'date' : this.date,
            })
        },
        btnRemoveArray(id){
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
                    const url = `/admin/role`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnCancel(){
            this.form.permission = [];
            this.form.name = '';
            this.form.roleId = '';
            this.roleEditData = '';
            this.show = false;
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '3');
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style>

</style>
