<template>
    <admin-layout>
        <div class="allTaxonami">
            <div class="allTaxonamiPanelTop">
                <h1>{{name}}</h1>
                <div class="allTaxonamiTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link :href="'/admin/notification/'+ routeAddress">{{name}}</inertia-link>
                </div>
            </div>
            <div class="allTaxonamiOptions">
                <div class="allTaxonamiOptionsButtons">
                    <button @click="openCreate" v-if="adds == 1">افزودن {{ name }}</button>
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
                <div class="createTaxonamiPanel" v-if="show">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <p>توضیحات {{name}} و اطلاعات را اینجا وارد کنید</p>
                    <div class="allCreateTaxonamiItems">
                        <div class="allCreateTaxonamiItem" v-if="name == 'ایمیل'">
                            <label>عنوان :</label>
                            <input type="text" placeholder="عنوان را وارد کنید ..." v-model="form.title">
                        </div>
                        <div class="allCreateTaxonamiItem">
                            <label>کاربران :</label>
                            <post-taxonami :taxes="users" :taxRoute="'کاربر'" :tax="['0']"  v-on:sendTax="getUser"></post-taxonami>
                        </div>
                        <div class="allCreateTaxonamiItemBody">
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
                    <all-table :table="taxes.data" :labels="labels" :deletes="deletes" :shows="shows" :edits="0" v-on:sendShow="btnShow" v-on:sendCheck="getCheck" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="taxes.links"></paginate-panel>
            </div>
            <div class="allShowNotify" v-if="form.notify_id && notifyShow">
                <div class="allShowNotifyItems">
                    <h1>{{notifyShow.title}}</h1>
                    <p>{{notifyShow.body}}</p>
                    <button @click="form.notify_id = 0">انصراف</button>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import AllTable from "../Table/AllTable";
import PostTaxonami from "../Post/PostTaxonami";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "NotificationPanel",
    components: {SvgIcon, PostTaxonami, AllTable, PaginatePanel, AdminLayout},
    props: ['name', 'routeAddress','notifyShow', 'errors', 'labels', 'adds' , 'users', 'shows', 'taxes', 'deletes'],
    metaInfo: {
        title: 'اطلاع رسانی',
    },
    data() {
        return {
            showFilter: false,
            show: false,
            showCats: false,
            showImage: false,
            value: [],
            date: '',
            search: '',
            form: {
                title: '',
                body: '',
                users: [],
                notify_id: 0
            },
        }
    },
    methods: {
        btnShow(id){
            this.form.notify_id = id;
            const url = `/admin/notification/${this.routeAddress}`;
            this.$inertia.post(url,this.form)
        },
        getUser(user) {
            this.form.users = user;
        },
        getCheck(id) {
            this.value = id;
        },
        sendCreate() {
            const url = `/admin/notification/${this.routeAddress}`;
            this.$inertia.post(url, this.form)
                .then(response => {
                    if (!this.errors.name) {
                        this.form.title = '';
                        this.form.body = '';
                        this.form.users = [];
                        this.show = false;
                    }
                })
        },
        btnCancel() {
            this.form.title = '';
            this.form.body = '';
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
                    const url = `/admin/notification/${this.routeAddress}`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', '8');
        },
        openCreate() {
            this.show = true;
            this.form.title = '';
            this.form.body = '';
            this.$eventHub.emit('deleteTax');
            this.form.taxId = '';
        },
        btnSearch(number) {
            if (number == 1) {
                this.date = '';
            }
            const url = `/admin/${this.routeAddress}`;
            this.$inertia.post(url, {
                date: this.date,
                search: this.search
            })
        },
        onReady(editor) {
            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        },
    },
    mounted() {
        this.sidebars();
    }
}
</script>

<style scoped>

</style>
