<template>
    <admin-layout>
        <div class="allRobot">
            <div class="allRobotPanelTop">
                <h1>رباط</h1>
                <div class="allRobotTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/robot">رباط ها</inertia-link>
                </div>
            </div>
            <div class="allRobotOptions">
                <div class="allRobotOptionsButtons">
                    <button @click="openCreate" v-if="adds == 1">افزودن رباط</button>
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
            <transition name="slide-fade">
                <div class="createRobotPanel" v-if="show || taxEditData">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <p>توضیحات رباط و اطلاعات را اینجا وارد کنید</p>
                    <div class="allCreateRobotItems">
                        <div class="allCreateRobotItem">
                            <label>نام :</label>
                            <input type="text" placeholder="نام را وارد کنید ..." v-model="form.name">
                        </div>
                        <div class="allCreateRobotItem">
                            <label>توکن رباط :</label>
                            <input type="text" placeholder="توکن را وارد کنید ..." v-model="form.token">
                        </div>
                        <div class="allCreateRobotItem">
                            <label>آیدی گروه(imenShop) :</label>
                            <input type="text" placeholder="آیدی را وارد کنید ..." v-model="form.group">
                        </div>
                        <div class="allCreateRobotItem">
                            <label>وضعیت فعالیت :</label>
                            <div class="allCategoryPanel" @click="showStatus = !showStatus">
                                <div class="categoryShow">
                                    <h4 v-if="form.status == 0">غیر فعال</h4>
                                    <h4 v-if="form.status == 1">فعال</h4>
                                </div>
                                <ul v-if="showStatus">
                                    <li @click="sendStatus(0)">غیر فعال</li>
                                    <li @click="sendStatus(1)">فعال</li>
                                </ul>
                            </div>
                        </div>
                        <div class="allCreateRobotItem">
                            <label>اطلاعات ارسالی</label>
                            <div class="allPermissionItems">
                                <div class="getPermissions" @click="showData = !showData">
                                    <h4 v-if="form.dataShow.length == 0">اطلاعات را وارد کنید</h4>
                                    <ul>
                                        <li v-for="(item , index) in form.dataShow">
                                            <i @click.stop="removeData(index)">
                                                <svg-icon :icon="'#cancel'"></svg-icon>
                                            </i>
                                            <span>{{item}}</span>
                                        </li>
                                    </ul>
                                </div>
                                <ul class="getAllPermissions" v-if="showData">
                                    <VuePerfectScrollbar class="scroll-area">
                                        <li @click.prevent="getData('payDay' , 'ارسال اطلاعات پرداختی امروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات پرداختی امروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('ticketDay' , 'ارسال اطلاعات تیکت های امروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات تیکت های امروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('viewDay' , 'ارسال اطلاعات بازدید های امروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات بازدید های امروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('registerDay' , 'ارسال اطلاعات ثبت نام های امروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات ثبت نام های امروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('payYesterDay' , 'ارسال اطلاعات پرداختی دیروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات پرداختی دیروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('ticketYesterDay' , 'ارسال اطلاعات تیکت های دیروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات تیکت های دیروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('viewYesterDay' , 'ارسال اطلاعات بازدید های دیروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات بازدید های دیروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('registerYesterDay' , 'ارسال اطلاعات ثبت نام های دیروز بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات ثبت نام های دیروز بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('payMonth' , 'ارسال اطلاعات پرداختی این ماه بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات پرداختی این ماه بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('ticketMonth' , 'ارسال اطلاعات تیکت های این ماه بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات تیکت های این ماه بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('viewMonth' , 'ارسال اطلاعات بازدید های این ماه بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات بازدید های این ماه بعد هربار ورود به صفحه اصلی</li>
                                        <li @click.prevent="getData('registerMonth' , 'ارسال اطلاعات ثبت نام های این ماه بعد هربار ورود به صفحه اصلی')">ارسال اطلاعات ثبت نام های این ماه بعد هربار ورود به صفحه اصلی</li>
                                    </VuePerfectScrollbar>
                                </ul>
                            </div>
                        </div>
                        <div class="allCreateRobotItem">
                            <label>توضیحات :</label>
                            <textarea v-model="form.body" placeholder="توضیحات را وارد کنید ..."></textarea>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="sendCreate">ارسال اطلاعات</button>
                        <button @click="btnCancel">انصراف</button>
                    </div>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="taxes.links"></paginate-panel>
            </div>
            <transition name="slide-fade" v-if="shows">
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
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../PaginatePanel";
import AllTable from "../Table/AllTable";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "RobotPanel",
    props: ['errors', 'taxEdit', 'labels', 'adds', 'shows', 'taxes', 'deletes', 'edits'],
    metaInfo: {
        title: 'رباط ها',
    },
    components: {
        AdminLayout,
        SvgIcon,
        PaginatePanel,
        AllTable,
        VuePerfectScrollbar,
    },
    data() {
        return {
            showFilter: false,
            show: false,
            showCats: false,
            showImage: false,
            value: [],
            taxEditData: '',
            date: '',
            search: '',
            showData: false,
            showStatus: false,
            i: 0,
            form: {
                name: '',
                token: '',
                group: '',
                status: 0,
                data: [],
                datas: [],
                body: '',
                dataShow: [],
                taxId: '',
            },
        }
    },
    methods: {
        sendStatus(number){
            this.form.status = number;
        },
        getData(item,index){
            this.i = 0;
            for ( this.i ; this.i <  this.form.data.length; this.i++) {
                if (this.form.data[this.i] == item){
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.form.data.push(item);
                this.form.dataShow.push(index);
            }
            this.i = 0;
        },
        removeData(index){
            this.form.data.splice(index , 1);
            this.form.dataShow.splice(index , 1);
        },
        openEdit(id) {
            this.form.name = '';
            this.form.body = '';
            this.form.status = 0;
            this.form.taxId = id;
            this.show = !this.show;
            const url = `/admin/robot`;
            this.$inertia.post(url, {
                taxId: id
            })
                .then(response => {
                    this.taxEditData = this.taxEdit;
                    this.form.name = this.taxEditData.name;
                    this.form.group = this.taxEditData.group;
                    this.form.status = this.taxEditData.status;
                    this.form.token = this.taxEditData.token;
                    this.form.body = this.taxEditData.body;
                    this.i = 0;
                    for ( this.i ; this.i <  JSON.parse(this.taxEditData.data).length; this.i++) {
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'payDay'){
                            this.form.dataShow.push('ارسال اطلاعات پرداختی امروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'ticketDay'){
                            this.form.dataShow.push('ارسال اطلاعات تیکت های امروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'viewDay'){
                            this.form.dataShow.push('ارسال اطلاعات بازدید های امروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'registerDay'){
                            this.form.dataShow.push('ارسال اطلاعات ثبت نام های امروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'payYesterDay'){
                            this.form.dataShow.push('ارسال اطلاعات پرداختی دیروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'ticketYesterDay'){
                            this.form.dataShow.push('ارسال اطلاعات تیکت های دیروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'viewYesterDay'){
                            this.form.dataShow.push('ارسال اطلاعات بازدید های دیروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'registerYesterDay'){
                            this.form.dataShow.push('ارسال اطلاعات ثبت نام های دیروز بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'payMonth'){
                            this.form.dataShow.push('ارسال اطلاعات پرداختی این ماه بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'ticketMonth'){
                            this.form.dataShow.push('ارسال اطلاعات تیکت های این ماه بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'viewMonth'){
                            this.form.dataShow.push('ارسال اطلاعات بازدید های این ماه بعد هربار ورود به صفحه اصلی');
                        }
                        if (JSON.parse(this.taxEditData.data)[this.i] == 'registerMonth'){
                            this.form.dataShow.push('ارسال اطلاعات ثبت نام های این ماه بعد هربار ورود به صفحه اصلی');
                        }
                        this.form.data.push(JSON.parse(this.taxEditData.data)[this.i]);
                    }
                    this.i = 0;
                })
        },
        getCheck(id) {
            this.value = id;
        },
        sendCreate() {
            this.form.datas = JSON.stringify(this.form.data);
            const url = `/admin/robot`;
            this.$inertia.post(url, this.form)
                .then(response => {
                    if (!this.errors.name) {
                        this.form.name = '';
                        this.form.body = '';
                        this.form.status = 0;
                        this.form.group = '';
                        this.form.token = '';
                        this.form.datas = [];
                        this.form.data = [];
                        this.form.dataShow = [];
                        this.show = false;
                        this.taxEditData = '';
                    }
                })
        },
        btnCancel() {
            this.form.name = '';
            this.form.body = '';
            this.form.group = '';
            this.form.status = 0;
            this.form.token = '';
            this.form.datas = [];
            this.form.data = [];
            this.form.dataShow = [];
            this.taxEditData = '';
            this.show = false;
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
                    const url = `/admin/robot`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', '9');
        },
        openCreate() {
            this.show = true;
            this.form.name = '';
            this.form.group = '';
            this.form.token = '';
            this.form.status = 0;
            this.form.datas = [];
            this.form.data = [];
            this.form.dataShow = [];
            this.form.body = '';
            this.taxEditData = '';
            this.form.taxId = '';
        },
        btnSearch(number) {
            if (number == 1) {
                this.date = '';
            }
            const url = `/admin/robot`;
            this.$inertia.post(url, {
                date: this.date,
                search: this.search
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
