<template>
    <admin-layout>
        <div class="allTaxonami">
            <div class="allTaxonamiPanelTop">
                <h1>{{ name }}</h1>
                <div class="allTaxonamiTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link :href="'/admin/'+url">{{ name }}</inertia-link>
                </div>
            </div>
            <div class="allTaxonamiOptions">
                <div class="allTaxonamiOptionsButtons">
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
                            <div class="filterContentItem" v-if="name == 'رویداد'">
                                <label>فیلتر نوع</label>
                                <div class="allCategoryPanel" @click="showType = !showType">
                                    <div class="categoryShow">
                                        <h4 v-if="type == 0">همه</h4>
                                        <h4 v-if="type == 1">ثبت نام</h4>
                                        <h4 v-if="type == 2">نشانه گذاری</h4>
                                        <h4 v-if="type == 3">سبد خرید</h4>
                                        <h4 v-if="type == 4">دیدگاه</h4>
                                        <h4 v-if="type == 5">علاقه مندی</h4>
                                        <h4 v-if="type == 6">امتیاز داده</h4>
                                        <h4 v-if="type == 7">بازخورد</h4>
                                        <h4 v-if="type == 8">تیکت</h4>
                                        <h4 v-if="type == 9">تغییر رمز</h4>
                                        <h4 v-if="type == 10">تغییر رمز</h4>
                                        <h4 v-if="type == 11">تغییر اطلاعات کاربر</h4>
                                        <h4 v-if="type == 12">تکمیل اطلاعات کاربر</h4>
                                        <h4 v-if="type == 13">پرسش و پاسخ</h4>
                                        <h4 v-if="type == 14">ارسال مدارک</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showType">
                                        <li @click="type = 0" v-on:click="btnSearch(0)">همه</li>
                                        <li @click="type = 1" v-on:click="btnSearch(0)">ثبت نام</li>
                                        <li @click="type = 2" v-on:click="btnSearch(0)">نشانه گذاری</li>
                                        <li @click="type = 3" v-on:click="btnSearch(0)">سبد خرید</li>
                                        <li @click="type = 4" v-on:click="btnSearch(0)">دیدگاه</li>
                                        <li @click="type = 5" v-on:click="btnSearch(0)">علاقه مندی</li>
                                        <li @click="type = 6" v-on:click="btnSearch(0)">امتیاز داده</li>
                                        <li @click="type = 7" v-on:click="btnSearch(0)">بازخورد</li>
                                        <li @click="type = 8" v-on:click="btnSearch(0)">تیکت</li>
                                        <li @click="type = 9" v-on:click="btnSearch(0)">تغییر رمز</li>
                                        <li @click="type = 10" v-on:click="btnSearch(0)">تغییر رمز</li>
                                        <li @click="type = 11" v-on:click="btnSearch(0)">تغییر اطلاعات کاربر</li>
                                        <li @click="type = 12" v-on:click="btnSearch(0)">تکمیل اطلاعات کاربر</li>
                                        <h4 @click="type = 13" v-on:click="btnSearch(0)">پرسش و پاسخ</h4>
                                        <h4 @click="type = 14" v-on:click="btnSearch(0)">ارسال مدارک</h4>
                                    </ul>
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
                <div class="showTable" v-if="show == false">
                    <all-table :table="taxes.data" :labels="labels" :deletes="deletes" :shows="0" :edits="0" v-on:sendCheck="getCheck" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="taxes.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../PaginatePanel";
import AllTable from "../Table/AllTable";

export default {
    name: "EventPanel",
    props: ['errors' , 'labels' , 'adds' , 'shows' , 'name' , 'url' , 'taxes', 'deletes', 'edits'],
    metaInfo: {
        title: 'رویداد',
    },
    components: {
        AdminLayout,
        SvgIcon,
        PaginatePanel,
        AllTable,
    },
    data() {
        return {
            showFilter: false,
            showType: false,
            show: false,
            value: [],
            date: '',
            search: '',
            type: 0,
        }
    },
    methods: {
        getCheck(id) {
            this.value = id;
        },
        btnRemoveArray(id) {
            if (id) {
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
                    const url = `/admin/${this.url}`;
                    this.$inertia.post(url, {'value': this.value})
                }
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', 12);
        },
        btnSearch(number) {
            if (number == 1) {
                this.date = '';
            }
            const url = `/admin/${this.url}`;
            this.$inertia.post(url, {
                date: this.date,
                search: this.search,
                type: this.type,
            })
        },
    },
    mounted() {
        this.sidebars();
    }
}
</script>

<style scoped>

</style>
