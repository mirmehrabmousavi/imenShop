<template>
    <admin-layout>
        <div class="questionPanel">
            <div class="allQuestionPanelTop">
                <h1>پرسش و پاسخ ها</h1>
                <div class="allQuestionPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/question">پرسش و پاسخ ها</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length">حذف</button>
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
                                        <label>فیلتر تاریخ</label>
                                        <div class="allQuestionPanelTitleDate">
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
                                        <div class="allCategoryPanel" @click="showStatus = 2">
                                            <div class="categoryShow">
                                                <h4 v-if="approveds == -1">همه</h4>
                                                <h4 v-if="approveds == 0">در حال بررسی</h4>
                                                <h4 v-if="approveds == 1">تایید شده</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showStatus == 2">
                                                <li @click.stop="approveds = -1; showStatus= 0" v-on:click="btnSearch(0)">همه</li>
                                                <li @click.stop="approveds = 0; showStatus= 0" v-on:click="btnSearch(0)">در حال بررسی</li>
                                                <li @click.stop="approveds = 1; showStatus= 0" v-on:click="btnSearch(0)">تایید شده</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
                <div class="paginate">
                    <paginate-panel :link="questions.links"></paginate-panel>
                </div>
                <div class="allTableContainer">
                    <all-table :table="questions.data" :labels="labels" :deletes="deletes" :shows="0" :edits="edits" v-on:sendCheck="getCheck" v-on:sendDelete="btnRemoveArray" v-on:sendEdit="openEdit"></all-table>
                </div>
                <div class="paginate">
                    <div class="showInfo">
                        نمایش
                        {{questions.from}}
                        تا
                        {{questions.to}}
                        از
                        {{questions.total}}
                        ورودی
                    </div>
                    <paginate-panel :link="questions.links"></paginate-panel>
                </div>
            </div>
            <div class="createQuestionPanel" v-if="show">
                <div class="createQuestionPanelItems">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <i>
                            <svg-icon :icon="'#error'"></svg-icon>
                        </i>
                        <span>
                               {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                    </div>
                    <div class="createQuestionPanelItem">
                        <label>توضیحات</label>
                        <textarea v-model="form.body" placeholder="توضیح را وارد کنید ..."></textarea>
                    </div>
                    <div class="createQuestionPanelItem">
                        <label>تاییدیه</label>
                        <div class="allCategoryPanel" @click="showStatus = 1">
                            <div class="categoryShow">
                                <h4 v-if="form.approved == 0">در حال بررسی</h4>
                                <h4 v-if="form.approved == 1">تایید شده</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showStatus == 1">
                                <li @click.stop="btnApproved(0)">در حال بررسی</li>
                                <li @click.stop="btnApproved(1)">تایید شده</li>
                            </ul>
                        </div>
                    </div>
                    <div class="button">
                        <button @click="updateQuestion">بروزرسانی</button>
                        <button @click="show = false">انصراف</button>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import PaginatePanel from "../PaginatePanel";
import AllTable from "../Table/AllTable";
import SvgIcon from "../../Svg/SvgIcon";
import AdminLayout from "../../../components/layout/AdminLayout";
export default {
    name: "QuestionPanel",
    components: {AdminLayout,AllTable,PaginatePanel,SvgIcon},
    props: ['errors' , 'taxEdit', 'labels', 'questions', 'deletes', 'edits'],
    metaInfo: {
        title: 'پرسش و پاسخ ها'
    },
    data(){
        return{
            showFilter: false,
            date: '',
            show: false,
            value: [],
            showStatus: false,
            approveds: -1,
            form: {
                approved: 0,
                body: '',
                taxId: '',
            },
        }
    },
    methods:{
        btnApproved(num){
            this.form.approved = num;
            this.showStatus = -1;
        },
        getCheck(id) {
            this.value = id;
        },
        updateQuestion(){
            const url = `/admin/question`;
            this.$inertia.post(url , this.form)
                .then(response =>{
                    if (this.errors.body){
                    }else{
                        this.form.body = '';
                        this.form.approved = '';
                        this.form.taxId = '';
                        this.show = !this.show;
                    }
                })
        },
        openEdit(id) {
            this.form.approved = '';
            this.form.body = '';
            this.form.taxId = id;
            this.show = !this.show;
            const url = `/admin/question`;
            this.$inertia.post(url, {
                taxId: id
            })
                .then(response => {
                    this.taxEditData = this.taxEdit;
                    this.form.body = this.taxEditData.body;
                    this.form.approved = this.taxEditData.approved;
                })
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
                    const url = `/admin/question`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        btnSearch(number) {
            if (number == 1) {
                this.date = '';
            }
            const url = `/admin/question`;
            this.$inertia.post(url, {
                date: this.date,
                approveds: this.approveds,
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', 5);
        },
    },
    mounted() {
        this.sidebars();
    }
}
</script>

<style scoped>

</style>
