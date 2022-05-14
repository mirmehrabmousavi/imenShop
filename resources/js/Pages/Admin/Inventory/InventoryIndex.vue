<template>
    <admin-layout>
        <div class="allPostPanel">
            <div class="allPostPanelTop">
                <h1>انبارداری</h1>
                <div class="allPostTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/inventory">انبارداری</inertia-link>
                </div>
            </div>
            <div class="allTable">
                <div class="allTopTable">
                    <div></div>
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
                                                <h4 v-if="sort == 1">موجود</h4>
                                                <h4 v-if="sort == 2">ناموجود</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showSort">
                                                <li @click="sort = 0" v-on:click="btnSearch(0)">همه</li>
                                                <li @click="sort = 1" v-on:click="btnSearch(0)">موجود</li>
                                                <li @click="sort = 2" v-on:click="btnSearch(0)">ناموجود</li>
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
                        <div class="postItem" v-for="item in posts.data">
                            <div class="postTop">
                                <div class="postPic">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                </div>
                                <div class="postTitle">
                                    <h3>
                                        {{item.title}}
                                        <span v-if="item.count >= 1" class="count">({{ item.count }} مورد)</span>
                                        <span v-else>(ناموجود)</span>
                                    </h3>
                                    <h3 v-if="item.titleEn">{{item.titleEn}}</h3>
                                    <h3 v-else>{{item.title}}</h3>
                                </div>
                                <div class="postOptions">
                                    <inertia-link title="ویرایش محصول" :href="'/admin/post/' + item.id + '/edit'">
                                        <svg-icon :icon="'#edit'"></svg-icon>
                                    </inertia-link>
                                    <inertia-link :href="'/admin/variety/' + item.id + '/create'" title="افزودن تنوع">
                                        <svg-icon :icon="'#graph'"></svg-icon>
                                    </inertia-link>
                                    <inertia-link :href="'/admin/post/' + item.id + '/show'" title="نمایش محصول">
                                        <svg-icon :icon="'#eye'"></svg-icon>
                                    </inertia-link>
                                </div>
                            </div>
                            <div class="postBot">
                                <ul>
                                    <li>
                                        <span>رنگ :</span>
                                        <div v-if="item.review[0].colors != '[]'">
                                            <span v-for="value in JSON.parse(item.review[0].colors)">
                                                <span v-if="value.count >= 1" class="count">{{value.name}} ({{ value.count }} مورد)</span>
                                                <span v-else>{{value.name}} (ناموجود)</span>
                                            </span>
                                        </div>
                                        <div v-else>
                                            <span>بدون رنگ</span>
                                        </div>
                                    </li>
                                    <li>
                                        <span>سایز :</span>
                                        <div v-if="item.review[0].size != '[]'">
                                            <span v-for="value in JSON.parse(item.review[0].size)">
                                                <span v-if="value.count >= 1" class="count">{{value.name}} ({{ value.count }} مورد)</span>
                                                <span v-else>{{value.name}} (ناموجود)</span>
                                            </span>
                                        </div>
                                        <div v-else>
                                            <span>بدون سایز</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="paginate">
                    <paginate-panel :link="posts.links"></paginate-panel>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../PaginatePanel";
export default {
    name: "InventoryIndex",
    props : ['posts','errors'],
    metaInfo: {
        title: 'انبار داری',
    },
    components:{
        AdminLayout,
        SvgIcon,
        PaginatePanel,
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
            getPosts: '',
            showPost: '',
            date : '',
            sort : 0,
            getPage : 25,
        }
    },
    methods:{
        sendSortCat(name){
            this.sortCat = name;
            const url = `/admin/inventory`;
            this.$inertia.post(url , {
                category : this.sortCat,
                search : this.search,
                getPage : this.getPage,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/inventory`;
            this.$inertia.post(url , {
                search : this.search,
                getPage : this.getPage,
                category : this.sortCat,
                sort : this.sort,
                sortType : this.sortType,
                date : this.date,
            })
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '6');
        },
    },
    mounted(){
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
