<template>
    <admin-layout>
        <div class="allChargePanel">
            <div class="allChargePanelTop">
                <h1>شارژ ها</h1>
                <div class="allChargeTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/score">شارژ ها</inertia-link>
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
                <div class="createChargePanel" v-if="show || scoreEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                            <span>
                                {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                    </div>
                    <p>توضیحات شارژ و اطلاعات لازم را اینجا وارد کنید</p>
                    <div class="allCreateChargeItems">
                        <div class="allCreateChargeItem">
                            <label>امتیاز :</label>
                            <input type="text" placeholder="امتیاز را وارد کنید ..." v-model="form.price">
                        </div>
                        <div class="allCreateChargeItem">
                            <label>نوع :</label>
                            <select v-model="form.type">
                                <option value="0">افزایش امتیاز</option>
                                <option value="1">کاهش امتیاز</option>
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
                <paginate-panel :link="scores.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <all-table :table="scores.data" :labels="labels" :deletes="deletes" :shows="0" :edits="edits" v-on:sendCheck="getCheck" v-on:sendEdit="openEdit" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="scores.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import AllTable from "../Table/AllTable";
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "ScorePanel",
    props:['scores','users','labels','adds','deletes','edits','errors','scoreEdit','adds','edits','deletes'],
    metaInfo: {
        title: 'امتیاز ها',
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
            scoreEditData: this.scoreEdit,
            showPermission: false,
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                price : '',
                type : '',
                user : '',
                scoreId: ''
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
            this.form.user = '';
            this.scoreEditData = '';
            this.form.scoreId = '';
        },
        sendCreate(){
            const url  = '/admin/score';
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (!this.errors.price){
                        this.form.price = '';
                        this.form.type = '';
                        this.form.user = '';
                        this.show = false;
                        this.scoreEditData = '';
                    }
                })
        },
        openEdit(id){
            this.form.price = '';
            this.form.type = '';
            this.form.user = '';
            this.form.scoreId = id;
            this.show = !this.show;
            const url = `/admin/score`;
            this.$inertia.post(url,{
                scoreId : id
            })
                .then(response=>{
                    this.scoreEditData = this.scoreEdit;
                    this.form.price = this.scoreEditData.name;
                    this.form.type = this.scoreEditData.type;
                    this.form.user = this.scoreEditData.user[0].id;
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = "/admin/score";
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
                    const url = `/admin/score`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnCancel(){
            this.form.price = '';
            this.form.type = '';
            this.form.user = '';
            this.form.scoreId = '';
            this.scoreEditData = '';
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
