<template>
    <admin-layout>
        <div class="allPayPanel">
            <div class="allPayPanelTop">
                <h1>دیدگاه ها</h1>
                <div class="allPayPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/comment">دیدگاه ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length && deletes == 1">حذف</button>
                    </div>
                    <div class="allTopTableItem">
                        <div class="filterItems">
                            <div class="ContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
                                <i>
                                    <svg-icon :icon="'#filter'"></svg-icon>
                                </i>
                                فیلتر اطلاعات
                            </div>
                            <transition name="bounce">
                                <div class="filterContent" v-if="showFilter">
                                    <div class="filterContentItem">
                                        <label>فیلتر آیدی</label>
                                        <input type="text" v-model="search"  placeholder="آیدی را وارد کنید" @keypress.enter="btnSearch(0)">
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
                                        <label>فیلتر وضعیت</label>
                                        <div class="allCategoryPanel" @click="showSort = !showSort">
                                            <div class="categoryShow">
                                                <h4 v-if="sort == 0">همه</h4>
                                                <h4 v-if="sort == 1">در انتظار تایید</h4>
                                                <h4 v-if="sort == 2">تایید شده</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showSort">
                                                <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                                <li @click="sort = 1" v-on:click="btnSearch(0)">در انتظار تایید</li>
                                                <li @click="sort = 2" v-on:click="btnSearch(0)">تایید شده</li>
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
                    <paginate-panel :link="comments.links"></paginate-panel>
                </div>
                <div class="allTableContainer">
                    <table>
                        <tr>
                            <div>
                                <th @click="checkAll">
                                    <svg-icon :icon="'#check'" v-if="comments.data.length == value.length"></svg-icon>
                                    <svg-icon :icon="'#uncheck'" v-else></svg-icon>
                                </th>
                                <th>آیدی</th>
                                <th>کالا</th>
                                <th>نویسنده</th>
                                <th>عنوان</th>
                                <th>توضیح</th>
                                <th>وضعیت</th>
                                <th>تاریخ ثبت</th>
                                <th>عملیات</th>
                            </div>
                        </tr>
                        <tr v-for="(item , index) in comments.data">
                            <div>
                                <td @click="getCheck(item.id)">
                                    <span>
                                        <i v-for="(values , index) in value" v-if="values == item.id">
                                            <svg-icon :icon="'#check'"></svg-icon>
                                        </i>
                                        <i>
                                            <svg-icon :icon="'#uncheck'"></svg-icon>
                                        </i>
                                    </span>
                                </td>
                                <td>
                                    <span>{{item.id}}</span>
                                </td>
                                <td>
                                    <span>
                                        <inertia-link class="tablePic" :href="'/product/' + item.post.slug">
                                            <img :src="JSON.parse(item.post.image)[0]">
                                        </inertia-link>
                                    </span>
                                </td>
                                <td>
                                    <span>{{item.user.name}}</span>
                                </td>
                                <td>
                                    <span>{{item.title}}</span>
                                </td>
                                <td>
                                    <span>{{item.body}}</span>
                                </td>
                                <td>
                                    <span>
                                        <span class="unActive" v-if="item.approved == 0">در انتظار تایید</span>
                                        <span class="activeStatus" v-else>تایید شده </span>
                                    </span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                                <td>
                                    <span>
                                        <inertia-link :href="'/admin/comment/' + item.id" v-if="edits == 1">
                                            <svg-icon :icon="'#edit'"></svg-icon>
                                        </inertia-link>
                                        <i @click="btnRemoveArray(item.id , index)" v-if="deletes == 1">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </span>
                                </td>
                            </div>
                        </tr>
                    </table>
                </div>
                <div class="paginate">
                    <div class="showInfo">
                        نمایش
                        {{comments.from}}
                        تا
                        {{comments.to}}
                        از
                        {{comments.total}}
                        ورودی
                    </div>
                    <paginate-panel :link="comments.links"></paginate-panel>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
export default {
    name: "CommentPanel",
    components:{
        AdminLayout,
        PaginatePanel,
        datePicker: VuePersianDatetimePicker,
        SvgIcon
    },
    props:['comments','deletes','edits'],
    metaInfo: {
        title: 'دیدگاه ها'
    },
    data() {
        return {
            search : '',
            date : '',
            sort : 0,
            getPage : 25,
            showPage : false,
            showFilter : false,
            showSort : false,
            allows : [],
            value : [],
        }
    },
    methods:{
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
                    const url = `/admin/comment`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/comment`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                date : this.date,
            })
        },
        checkAll(){
            this.i = 0;
            if(this.comments.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.comments.data.length; this.i++) {
                    this.value.push(this.comments.data[this.i].id);
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
        sidebar(){
            this.$eventHub.emit('sidebar' , 5);
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>
