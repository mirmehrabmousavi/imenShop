<template>
    <admin-layout>
        <div class="allPostPanel">
            <div class="allPostPanelTop">
                <h1>همه تنوع ها</h1>
                <div class="allPostTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/variety">همه تنوع ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length && deletes == 1">حذف</button>
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
                    <div class="allTableContainer">
                        <all-table :table="posts.data" :nameTable="'variety'" :labels="labels" :deletes="deletes" :shows="shows" v-on:sendCheck="getCheck" :edits="edits" v-on:sendDelete="btnRemoveArray"></all-table>
                    </div>
                </transition>
                <div class="paginate">
                    <paginate-panel :link="posts.links"></paginate-panel>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import PaginatePanel from "../PaginatePanel";
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import AllTable from "../Table/AllTable";
export default {
    name: "AllVariety",
    props : ['posts','errors','shows','categories','showPosts','getPost','labels','deletes','edits'],
    metaInfo: {
        title: 'همه تنوع ها',
    },
    components:{
        PaginatePanel,
        AdminLayout,
        SvgIcon,
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
            showFilter: false,
            showAllCat: false,
            allCategories: this.categories,
            search : '',
            sortCat : '',
            date : '',
            settings: {
                maxScrollbarLength: 60
            },
            sort : 0,
            getPage : 25,
            value : [],
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
        getCheck(id){
            this.value = id;
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '15');
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
                    const url = `/admin/post`;
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
