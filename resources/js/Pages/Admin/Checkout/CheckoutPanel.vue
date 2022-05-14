<template>
    <admin-layout>
        <div class="allCheckoutPanel">
            <div class="allCheckoutTop">
                <h1>تسویه حساب ها</h1>
                <div class="allCheckoutTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/checkout">تسویه حساب ها</inertia-link>
                </div>
            </div>
            <div class="allTable" v-show="checkout == ''">
                <div class="allTopTable">
                    <div class="allTopTableItem">
                        <button class="button" @click="btnRemoveArray('')" v-if="value.length">حذف</button>
                    </div>
                    <div class="filterItems">
                        <div class="ContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
                            <svg-icon :icon="'#filter'"></svg-icon>
                            فیلتر اطلاعات
                        </div>
                        <transition name="bounce">
                            <div class="filterContent" v-if="showFilter">
                                <div class="filterContentItem">
                                    <label>فیلتر شماره درخواست</label>
                                    <input type="text" v-model="search"  placeholder="شماره را وارد کنید" @keypress.enter="btnSearch(0)">
                                </div>
                                <div class="filterContentItem">
                                    <label>فیلتر نوع</label>
                                    <div class="allCategoryPanel" @click="showSort = !showSort">
                                        <div class="categoryShow">
                                            <h4 v-if="sort == 0">همه</h4>
                                            <h4 v-if="sort == 1">درحال بررسی</h4>
                                            <h4 v-if="sort == 2">رد شده</h4>
                                            <h4 v-if="sort == 3">تسویه شده</h4>
                                            <i>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </i>
                                        </div>
                                        <ul v-if="showSort">
                                            <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                            <li @click="sort = 1" v-on:click="btnSearch(0)">درحال بررسی</li>
                                            <li @click="sort = 2" v-on:click="btnSearch(0)">رد شده</li>
                                            <li @click="sort = 3" v-on:click="btnSearch(0)">تسویه شده</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="paginate">
                    <paginate-panel :link="checkouts.links"></paginate-panel>
                </div>
                <div class="allTableContainer">
                    <all-table :table="checkouts.data" v-on:sendEdit="openEdit" :labels="labels" :deletes="1" :shows="0" :edits="1" v-on:sendCheck="getCheck" v-on:sendDelete="btnRemoveArray"></all-table>
                </div>
                <div class="paginate">
                    <div class="showInfo">
                        نمایش
                        {{checkouts.from}}
                        تا
                        {{checkouts.to}}
                        از
                        {{checkouts.total}}
                        ورودی
                    </div>
                    <paginate-panel :link="checkouts.links"></paginate-panel>
                </div>
            </div>
            <div class="createCheckoutPanel" v-if="checkout != ''">
                <div class="createCheckoutItems">
                    <p>توضیحات تسویه حساب و اطلاعات را اینجا وارد کنید</p>
                    <div class="allCreateItems">
                        <div class="allCreateItem">
                            <label>نام کاربر :</label>
                            <h3>{{checkout.user.name}}</h3>
                        </div>
                        <div class="allCreateItem">
                            <label>شماره تماس کاربر :</label>
                            <h3>{{checkout.user.number}}</h3>
                        </div>
                        <div class="allCreateItem">
                            <label>ایمیل کاربر :</label>
                            <h3>{{checkout.user.email}}</h3>
                        </div>
                        <div class="allCreateItem">
                            <label>شماره شبا :</label>
                            <input type="text" placeholder="شبا را وارد کنید ..." v-model="form.shaba">
                        </div>
                        <div class="allCreateItem">
                            <label>مبلغ :</label>
                            <input type="text" placeholder="مبلغ را وارد کنید ..." v-model="form.price">
                        </div>
                        <div class="allCreateItem">
                            <label>وضعیت :</label>
                            <div class="allCategoryPanel" @click="showSort = !showSort">
                                <div class="categoryShow">
                                    <h4 v-if="form.status == 0">در حال بررسی</h4>
                                    <h4 v-if="form.status == 1">رد شده</h4>
                                    <h4 v-if="form.status == 2">تسویه شده</h4>
                                    <i>
                                        <svg-icon :icon="'#down'"></svg-icon>
                                    </i>
                                </div>
                                <ul v-if="showSort">
                                    <li @click="form.status = 0">در حال بررسی</li>
                                    <li @click="form.status = 1">رد شده</li>
                                    <li @click="form.status = 2">تسویه شده</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <button @click="updateCheckout">بروزرسانی</button>
                        <button @click="btnCancel">انصراف</button>
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import AllTable from "../Table/AllTable";
export default {
    name: "CheckoutPanel",
    props: ['checkouts','labels','checkEdit'],
    components: {AllTable, PaginatePanel, AdminLayout},
    metaInfo: {
        title: 'نمایش تسویه حساب ها'
    },
    data() {
        return {
            value: [],
            checkout: '',
            showFilter: false,
            search: '',
            sort: 0,
            showSort: false,
            form:{
                shaba: '',
                status: 0,
                price: 0,
                taxId: 0,
                update: 0
            }
        };
    },
    methods:{
        getCheck(id) {
            this.value = id;
        },
        btnCancel(){
            this.checkout = [];
            this.form={
                shaba: '',
                status: 0,
                price: 0,
                taxId: 0,
                update: 0
            }
        },
        updateCheckout(){
            this.form.update = 1;
            const url = `/admin/checkout`;
            this.$inertia.post(url, this.form)
                .then(response => {
                    this.checkout = [];
                    this.form={
                        shaba: '',
                        status: 0,
                        price: 0,
                        taxId: 0,
                        update: 0
                    }
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
                    const url = `/admin/checkout`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        openEdit(id){
            this.form.taxId = id;
            const url = `/admin/checkout`;
            this.$inertia.post(url, {
                taxId: id
            })
                .then(response => {
                    this.checkout = this.checkEdit;
                    this.form.shaba = this.checkout.shaba;
                    this.form.status = this.checkout.status;
                    this.form.price = this.checkout.price;
                })
        },
        btnSearch(){
            const url = `/admin/checkout`;
            this.$inertia.post(url , {
                search : this.search,
                sort : this.sort,
            })
        },
        sidebars() {
            this.$eventHub.emit('sidebar', 13);
        },
    },
    mounted() {
        this.sidebars();
    }
}
</script>

<style scoped>

</style>
