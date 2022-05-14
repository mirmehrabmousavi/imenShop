<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="11"></user-list>
            </div>
            <div class="allPostPanel">
                <div class="allPostPanelTop">
                    <h1>افزودن تنوع</h1>
                    <div class="allPostTitle">
                        <inertia-link href="/">خانه</inertia-link>
                        <span>/</span>
                        <a>افزودن تنوع</a>
                    </div>
                </div>
                <div class="allAddVariety">
                    <div class="allAddVarietyTop">
                        <div class="allAddVarietyPic">
                            <img :src="JSON.parse(posts.image)[0]" :alt="posts.title">
                        </div>
                        <div class="allAddVarietySubject">
                            <h1>{{ posts.title }}</h1>
                            <ul>
                                <li v-if="posts.category.length">
                                    <span>دسته بندی :</span>
                                    <span>{{posts.category[0].name}}</span>
                                </li>
                                <li v-if="posts.review[0].colors">
                                    <span>تنوع رنگ :</span>
                                    <span>{{JSON.parse(posts.review[0].colors).length}}</span>
                                </li>
                                <li v-if="posts.review[0].size">
                                    <span>تنوع سایز :</span>
                                    <span>{{JSON.parse(posts.review[0].size).length}}</span>
                                </li>
                                <li>
                                    <span>مبلغ :</span>
                                    <span>{{posts.price|NumFormat}} تومان</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="allVarieties">
                        <div class="allVarietiesTitle">
                            <span>لیست فروشندگان</span>
                        </div>
                        <ul>
                            <li v-for="(item,index) in posts.post">
                                <div class="sellerProfile">
                                    <img v-if="item.user.profile" :src="item.user.profile" :alt="item.user.name">
                                    <img v-else src="/img/user.png" :alt="item.user.name">
                                    <span>{{item.user.name}}</span>
                                </div>
                                <div class="sellerSize" v-if="JSON.parse(item.review[0].colors).length">
                                    <div class="allCategoryPanel">
                                        <div class="categoryShow" @click="btnColorChange(index)">
                                            <h4>{{sellerColors[index].name}}</h4>
                                            <i>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </i>
                                        </div>
                                        <ul v-if="showColorsSeller == index">
                                            <VuePerfectScrollbar class="scroll-area">
                                                <li v-for="item in JSON.parse(item.review[0].colors)" v-if="item.count >= 0" :key="item.name" :title="item.name" @click.prevent="btnColorSeller(item,index)">
                                                    <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                                    <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                                </li>
                                            </VuePerfectScrollbar>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sellerSize" v-if="JSON.parse(item.review[0].size).length">
                                    <div class="allCategoryPanel">
                                        <div class="categoryShow" @click="btnShowSizeSeller(index)">
                                            <h4>{{sellerSizes[index].name}}</h4>
                                            <i>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </i>
                                        </div>
                                        <ul v-if="showSizeSeller == index">
                                            <VuePerfectScrollbar class="scroll-area">
                                                <li v-for="item in JSON.parse(item.review[0].size)" v-if="item.count >= 0" :key="item.name" :title="item.name" @click.prevent="btnSizeSeller(item , index)">
                                                    <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                                    <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                                </li>
                                            </VuePerfectScrollbar>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sellerSize" v-if="item.guarantee.length">
                                    <div class="allCategoryPanel">
                                        <div class="categoryShow" @click="btnShowGuaranteeSeller(index)">
                                            <h4>{{sellerGuarantees[index].name}}</h4>
                                            <i>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </i>
                                        </div>
                                        <ul v-if="showGuaranteeSeller == index">
                                            <VuePerfectScrollbar class="scroll-area">
                                                <li v-for="item in item.guarantee" :key="item.name" :title="item.name" @click.prevent="btnGuaranteeSeller(item,index)">
                                                    <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                                    <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                                </li>
                                            </VuePerfectScrollbar>
                                        </ul>
                                    </div>
                                </div>
                                <div class="sellerPrice">{{sellerPrice[index]|NumFormat}} تومان</div>
                                <div class="sellerAdd">
                                    <button v-if="sellerCount[index] >= 1" @click="btnAddSeller(item.id , index)">
                                        <svg-icon class="loading" v-if="showLoaderSeller == index" :icon="'#loading'"></svg-icon>
                                        <span v-else>افزودن به سبد خرید</span>
                                    </button>
                                    <button v-else>ناموجود</button>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="allCreateVariety">
                        <div class="allVarietiesTitle">
                            <span>افزودن تنوع</span>
                        </div>
                        <div class="allCreateVarietyItems">
                            <div class="allCreateVarietyItem">
                                <h3>گارانتی</h3>
                                <post-taxonami add="1" :taxes="guarantees" :taxRoute="'گارانتی'" :tax="['0']"  v-on:sendTax="getGuarantees"></post-taxonami>
                            </div>
                            <div class="allCreateVarietyItem">
                                <h3>تعداد</h3>
                                <input type="text" v-model="form.count" placeholder="تعداد را وارد کنید ...">
                            </div>
                        </div>
                        <div class="allCreateVarietyItems">
                            <div class="allCreateVarietyItem">
                                <h3>قیمت</h3>
                                <input type="text" v-model="form.price" placeholder="قیمت را وارد کنید ...">
                            </div>
                            <div class="allCreateVarietyItem">
                                <h3>تخفیف</h3>
                                <input type="text" v-model="form.off" placeholder="تخفیف را وارد کنید ...">
                            </div>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>رنگ</label>
                                <i @click="addColor">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>نام رنگ</th>
                                    <th>نام انگلیسی</th>
                                    <th>کد رنگ</th>
                                    <th>تعداد</th>
                                    <th>افزودن قیمت (تومان)</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.colors" :key="index">
                                    <td>
                                        <input type="text" v-model="item.name" placeholder="نام را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.nameEn" placeholder="نام را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.color" placeholder="کد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.count" placeholder="تعداد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.price" placeholder="قیمت را وارد کنید">
                                    </td>
                                    <td>
                                        <i @click="deleteColor(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <div class="abilityPostToolTip">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p>برای اضافه نشدن قیمت به قیمت اصلی عدد صفر را وارد کنید</p>
                            </div>
                        </div>
                        <div class="abilityPost">
                            <div class="abilityTitle">
                                <label>سایز</label>
                                <i @click="addSize">
                                    <svg-icon :icon="'#add'"></svg-icon>
                                </i>
                            </div>
                            <table class="abilityTable">
                                <tr>
                                    <th>سایز</th>
                                    <th>سایز انگلیسی</th>
                                    <th>تعداد</th>
                                    <th>افزودن قیمت (تومان)</th>
                                    <th>حذف</th>
                                </tr>
                                <tr v-for="(item, index) in form.sizes" :key="index">
                                    <td>
                                        <input type="text" v-model="item.name" placeholder="سایز را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.nameEn" placeholder="سایز را وارد کنید">
                                    </td>
                                    <td>
                                        <input type="text" v-model="item.count" placeholder="تعداد را وارد کنید">
                                    </td>
                                    <td>
                                        <input v-model="item.price" placeholder="قیمت را وارد کنید">
                                    </td>
                                    <td>
                                        <i @click="deleteSize(index)">
                                            <svg-icon :icon="'#trash'"></svg-icon>
                                        </i>
                                    </td>
                                </tr>
                            </table>
                            <div class="abilityPostToolTip">
                                <i>
                                    <svg-icon :icon="'#lamp'"></svg-icon>
                                </i>
                                <p>برای اضافه نشدن قیمت به قیمت اصلی عدد صفر را وارد کنید</p>
                            </div>
                        </div>
                        <div class="buttons">
                            <button @click="btnAddVar">افزودن تنوع</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import UserList from "./UserList";
