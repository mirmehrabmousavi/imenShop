<template>
    <admin-layout>
        <div class="allShowPay">
            <div class="topShowPay">
                <div class="title">
                    <h1>جزئیات سفارش</h1>
                    <span>{{pay.created_at}}</span>
                    <div v-if="pay.pay_meta.length">
                        <span v-if="pay.pay_meta[0].post.type != 1 && pay.status == 100">
                            <a :href="'/admin/pay/invoice/' + pay.id">دریافت فاکتور</a>
                        </span>
                    </div>
                    <button @click="showMap = !showMap">
                        <svg-icon :icon="'#location'"></svg-icon>
                        نمایش در نقشه
                    </button>
                </div>
                <div class="detail">
                    <div class="topDetail">
                        <div class="items">
                            <div class="item">
                                <h5>تحویل گیرنده :</h5>
                                <div v-if="pay.address.length">{{pay.address[0].name}}</div>
                            </div>
                            <div class="item">
                                <h5>شماره تماس :</h5>
                                <div v-if="pay.address.length">{{pay.address[0].number}}</div>
                            </div>
                            <div class="item" v-if="pay.carrier.length">
                                <h5>نوع حامل :</h5>
                                <div>{{pay.carrier[0].name}}</div>
                            </div>
                            <div class="item" v-if="pay.time">
                                <h5>بازه زمانی :</h5>
                                <div>
                                    <span>بازه</span>
                                    <span>{{JSON.parse(pay.time).from}}:00</span>
                                    <span>-</span>
                                    <span>{{JSON.parse(pay.time).to}}:00</span>
                                    <span>---></span>
                                    <span>{{JSON.parse(pay.time).day}} / </span>
                                    <span>{{JSON.parse(pay.time).month}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="items">
                            <div class="item">
                                <h5>آدرس :</h5>
                                <div v-if="pay.address.length">{{pay.address[0].address}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="botDetail">
                        <div class="items">
                            <div class="item">
                                <h5>مبلغ پرداخت :</h5>
                                <div>{{pay.price|NumFormat}} تومان</div>
                            </div>
                            <div class="item" v-if="pay.method == 2">
                                <h5>مبلغ بیعانه :</h5>
                                <div>{{pay.deposit|NumFormat}} تومان</div>
                            </div>
                            <div class="item">
                                <h5>وضعیت پرداخت :</h5>
                                <div class="allCategoryPanel" @click="btnShowData(1)">
                                    <div class="categoryShow">
                                        <h4 class="active" v-if="payStatus == 100">پرداخت کامل</h4>
                                        <h4 class="active" v-else-if="payStatus == 50">پرداخت بیعانه</h4>
                                        <h4 class="unActive" v-else>پرداخت نشده</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showData == 1">
                                        <li @click="btnChangeStatus(100)">پرداخت کامل</li>
                                        <li @click="btnChangeStatus(50)">پرداخت بیعانه</li>
                                        <li @click="btnChangeStatus(0)">پرداخت نشده</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <h5>روش پرداخت :</h5>
                                <div v-if="pay.method == 0">پرداخت از درگاه</div>
                                <div v-if="pay.method == 1">پرداخت از کیف پول</div>
                                <div v-if="pay.method == 2">پرداخت در محل</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="trackPay">
                    <input type="text" placeholder="کد رهگیری را وارد کنید" v-model="track">
                    <button @click="btnTrack">اعمال کد</button>
                </div>
            </div>
            <div class="allShowPayContainer">
                <div class="topContainer">
                    <div class="level">
                        <h3>مرحله مرسوله :</h3>
                        <span v-if="pay.deliver == 0">دریافت سفارش</span>
                        <span v-if="pay.deliver == 1">در انتظار بررسی</span>
                        <span v-if="pay.deliver == 2">بسته بندی شده</span>
                        <span v-if="pay.deliver == 3">تحویل پیک</span>
                        <span v-if="pay.deliver == 4">تکمیل شده</span>
                    </div>
                    <div class="rateItemsCount">
                        <div class="rateItemsCountItem" title="دریافت سفارش">
                            <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 1">
                            </div>
                            <div class="rateItemsCountItemBar" v-if="pay.deliver == 0">
                            </div>
                            <div class="rateItemsCountItemCircleActives" @click.prevent="btnChange(0)" v-if="pay.deliver >= 1">
                                <svg-icon :icon="'#delivery-complete'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircleActive" @click.prevent="btnChange(0)" v-if="pay.deliver == 0">
                                <svg-icon :icon="'#delivery-complete'"></svg-icon>
                            </div>
                        </div>
                        <div class="rateItemsCountItem" title="در انتظار بررسی">
                            <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 2">
                            </div>
                            <div class="rateItemsCountItemBar" v-if="pay.deliver <= 1">
                            </div>
                            <div class="rateItemsCountItemCircleActives" @click.prevent="btnChange(1)" v-if="pay.deliver >= 2">
                                <svg-icon :icon="'#waiting-room'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircleActive" @click.prevent="btnChange(1)" v-if="pay.deliver == 1">
                                <svg-icon :icon="'#waiting-room'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircle" @click.prevent="btnChange(1)" v-if="pay.deliver <= 0">
                                <svg-icon :icon="'#waiting-room'"></svg-icon>
                            </div>
                        </div>
                        <div class="rateItemsCountItem" title="بسته بندی شده">
                            <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 3">
                            </div>
                            <div class="rateItemsCountItemBar" v-if="pay.deliver <= 2">
                            </div>
                            <div class="rateItemsCountItemCircleActives" @click.prevent="btnChange(2)" v-if="pay.deliver >= 3">
                                <svg-icon :icon="'#package-delivery'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircleActive" @click.prevent="btnChange(2)" v-if="pay.deliver == 2">
                                <svg-icon :icon="'#package-delivery'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircle" @click.prevent="btnChange(2)" v-if="pay.deliver <= 1">
                                <svg-icon :icon="'#package-delivery'"></svg-icon>
                            </div>
                        </div>
                        <div class="rateItemsCountItem" title="تحویل پیک">
                            <div class="rateItemsCountItemBarActive" v-if="pay.deliver >= 4">
                            </div>
                            <div class="rateItemsCountItemBar" v-if="pay.deliver <= 3">
                            </div>
                            <div class="rateItemsCountItemCircleActives" @click.prevent="btnChange(3)" v-if="pay.deliver >= 4">
                                <svg-icon :icon="'#delivery-truck'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircleActive" @click.prevent="btnChange(3)" v-if="pay.deliver == 3">
                                <svg-icon :icon="'#delivery-truck'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircle" @click.prevent="btnChange(3)" v-if="pay.deliver <= 2">
                                <svg-icon :icon="'#delivery-truck'"></svg-icon>
                            </div>
                        </div>
                        <div class="rateItemsCountItem" title="تکمیل شده">
                            <div class="rateItemsCountItemCircleActive" @click.prevent="btnChange(4)" v-if="pay.deliver == 4">
                                <svg-icon :icon="'#delivery-box'"></svg-icon>
                            </div>
                            <div class="rateItemsCountItemCircle" @click.prevent="btnChange(4)" v-if="pay.deliver <= 3">
                                <svg-icon :icon="'#delivery-box'"></svg-icon>
                            </div>
                        </div>
                    </div>
                    <div class="code">
                        <h3>شماره سفارش :</h3>
                        <span>{{pay.property}}</span>
                    </div>
                    <div class="cashBacks">
                        <div class="cash">
                            <button v-if="payBack == 1" class="active">کش بک به کیف پول سایت</button>
                            <button v-else @click="btnCashBack(1)">کش بک به کیف پول سایت</button>
                        </div>
                        <div class="cash">
                            <button v-if="payBack == 2" class="active">کش بک به حساب با شبا</button>
                            <button v-else @click="btnCashBack(2)">کش بک به حساب با شبا</button>
                        </div>
                    </div>
                </div>
                <div class="items">
                    <div class="titleProducts">
                        <div class="title">محصولاتی که سفارش داده شده</div>
                        <button @click="showAdd = !showAdd">افزودن محصول به سفارش</button>
                    </div>
                    <div class="createPayMeta" v-if="showAdd">
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="showCreate = !showCreate">
                                <h4 v-if="productName == ''">انتخاب محصول</h4>
                                <h4 v-else>{{productName.title}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showCreate">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li>
                                        <input v-model="search" type="text" placeholder="جستجو" @keyup="btnSearch">
                                    </li>
                                    <li v-for="item in allProducts" @click="sendProduct(item,item.id)">{{item.title}}</li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                        <div class="createPayItems" v-if="productName">
                            <div class="createPayItem">
                                <h4>تعداد</h4>
                                <input type="text" v-model="count" placeholder="تعداد را وارد کنید ...">
                            </div>
                            <div class="createPayItem" v-if="productName.guarantee.length">
                                <h4>گارانتی</h4>
                                <select v-model="guarantee">
                                    <option v-for="item in productName.guarantee" :value="item.id">{{ item.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="createPayItems" v-if="productName">
                            <div class="createPayItem" v-if="productName.review">
                                <h4>رنگ</h4>
                                <select v-if="productName.review[0].colors" v-model="color">
                                    <option v-for="item in JSON.parse(productName.review[0].colors)" :value="item">{{ item.name }}</option>
                                </select>
                            </div>
                            <div class="createPayItem" v-if="productName.review">
                                <h4>سایز</h4>
                                <select v-if="productName.review[0].size" v-model="size">
                                    <option v-for="item in JSON.parse(productName.review[0].size)" :value="item">{{ item.name }}</option>
                                </select>
                            </div>
                        </div>
                        <button class="btnAdd" @click="btnAdd">اضافه شدن محصول به سفارش</button>
                    </div>
                    <div class="item" v-for="item in pay.pay_meta">
                        <inertia-link :href="'/product/'+item.post.slug" class="cartDetailPic">
                            <img :src="JSON.parse(item.post.image)[0]" :alt="item.post.title">
                        </inertia-link>
                        <div class="cartDetailInfo">
                            <inertia-link :href="'/product/'+item.post.slug" class="cartDetailInfoItem">
                                <h3>{{item.post.title}}</h3>
                            </inertia-link>
                            <div class="cartDetailInfoItem" v-if="JSON.parse(item.color) != ''">
                                <i>
                                    <svg-icon :icon="'#color'"></svg-icon>
                                </i>
                                <span>{{JSON.parse(item.color).name}}</span>
                            </div>
                            <div class="cartDetailInfoItem" v-if="JSON.parse(item.size) != ''">
                                <i>
                                    <svg-icon :icon="'#sizeFront'"></svg-icon>
                                </i>
                                <span>{{JSON.parse(item.size).name}}</span>
                            </div>
                            <div class="cartDetailInfoItem" v-if="item.guarantee">
                                <i>
                                    <svg-icon :icon="'#security'"></svg-icon>
                                </i>
                                <span>{{item.guarantee.name}}</span>
                            </div>
                            <div class="cartDetailInfoItem">
                                <i>
                                    <svg-icon :icon="'#post'"></svg-icon>
                                </i>
                                <span>{{item.count}}</span>
                            </div>
                            <div class="cartDetailInfoItem">
                                <i>
                                    <svg-icon :icon="'#cost'"></svg-icon>
                                </i>
                                <span>{{item.price|NumFormat}} تومان</span>
                            </div>
                            <div class="cartDetailInfoItem">
                                <i>
                                    <svg-icon :icon="'#car'"></svg-icon>
                                </i>
                                <span v-if="item.deliver == 0">دریافت سفارش</span>
                                <span v-if="item.deliver == 1">در انتظار بررسی</span>
                                <span v-if="item.deliver == 2">بسته بندی شده</span>
                                <span v-if="item.deliver == 3">تحویل پیک</span>
                                <span v-if="item.deliver == 4">تکمیل شده</span>
                            </div>
                            <div class="cartCount">
                                <button @click="changeCart(item.id , change = '1')">+</button>
                                <span class="cartCountPrice">{{item.count}}</span>
                                <button @click="changeCart(item.id , change = '0')">-</button>
                            </div>
                            <div class="cartDelete">
                                <i @click="deleteCart(item.id)">
                                    <svg-icon :icon="'#cancel'"></svg-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="showMapAddress" v-if="showMap">
                <show-address :map="map" :pay="pay" v-on:sendClose="getClose"></show-address>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import ShowAddress from "../../../components/Admin/Pay/ShowAddress";
export default {
    name: "ShowPayPanel",
    components: {
        ShowAddress,
        AdminLayout,
        SvgIcon,
        VuePerfectScrollbar,
    },
    props: ['pay','map','products'],
    metaInfo: {
        title: 'نمایش پرداختی'
    },
    data(){
        return{
            productName: '',
            productId: '',
            showMap: false,
            showCreate: false,
            showAdd: false,
            showData: 0,
            allProducts: this.products,
            size: '',
            color: '',
            search: '',
            count: '',
            guarantee: '',
            payStatus: this.pay.status,
            track: this.pay.track,
            payBack: this.pay.back,
        }
    },
    methods:{
        btnShowData(num){
            if(this.showData == num){
                this.showData = 0;
            }else{
                this.showData = num;
            }
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
        sendProduct(name,id){
            this.productName= name;
            this.productId= id;
            this.showCreate= false;
        },
        btnChangeStatus(num){
            const url = `/admin/show-pay/${this.pay.id}`;
            this.$inertia.post(url,{
                status : num,
                update: 5
            })
            .then(res=>{
                this.payStatus = num;
            })
        },
        btnTrack(){
            const url = `/admin/show-pay/${this.pay.id}`;
            this.$inertia.post(url,{
                track : this.track,
                update: 7
            })
            .then(res=>{
                this.$swal.fire(
                    'با موفقیت انجام شد',
                    'تغییرات با موفقیت انجام شد',
                    'success'
                );
            })
        },
        btnCashBack(num){
            this.$swal.fire({
                title: 'آیا مطمینین ؟',
                text: "مبلغ به حساب شخص برگشت داده میشود",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'برگشت مبلغ',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    const url = `/admin/show-pay/${this.pay.id}`;
                    this.$inertia.post(url,{
                        back : num,
                        update: 6
                    })
                        .then(res=>{
                            this.payBack = num;
                        })
                }
            })
        },
        changeCart(id , change){
            this.showLoader = true;
            const url = `/admin/show-pay/${this.pay.id}`;
            this.$inertia.post(url,{
                change : change,
                payMeta : id,
                pay: this.pay.id,
                update: 1
            })
                .then(response=>{
                })
        },
        btnAdd(){
            this.showLoader = true;
            const url = `/admin/show-pay/${this.pay.id}`;
            this.$inertia.post(url,{
                size : this.size,
                color: this.color,
                count: this.count,
                guarantee: this.guarantee.id,
                product: this.productName,
                update: 4
            })
                .then(response=>{
                    this.showAdd = false;
                    this.size = '';
                    this.color = '';
                    this.count = '';
                    this.guarantee = '';
                    this.productName = '';
                })
        },
        deleteCart(id){
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
                    const url = `/admin/show-pay/${this.pay.id}`;
                    this.$inertia.post(url,{
                        payMeta : id,
                        pay: this.pay.id,
                        update: 3
                    })
                }
            })
        },
        getPrint(id){
            this.$htmlToPaper('printMe');
        },
        btnChange(num){
            const url = `/admin/show-pay/${this.pay.id}`;
            this.$inertia.post(url , {
                deliver : num,
                update : 2,
            })
                .then(response=>{
                    this.deliver = num
                })
        },
        getClose(){
            this.showMap = !this.showMap;
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
