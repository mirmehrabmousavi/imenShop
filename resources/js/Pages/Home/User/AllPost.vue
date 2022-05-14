<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list></user-list>
            </div>
            <div class="allPostPanel">
                <div class="allPostPanelTop">
                    <h1>همه محصولات من</h1>
                    <div class="allPostTitle">
                        <inertia-link href="/">خانه</inertia-link>
                        <span>/</span>
                        <inertia-link href="/profile/product">همه محصولات من</inertia-link>
                    </div>
                </div>
                <div class="allTable">
                    <div class="allTopTable">
                        <div class="allTopTableItem">
                            <div class="filterItems">
                                <div class="filterTitle" @click="showFilter = !showFilter">
                                    <i>
                                        <svg-icon :icon="'#filter'"></svg-icon>
                                    </i>
                                    فیلتر اطلاعات
                                </div>
                                <transition name="bounce">
                                    <div class="filterContent" v-if="showFilter">
                                        <div class="filterContentItem">
                                            <label>فیلتر عنوان و آیدی</label>
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
                                        <div class="filterContentItem">
                                            <label>فیلتر دسته بندی</label>
                                            <div class="allCategoryPanel">
                                                <div class="categoryShow" @click="showAllCat = !showAllCat">
                                                    <h4 v-if="sortCat == ''">همه</h4>
                                                    <h4 v-else>{{sortCat}}</h4>
                                                    <i>
                                                        <svg-icon :icon="'#down'"></svg-icon>
                                                    </i>
                                                </div>
                                                <ul v-if="showAllCat">
                                                    <VuePerfectScrollbar class="scroll-area">
                                                        <li @click="sendSortCat('')">همه</li>
                                                        <li v-for="item in allCategories" @click="sendSortCat(item.name)">{{item.name}}</li>
                                                    </VuePerfectScrollbar>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filterContentItem">
                                            <label>فیلتر وضعیت</label>
                                            <div class="allCategoryPanel" @click="showSort = !showSort">
                                                <div class="categoryShow">
                                                    <h4 v-if="sort == 0">همه</h4>
                                                    <h4 v-if="sort == 1">پیشنویس</h4>
                                                    <h4 v-if="sort == 2">منتشر شده</h4>
                                                    <i>
                                                        <svg-icon :icon="'#down'"></svg-icon>
                                                    </i>
                                                </div>
                                                <ul v-if="showSort">
                                                    <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                                    <li @click="sort = 1" v-on:click="btnSearch(0)">پیشنویس</li>
                                                    <li @click="sort = 2" v-on:click="btnSearch(0)">منتشر شده</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filterContentItem">
                                            <label>تعداد نمایش</label>
                                            <div class="allCategoryPanel" @click="showPage = !showPage">
                                                <div class="categoryShow">
                                                    <h4 v-if="getPage == 25">25</h4>
                                                    <h4 v-if="getPage == 50">50</h4>
                                                    <h4 v-if="getPage == 70">70</h4>
                                                    <h4 v-if="getPage == 100">100</h4>
                                                    <i>
                                                        <svg-icon :icon="'#down'"></svg-icon>
                                                    </i>
                                                </div>
                                                <ul v-if="showPage">
                                                    <li @click="getPage = 25" v-on:click="btnSearch(0)">25</li>
                                                    <li @click="getPage = 50" v-on:click="btnSearch(0)">50</li>
                                                    <li @click="getPage = 70" v-on:click="btnSearch(0)">70</li>
                                                    <li @click="getPage = 100" v-on:click="btnSearch(0)">100</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                    <div class="paginate">
                        <paginate-panel :link="posts.links"></paginate-panel>
                    </div>
                    <transition name="slide-fade">
                        <div class="allTableContainer">
                            <table>
                                <tr>
                                    <div>
                                        <th @click="checkAll">
                                            <svg-icon :icon="'#check'" v-if="posts.length == value.length"></svg-icon>
                                            <svg-icon :icon="'#uncheck'" v-else></svg-icon>
                                        </th>
                                        <th>#</th>
                                        <th>تصویر</th>
                                        <th>عنوان</th>
                                        <th>قیمت</th>
                                        <th>عملیات</th>
                                    </div>
                                </tr>
                                <tr v-for="(item, index) in posts.data" :key="index">
                                    <div class="active" v-for="(focus , index3) in value" v-if="focus == item.id" :key="index3">

                                        <td @click="getCheck(item.id)">
                                            <i v-for="(values , index2) in value" v-if="values == item.id" :key="index2">
                                                <svg-icon :icon="'#check'"></svg-icon>
                                            </i>
                                            <i>
                                                <svg-icon :icon="'#uncheck'"></svg-icon>
                                            </i>
                                        </td>
                                        <td>
                                            <span>{{++index}}</span>
                                        </td>
                                        <td>
                                            <span>
                                                <img :src="JSON.parse(item.image)[0]">
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{item.title}}</span>
                                        </td>
                                        <td>
                                            <span>{{item.price|NumFormat}} تومان</span>
                                        </td>
                                        <td>
                                            <span>
                                                <inertia-link  :href="'/profile/product/' + item.slug + '/edit'">
                                                    <svg-icon :icon="'#edit'"></svg-icon>
                                                </inertia-link>
                                                <inertia-link :href="'/profile/product/' + item.slug + '/show'">
                                                    <svg-icon :icon="'#eye'"></svg-icon>
                                                </inertia-link>
                                            </span>
                                        </td>
                                    </div>
                                    <div>
                                        <td @click="getCheck(item.id)">
                                            <i v-for="(values , index2) in value" v-if="values == item.id" :key="index2">
                                                <svg-icon :icon="'#check'"></svg-icon>
                                            </i>
                                            <i>
                                                <svg-icon :icon="'#uncheck'"></svg-icon>
                                            </i>
                                        </td>
                                        <td>
                                            <span>{{++index}}</span>
                                        </td>
                                        <td>
                                            <span>
                                                <img :src="JSON.parse(item.image)[0]">
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{item.title}}</span>
                                        </td>
                                        <td>
                                            <span>{{item.price|NumFormat}} تومان</span>
                                        </td>
                                        <td>
                                            <span>
                                                <inertia-link  :href="'/profile/product/' + item.slug + '/edit'">
                                                    <svg-icon :icon="'#edit'"></svg-icon>
                                                </inertia-link>
                                                <inertia-link :href="'/profile/product/' + item.slug + '/show'">
                                                    <svg-icon :icon="'#eye'"></svg-icon>
                                                </inertia-link>
                                            </span>
                                        </td>
                                    </div>
                                </tr>
                            </table>
                        </div>
                    </transition>
                    <div class="paginate">
                        <paginate-panel :link="posts.links"></paginate-panel>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import PaginatePanel from "../../Admin/PaginatePanel";
