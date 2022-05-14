<template>
    <admin-layout>
        <div class="allCreatePays">
            <div class="allPayPanelTop">
                <h1>افزودن سفارش دستی</h1>
                <div class="allPayPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/pay/create">افزودن سفارش دستی</inertia-link>
                </div>
            </div>
            <div class="allCreatePay">
                <div class="createItems">
                    <div class="createItem">
                        <h4>وضعیت خرید</h4>
                        <select v-model="status">
                            <option value="0">رد خرید</option>
                            <option value="100">تایید پرداخت</option>
                        </select>
                    </div>
                    <div class="createItem">
                        <h4>وضعیت ارسال</h4>
                        <select v-model="deliver">
                            <option value="0">دریافت سفارش</option>
                            <option value="1">در انتظار بررسی</option>
                            <option value="2">بسته بندی شده</option>
                            <option value="3">تحویل پیک</option>
                            <option value="4">تکمیل شده</option>
                        </select>
                    </div>
                    <div class="createItem">
                        <h4>نحوه ارسال</h4>
                        <select v-model="carrier">
                            <option v-for="item in carriers" :value="item.id">{{item.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="createItems">
                    <div class="createItem">
                        <h4>کاربر را انتخاب کنین</h4>
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="btnShowAllCat(-1)">
                                <h4 v-if="people.userName == ''">کاربر را انتخاب کنید</h4>
                                <h4 v-else>{{people.userName}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showAllCat == -1">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li>
                                        <input type="text" @keyup="searchAllUser" placeholder="جستجو ..." v-model="searchUser">
                                    </li>
                                    <li v-for="item in allUser" @click="sendSortCat(item.id,item.name)">{{item.name}}</li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="titleProducts">
                    <div class="title">محصولاتی که سفارش داده شده</div>
                    <button @click="btnShowAdd">افزودن محصول به سفارش</button>
                </div>
                <div class="createPayMetas">
                    <div class="createPayMeta" v-for="(element,index) in products">
                        <h2>محصول جدید برای سفارش</h2>
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="btnShowCreate(index)">
                                <h4 v-if="element.productName == ''">انتخاب محصول</h4>
                                <h4 v-else>{{element.productName.title}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showCreate == index">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li>
                                        <input v-model="search" type="text" placeholder="جستجو" @keyup="btnSearch">
                                    </li>
                                    <li v-for="item in allProducts" @click="sendProduct(item,item.id,index)">{{item.title}}</li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                        <div class="createPayItems" v-if="element.productName">
                            <div class="createPayItem">
                                <h4>تعداد</h4>
                                <input type="text" v-model="element.count" placeholder="تعداد را وارد کنید ...">
                            </div>
                            <div class="createPayItem">
                                <h4>قیمت هر یکی</h4>
                                <input type="text" v-model="element.price" placeholder="قیمت را وارد کنید ...">
                            </div>
                            <div class="createPayItem" v-if="element.productName.guarantee.length">
                                <h4>گارانتی</h4>
                                <select v-model="element.guarantee">
                                    <option v-for="item in element.productName.guarantee" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="createPayItems" v-if="element.productName">
                            <div class="createPayItem" v-if="element.productName.review">
                                <h4>رنگ</h4>
                                <select v-if="element.productName.review[0].colors" v-model="element.color">
                                    <option v-for="item in JSON.parse(element.productName.review[0].colors)" :value="item">{{ item.name }}</option>
                                </select>
                            </div>
                            <div class="createPayItem" v-if="element.productName.review">
                                <h4>سایز</h4>
                                <select v-if="element.productName.review[0].size" v-model="element.size">
                                    <option v-for="item in JSON.parse(element.productName.review[0].size)" :value="item">{{ item.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="send" @click="btnCreatePay">ثبت سفارش</button>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
export default {
    name: "CreatePay",
    components: {AdminLayout,SvgIcon,VuePerfectScrollbar},
    props:['allUsers','allProduct','carriers'],
    metaInfo: {
        title: 'افزودن پرداختی دستی'
    },
    data(){
        return{
            allUser: this.allUsers,
            allProducts: this.allProduct,
            showAllCat: -2,
            people: {
                userName: '',
                userId: '',
            },
            searchUser: '',
            search: '',
            carrier: '',
            status: 100,
            deliver: 0,
            showCreate: -2,
            products: [],
        }
    },
    methods: {
        btnShowAdd(){
            this.products.push({
                productName:'',
                productId:'',
                size:'',
                color:'',
                price:'',
                search:'',
                count:'',
                guarantee:'',
            });
        },
        btnSearch(){
            const url = `/admin/search-product`;
            axios.post(url , {
                search : this.search,
            })
                .then(response=>{
                    this.allProducts = response.data;
                })
        },
        btnCreatePay(){
            const url = `/admin/pay/create`;
            this.$inertia.post(url , {
                people : this.people,
                carrier : this.carrier,
                status : this.status,
                deliver : this.deliver,
                products : this.products,
            })
                .then(response=>{
                    this.allProducts = response.data;
                })
        },
        sendProduct(name,id,index){
            this.products[index].productName= name;
            this.products[index].productId= id;
            this.showCreate= -2;
        },
        btnShowCreate(num){
            if(this.showCreate == num){
                this.showCreate = -2;
            }else{
                this.showCreate = num;
            }
        },
        btnShowAllCat(num){
            if(this.showAllCat == num){
                this.showAllCat = -2;
            }else{
                this.showAllCat = num;
            }
        },
        searchAllUser(){
            const url = '/admin/search-tax';
            axios.post(url ,{
                search: this.searchUser,
                taxRoute: 'کاربر'
            })
                .then(response=>{
                    if (this.searchUser == ''){
                        this.allUser = this.allUsers;
                    }else{
                        this.allUser = response.data;
                    }
                })
        },
        sendSortCat(id,name){
            this.people.userName = name;
            this.people.userId = id;
            this.showAllCat = -2;
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '7');
        }
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
