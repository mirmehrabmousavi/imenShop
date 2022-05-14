<template>
    <admin-layout>
        <div class="allChargePanel">
            <div class="allChargePanelTop">
                <h1>شارژ ها</h1>
                <div class="allChargeTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/charge">شارژ ها</inertia-link>
                </div>
            </div>
            <div class="allChargeOptions">
                <div class="allChargeOptionsButtons">
                    <button @click="openCreate" v-if="adds">افزودن شارژ</button>
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
                <div class="createChargePanel" v-if="show || chargeEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                            <span>
                                {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                    </div>
                    <p>توضیحات شارژ و اطلاعات لازم را اینجا وارد کنید</p>
                    <div class="allCreateChargeItems">
                        <div class="allCreateChargeItem">
                            <label>مبلغ :</label>
                            <input type="text" placeholder="مبلغ را وارد کنید ..." v-model="form.price">
                        </div>
                        <div class="allCreateChargeItem">
                            <label>نوع :</label>
                            <select v-model="form.type">
                                <option value="0">شارژ حساب</option>
                                <option value="1">کاهش حساب</option>
                            </select>
                        </div>
                        <div class="allCreateChargeItem">
                            <label>وضعیت پرداخت :</label>
                            <select v-model="form.status">
                                <option value="100">موفق</option>
                                <option value="0">ناموفق</option>
                            </select>
                        </div>
                        <div class="allCreateChargeItem">
                            <label>کاربر :</label>
                            <select v-model="form.user">
                                <option v-for="item in users" :value="item.id">{{ item.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="sendCreate">ارسال اطلاعات</button>
                        <button @click="btnCancel">انصراف</button>
                    </div>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="charges.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <all-table :table="charges.data" :labels="labels" :deletes="deletes" :shows="0" :edits="edits" v-on:sendCheck="getCheck" v-on:sendEdit="openEdit" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="charges.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import PaginatePanel from "../PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import AllTable from "../Table/AllTable";
import AdminLayout from "../../../components/layout/AdminLayout";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "ChargePanel",
    props:['charges','users','labels','adds','deletes','edits','errors','chargeEdit','adds','edits','deletes'],
    metaInfo: {
        title: 'شارژ ها',
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
            chargeEditData: this.chargeEdit,
            showPermission: false,
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                price : '',
                type : '',
                status : '',
                user : '',
                chargeId: ''
            },
        }
    },
    methods:{
        getCheck(id){
            this.value = id;
        },
        openCreate(){
            this.show = true;
            this.form.price = '';
            this.form.type = '';
            this.form.status = '';
            this.form.user = '';
            this.chargeEditData = '';
            this.form.chargeId = '';
        },
        sendCreate(){
            const url  = '/admin/charge';
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (!this.errors.price){
                        this.form.price = '';
                        this.form.type = '';
                        this.form.status = '';
                        this.form.user = '';
                        this.show = false;
                        this.chargeEditData = '';
                    }
                })
        },
        openEdit(id){
            this.form.price = '';
            this.form.type = '';
            this.form.status = '';
            this.form.user = '';
            this.form.chargeId = id;
            this.show = !this.show;
            const url = `/admin/charge`;
            this.$inertia.post(url,{
                chargeId : id
            })
                .then(response=>{
                    this.chargeEditData = this.chargeEdit;
                    this.form.price = this.chargeEditData.price;
                    this.form.type = this.chargeEditData.type;
                    this.form.status = this.chargeEditData.status;
                    this.form.user = this.chargeEditData.user.id;
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = "/admin/charge";
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
                    const url = `/admin/charge`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnCancel(){
            this.form.price = '';
            this.form.type = '';
            this.form.status = '';
            this.form.user = '';
            this.form.chargeId = '';
            this.chargeEditData = '';
            this.show = false;
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '7');
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