import SvgIcon from "../../Svg/SvgIcon";
import PostTaxonami from "./PostTaxonami";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "AddVariety",
    components: {PostTaxonami, SvgIcon, UserList, HomeLayout ,VuePerfectScrollbar},
    props:['posts','guarantees'],
    data(){
        return{
            form:{
                price : null,
                count : null,
                off : '',
                allSize: [],
                allColor: [],
                allGuarantee: [],
                abilities: [],
                sizes:[],
                colors:[],
                update: 1,
            },
            showSizeSeller: -1,
            showColorsSeller: -1,
            showGuaranteeSeller: -1,
            sellerColors: [],
            sellerSizes: [],
            sellerGuarantees: [],
            sellerPrice: [],
            sellerCount: [],
            showLoaderSeller: -1,
            showSort: false,
            i: 0,
            notificationSystem: {
                options: {
                    show: {
                        icon: "icon-person",
                        position: "topCenter",
                    },
                    success: {
                        position: "bottomRight"
                    },
                    error: {
                        position: "bottomRight"
                    },
                }
            },
        }
    },
    methods: {
        btnAddSeller(id , index){
            this.showLoaderSeller = index;
            const url = `/add-cart2`;
            axios.post(url ,{
                postID : id,
                colorName : this.sellerColors[index],
                sizeName : this.sellerSizes[index],
                price: this.sellerPrice[index],
                guarantee: this.sellerGuarantees[index].id,
            })
                .then(response=>{
                    this.showLoaderSeller = -1;
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data == 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                })
                .catch(err =>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        btnGuaranteeSeller(item,index){
            this.sellerGuarantees[index] = item;
        },
        btnColorSeller(item,index){
            this.showColorsSeller = -1;
            this.sellerColors[index] = item;
            if (this.sellerSizes[index]){
                this.sellerPrice[index] = parseInt(this.posts.post[index].price) + parseInt(this.sellerSizes[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.sellerSizes[index].count <= 0 || this.posts.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }else{
                this.sellerPrice[index] = parseInt(this.posts.post[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.posts.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }
        },
        btnSizeSeller(item,index){
            this.showSizeSeller = -1;
            this.sellerSizes[index] = item;
            if (this.sellerColors[index]){
                this.sellerPrice[index] = parseInt(this.posts.post[index].price) + parseInt(this.sellerColors[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.sellerColors[index].count <= 0 || this.posts.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }else{
                this.sellerPrice[index] = parseInt(this.posts.post[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.posts.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }
        },
        btnColorChange(index){
            if(this.showColorsSeller == index){
                this.showColorsSeller = -1;
            }else{
                this.showColorsSeller = index;
            }
        },
        btnShowGuaranteeSeller(index){
            if(this.showGuaranteeSeller == index){
                this.showGuaranteeSeller = -1;
            }else{
                this.showGuaranteeSeller = index;
            }
        },
        btnShowSizeSeller(index){
            if(this.showSizeSeller == index){
                this.showSizeSeller = -1;
            }else{
                this.showSizeSeller = index;
            }
        },
        btnAddVar(){
            this.form.allColor = JSON.stringify(this.form.colors);
            this.form.allSize = JSON.stringify(this.form.sizes);
            const url = `/profile/add-variety/${this.posts.slug}`;
            this.$inertia.post(url , this.form)
        },
        deleteSize(index){
            this.form.sizes.splice(index,1);
        },
        deleteColor(index){
            this.form.colors.splice(index,1);
        },
        addColor() {
            this.form.colors.push({
                name:'',
                nameEn:'',
                color:'',
                price:'',
            });
        },
        addSize() {
            this.form.sizes.push({
                name:'',
                nameEn:'',
                price:'',
            });
        },
        getGuarantees(guarantee){
            this.form.allGuarantee = guarantee;
        },
        checkData(){
            for ( this.i ; this.i <  this.posts.post.length; this.i++) {
                this.sellerPrice.push(this.posts.post[this.i].price);
                this.sellerCount.push(this.posts.post[this.i].count);
                if (JSON.parse(this.posts.post[this.i].review[0].colors).length){
                    this.sellerPrice[this.i] = parseInt(this.sellerPrice[this.i]) + parseInt(JSON.parse(this.posts.post[this.i].review[0].colors)[0].price);
                    this.sellerColors.push(JSON.parse(this.posts.post[this.i].review[0].colors)[0]);
                    if(this.sellerColors[this.i].count <= 0){
                        this.sellerCount[this.i] = 0;
                    }
                }
                if (JSON.parse(this.posts.post[this.i].review[0].size).length){
                    this.sellerPrice[this.i] = parseInt(this.sellerPrice[this.i]) + parseInt(JSON.parse(this.posts.post[this.i].review[0].size)[0].price);
                    this.sellerSizes.push(JSON.parse(this.posts.post[0].review[0].size)[0]);
                    if(this.sellerSizes[this.i].count <= 0){
                        this.sellerCount[this.i] = 0;
                    }
                }
                if (this.posts.post[this.i].guarantee.length){
                    this.sellerGuarantees.push(this.posts.post[this.i].guarantee[0]);
                }
            }
            this.i = 0;
        }
    },
    mounted() {
        this.checkData();
    }
}
</script>

<style scoped>

</style>