import HomeLayout from "../../../components/layout/HomeLayout";
import UserList from "./UserList";
export default {
    name: "AllPost",
    props : ['posts','errors','categories'],
    metaInfo: {
        title: 'همه محصولات من',
    },
    components:{
        UserList,
        HomeLayout,
        PaginatePanel,
        SvgIcon,
        datePicker: VuePersianDatetimePicker,
        VuePerfectScrollbar,
    },
    data() {
        return {
            show: false,
            showSort: false,
            showPage: false,
            showSortType: false,
            showStatus: false,
            showImage: false,
            showFilter: false,
            showAllCat: false,
            allCategories: this.categories,
            search : '',
            sortCat : '',
            showPost: '',
            date : '',
            settings: {
                maxScrollbarLength: 60
            },
            form:{
                title : '',
                titleEn : '',
                slug : '',
                summery : '',
                price : '',
                count : '',
                guarantee : '',
                image : [],
                images : [],
                status : '',
                suggest : '',
                off : '',
                body : '',
                postId : '',
            },
            sort : 0,
            getPage : 25,
            value : [],
        }
    },
    methods:{
        checkAll(){
            this.i = 0;
            if(this.posts.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.posts.data.length; this.i++) {
                    this.value.push(this.posts.data[this.i].id);
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
        sendSortCat(name){
            this.sortCat = name;
            const url = `/profile/product`;
            this.$inertia.post(url , {
                category : this.sortCat,
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        deleteImage(index){
            this.form.images.splice(index , 1);
        },
        getClose(){
            this.showImage = false;
        },
        getUrl(url){
            this.form.images.push(url);
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/profile/product`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                category : this.sortCat,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
    },
}
</script>

<style scoped>

</style>
