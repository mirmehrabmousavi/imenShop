<template>
    <admin-layout>
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
            <div class="allCreateVariety">
                <div class="allVarietiesTitle">
                    <span>ویرایش تنوع</span>
                </div>
                <div class="allCreateVarietyItems">
                    <div class="allCreateVarietyItem">
                        <h3>وضعیت :</h3>
                        <div class="allCategoryPanel" @click.stop="showStatus = !showStatus">
                            <div class="categoryShow">
                                <h4 v-if="form.status == null">وضعیت را وارد کنید ...</h4>
                                <h4 v-if="form.status == 0">پیشنویس</h4>
                                <h4 v-if="form.status == 1">منتشر شده</h4>
                            </div>
                            <ul v-if="showStatus">
                                <li @click.stop="sendStatus(0)">پیشنویس</li>
                                <li @click.stop="sendStatus(1)">منتشر شده</li>
                            </ul>
                        </div>
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
                <div class="allCreateVarietyItems">
                    <div class="allCreateVarietyItem">
                        <h3>گارانتی</h3>
                        <post-taxonami add="1" :taxes="guarantees" :taxRoute="'گارانتی'" :tax="posts.guarantee"  v-on:sendTax="getGuarantees"></post-taxonami>
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
                    <button @click="btnAddVar">ویرایش تنوع</button>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import PostTaxonami from "../../Home/User/PostTaxonami";
import SvgIcon from "../../Svg/SvgIcon";
import UserList from "../../Home/User/UserList";
import AdminLayout from "../../../components/layout/AdminLayout";
import VuePerfectScrollbar from "vue-perfect-scrollbar";

export default {
    name: "EditVariety",
    components: {PostTaxonami, SvgIcon, UserList, AdminLayout ,VuePerfectScrollbar},
    props:['posts','guarantees'],
    metaInfo: {
        title: 'ویرایش تنوع',
    },
    data(){
        return{
            form:{
                price : this.posts.offPrice,
                count : this.posts.count,
                off : this.posts.off,
                status : this.posts.status,
                allSize: [],
                allColor: [],
                allGuarantee: [],
                abilities: [],
                sizes:JSON.parse(this.posts.review[0].size),
                colors:JSON.parse(this.posts.review[0].colors),
                update: 1,
            },
            showSort: false,
            showStatus: false,
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
        btnAddVar(){
            this.form.allColor = JSON.stringify(this.form.colors);
            this.form.allSize = JSON.stringify(this.form.sizes);
            const url = `/admin/variety/${this.posts.id}/edit`;
            this.$inertia.post(url , this.form)
        },
        sendStatus(num){
            this.form.status = num;
            this.showStatus = !this.showStatus;
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
            this.$eventHub.emit('sidebar' , 15);
        }
    },
    mounted() {
        this.checkData();
    }
}
</script>

<style scoped>

</style>
