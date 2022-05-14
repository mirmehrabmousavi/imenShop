<template>
    <admin-layout>
        <div class="allChargePanel">
            <div class="allChargePanelTop">
                <h1>رتبه ها</h1>
                <div class="allChargeTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/rank">رتبه ها</inertia-link>
                </div>
            </div>
            <div class="allChargeOptions">
                <div class="allChargeOptionsButtons">
                    <button @click="openCreate">افزودن رتبه</button>
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
                        </div>
                    </transition>
                </div>
            </div>
            <div class="createChargePanel" v-if="show || rankEditData">
                <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                </div>
                <p>توضیحات رتبه و اطلاعات لازم را اینجا وارد کنید</p>
                <div class="allCreateChargeItems">
                    <div class="allCreateChargeItem">
                        <label>نام رتبه :</label>
                        <input type="text" placeholder="نام رتبه را وارد کنید ..." v-model="form.name">
                    </div>
                    <div class="allCreateChargeItem">
                        <label>از امتیاز :</label>
                        <input type="text" placeholder="از امتیاز را وارد کنید ..." v-model="form.from">
                    </div>
                    <div class="allCreateChargeItem">
                        <label>تا امتیاز :</label>
                        <input type="text" placeholder="تا امتیاز را وارد کنید ..." v-model="form.to">
                    </div>
                    <div class="allCreateChargeItem">
                        <label>تخفیف روی محصولات :</label>
                        <input type="text" placeholder="تخفیف را وارد کنید ..." v-model="form.off">
                    </div>
                    <div class="allCreateChargeItem">
                        <label>محصولات :</label>
                        <div class="allTaxes">
                            <div class="taxShow" @click="showTax = !showTax">
                                <h4 v-if="form.postsId.length == 0">محصولات را وارد کنید</h4>
                                <ul v-else>
                                    <li v-for="(item , index) in form.postsName" :key="index">
                                        <i @click.stop="btnClose(index)">
                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                        </i>
                                        <span>{{item}}</span>
                                    </li>
                                </ul>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </div>
                            <ul class="showAllTaxes" v-if="showTax">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li class="searchTax">
                                        <input v-model="searchPost" type="text" placeholder="جستجو ..." @keyup="btnSearchPost">
                                    </li>
                                    <li v-for="(item , index) in allPosts" @click.stop="sendTax(item.id,item.title)" :key="index">
                                        {{item.title}}
                                    </li>
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
            <div class="pages">
                <paginate-panel :link="ranks.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable" v-if="show == false">
                    <all-table :table="ranks.data" :labels="labels" :deletes="1" :shows="0" :edits="1" v-on:sendCheck="getCheck" v-on:sendEdit="openEdit" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="ranks.links"></paginate-panel>
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
    name: "RankPanel",
    props:['ranks','labels','posts','categories','errors','rankEdit'],
    metaInfo: {
        title: 'رتبه ها',
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
            showTax: false,
            date: '',
            search: '',
            searchPost: '',
            i: 0,
            value: [],
            allPosts: this.posts,
            show: false,
            rankEditData: this.rankEdit,
            showPermission: false,
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                name : '',
                from : '',
                off : '',
                to : '',
                rankId : '',
                update : 1,
                postsId: [],
                postsName: [],
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
            this.form.from = '';
            this.form.to = '';
            this.form.off = '';
            this.rankEditData = '';
            this.form.postsName = [];
            this.form.postsId = [];
            this.form.rankId = '';
        },
        btnClose(index){
            this.form.postsId.splice(index,1);
            this.form.postsName.splice(index,1);
        },
        sendTax(id,name){
            this.form.postsName.push(name);
            this.form.postsId.push(id);
        },
        sendCreate(){
            const url  = '/admin/rank';
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (Object.keys(this.errors).length == 0){
                        this.form.name = '';
                        this.form.from = '';
                        this.form.to = '';
                        this.form.postsName = [];
                        this.form.postsId = [];
                        this.form.off = '';
                        this.show = false;
                        this.rankEditData = '';
                    }
                })
        },
        openEdit(id){
            this.form.name = '';
            this.form.from = '';
            this.form.to = '';
            this.form.off = '';
            this.form.postsName = [];
            this.form.postsId = [];
            this.form.rankId = id;
            this.show = !this.show;
            const url = `/admin/rank`;
            this.$inertia.post(url,{
                rankId : id
            })
                .then(response=>{
                    this.rankEditData = this.rankEdit;
                    this.form.name = this.rankEditData.name;
                    this.form.from = this.rankEditData.from;
                    this.form.to = this.rankEditData.to;
                    this.form.off = this.rankEditData.off;
                    this.i = 0;
                    for ( this.i ; this.i < this.categories.length; this.i++) {
                        this.form.postsName.push(this.categories[this.i].title);
                        this.form.postsId.push(this.categories[this.i].id);
                    }
                })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = "/admin/rank";
            this.$inertia.post(url ,{
                'search' : this.search,
                'date' : this.date,
            })
        },
        btnSearchPost(){
            const url = "/admin/search-product";
            axios.post(url ,{
                'search' : this.searchPost,
            })
            .then(res=>{
                this.allPosts = res.data;
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
                    const url = `/admin/rank`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnCancel(){
            this.form.name = '';
            this.form.from = '';
            this.form.off = '';
            this.form.rankId = '';
            this.rankEditData = '';
            this.form.to = '';
            this.form.postsName = [];
            this.form.postsId = [];
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
