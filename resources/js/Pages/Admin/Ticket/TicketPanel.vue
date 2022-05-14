<template>
    <admin-layout>
        <div class="allTicketPanel">
            <div class="allTicketPanelTitle">
                <div class="allTicketPanelAddress">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/ticket">درخواست ها</inertia-link>
                </div>
                <div class="allTicketPanelTitleOption">
                    <div class="filterItems">
                        <div class="filterTitle" @click.stop="showFilter = !showFilter">
                            <i>
                                <svg-icon :icon="'#filter'"></svg-icon>
                            </i>
                            فیلتر اطلاعات
                        </div>
                        <transition name="bounce">
                            <div class="filterContent" v-if="showFilter">
                                <div class="filterContentItem">
                                    <label>فیلتر نام فرستنده</label>
                                    <input type="text" v-model="search"  placeholder="نام را وارد کنید" @keypress.enter="btnSearch">
                                </div>
                                <div class="filterContentItem">
                                    <label>فیلتر تاریخ</label>
                                    <div class="allTicketPanelTitleDate">
                                        <date-picker
                                            v-model="date"
                                            type="datetime"
                                            format="YYYY-MM-DD"
                                            display-format="jYYYY-jMM-jDD"
                                            @close="dateClose"
                                            placeholder="تاریخ را وارد کنید"
                                            :timezone="true"
                                        />
                                        <i @click.stop="removeDate" v-if="date">
                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                        </i>
                                    </div>
                                </div>
                                <div class="filterContentItem">
                                    <label>فیلتر وضعیت</label>
                                    <div class="allCategoryPanel" @click="showSort = !showSort">
                                        <div class="categoryShow">
                                            <h4 v-if="sort == 0">همه</h4>
                                            <h4 v-if="sort == 1">پاسخ داده</h4>
                                            <h4 v-if="sort == 2">پاسخ نداده</h4>
                                            <i>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </i>
                                        </div>
                                        <ul v-if="showSort">
                                            <li @click="sort = 0" v-on:click="sortData">همه</li>
                                            <li @click="sort = 1" v-on:click="sortData">پاسخ داده</li>
                                            <li @click="sort = 2" v-on:click="sortData">پاسخ نداده</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                    <button class="button" v-if="value.length" @click="btnRemoveArray">حذف</button>
                </div>
                <div class="createTicketPanel" v-if="show">
                    <div class="createTicketPanelItems">
                        <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                            <i>
                                <svg-icon :icon="'#error'"></svg-icon>
                            </i>
                            <span>
                               {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                        </div>
                        <div class="createTicketPanelItem">
                            <label>توضیحات و درخواست</label>
                            <textarea v-model="form.body" placeholder="درخواست را وارد کنید ..."></textarea>
                        </div>
                        <div class="createTicketPanelItem">
                            <label>پاسخ</label>
                            <textarea v-model="form.answer" placeholder="پاسخ را وارد کنید ..."></textarea>
                        </div>
                        <div class="button">
                            <button @click="updateTicket">ارسال پاسخ</button>
                            <button @click="show = false">انصراف</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="allTable">
                <div class="paginate">
                    <paginate-panel :link="tickets.links"></paginate-panel>
                </div>
                <table>
                    <tr>
                        <div>
                            <th @click="checkAll">
                                <svg-icon :icon="'#check'" v-if="tickets.data.length == value.length"></svg-icon>
                                <svg-icon :icon="'#uncheck'" v-else></svg-icon>
                            </th>
                            <th>آیدی</th>
                            <th>نام فرستنده</th>
                            <th>درخواست</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                            <th>عملیات</th>
                        </div>
                    </tr>
                    <tr v-for="(item , index) in tickets.data">
                        <div>
                            <td @click="getCheck(item.id)">
                                <i v-for="values in value" v-if="values == item.id">
                                    <svg-icon :icon="'#check'"></svg-icon>
                                </i>
                                <i>
                                    <svg-icon :icon="'#uncheck'"></svg-icon>
                                </i>
                            </td>
                            <td>
                                <span>{{item.id}}</span>
                            </td>
                            <td>
                                <span>{{item.user.name}}</span>
                            </td>
                            <td>
                                <p>{{item.body}}</p>
                            </td>
                            <td>
                                <span>
                                    <span class="unActive" v-if="item.answer == null">پاسخ نداده</span>
                                    <span class="activeStatus" v-else>پاسخ داده</span>
                                </span>
                            </td>
                            <td>
                                <span>{{item.created_at}}</span>
                            </td>
                            <td>
                                <i @click="openEdit(item.id)">
                                    <svg-icon :icon="'#edit'"></svg-icon>
                                </i>
                                <i @click="btnRemove(item.id)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </td>
                        </div>
                    </tr>
                </table>
                <div class="paginate">
                    <paginate-panel :link="tickets.links"></paginate-panel>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from 'vue-persian-datetime-picker'
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "TicketPanel",
    props : ['tickets','allow','userData','errors'],
    components:{
        VuePerfectScrollbar,
        AdminLayout,
        PaginatePanel,
        datePicker: VuePersianDatetimePicker,
        SvgIcon,
    },
    metaInfo: {
      title: 'درخواست ها'
    },
    data() {
        return {
            showSort: false,
            show: false,
            showUser: false,
            showFilter: false,
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                body : '',
                message : '',
                title : '',
                answer : '',
            },
            search : '',
            date : '',
            sort : 0,
            ticketId : '',
            value : [],
            allows : [],
            i : 0,
        }
    },
    methods:{
        removeDate(){
            this.date = '';
            const url = `/admin/ticket`;
            this.$inertia.post(url , {
                sort : this.sort,
                search : this.search,
                date : this.date,
            })
        },
        dateClose(){
            const url = `/admin/ticket`;
            this.$inertia.post(url , {
                sort : this.sort,
                search : this.search,
                date : this.date,
            })
        },
        sortData(){
            const url = `/admin/ticket`;
            this.$inertia.post(url , {
                sort : this.sort,
                search : this.search,
                date : this.date,
            })
        },
        btnSearch(){
            const url = `/admin/ticket`;
            this.$inertia.post(url , {
                sort : this.sort,
                date : this.date,
                search : this.search
            })
        },
        updateTicket(){
            const url = `/admin/ticket/${this.ticketId}`;
            this.$inertia.put(url , this.form)
                .then(response =>{
                    if (this.errors.body){
                    }else{
                        this.form.title = '';
                        this.form.body = '';
                        this.form.answer = '';
                        this.show = !this.show;
                    }
                })
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '5');
        },
        checkAll(){
            this.i = 0;
            if(this.tickets.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.tickets.data.length; this.i++) {
                    this.value.push(this.tickets.data[this.i].id);
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
        openEdit(id){
            const url = `/admin/ticket/${id}/edit`;
            axios.get(url)
                .then(response=>{
                    this.show = true;
                    this.form.body = response.data.body;
                    this.form.answer = response.data.answer;
                    this.ticketId = response.data.id;
                })
        },
        btnRemoveArray(){
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
                    const url = `/admin/ticket/remove`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
        btnRemove(id){
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
                    this.value = [];
                    this.value.push(id);
                    const url = `/admin/ticket/remove`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
    },
    mounted() {
        this.sidebar();
    },
}
</script>

<style scoped>

</style>
