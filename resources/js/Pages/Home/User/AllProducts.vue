<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="11"></user-list>
            </div>
            <div class="allPostPanel">
                <div class="allPostPanelTop">
                    <h1>همه محصولات</h1>
                    <div class="allPostTitle">
                        <inertia-link href="/">خانه</inertia-link>
                        <span>/</span>
                        <inertia-link href="/profile/all-products">همه محصولات</inertia-link>
                    </div>
                </div>
                <div class="allTable">
                    <div class="allTopTable">
                        <div class="searches">
                            <input type="text" v-model="search" @keypress.enter="btnSearch" :placeholder="$t('search')">
                        </div>
                    </div>
                    <div class="paginate">
                        <paginate-panel :link="posts.links"></paginate-panel>
                    </div>
                    <transition name="slide-fade">
                        <div class="allTableContainer">
                            <div class="postItem" v-for="item in posts.data" @click="getCheck(item.id)">
                                <div class="postTop">
                                    <div class="postPic">
                                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                    </div>
                                    <div class="postTitle">
                                        <h3>{{item.title}}</h3>
                                        <h3 v-if="item.titleEn">{{item.titleEn}}</h3>
                                        <h3 v-else>{{item.title}}</h3>
                                    </div>
                                    <div class="postOptions">
                                        <inertia-link :href="'/profile/add-variety/' + item.slug" title="افزودن تنوع">
                                            <svg-icon :icon="'#graph'"></svg-icon>
                                            شما هم بفروشید
                                        </inertia-link>
                                    </div>
                                </div>
                                <div class="postBot">
                                    <ul>
                                        <li>
                                            <span>گروه :</span>
                                            <span v-if="item.category.length">{{item.category[0].name}}</span>
                                            <span v-else>بدون گروه</span>
                                        </li>
                                        <li>
                                            <span>قیمت محصول :</span>
                                            <span>{{ item.offPrice|NumFormat }} تومان</span>
                                        </li>
                                        <li>
                                            <span>تعداد تنوع فعال :</span>
                                            <span>{{ item.post_count }}</span>
                                        </li>
                                        <li>
                                            <span>وضعیت محصول :</span>
                                            <span v-if="item.status == 0">پیشنویس</span>
                                            <span v-if="item.status == 1">منتشر شده</span>
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
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import UserList from "./UserList";
import PaginatePanel from "../../Admin/PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "AllProducts",
    props: ['posts'],
    metaInfo: {
        title: 'همه محصولات',
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
            search : '',
            settings: {
                maxScrollbarLength: 60
            },
            value : [],
        }
    },
    methods:{
        checkAll(){
            this.i = 0;
            if(this.post.data.length == this.value.length){
                this.value = [];
            }else{
                this.value = [];
                for ( this.i ; this.i <  this.post.data.length; this.i++) {
                    this.value.push(this.post.data[this.i].id);
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
        btnSearch(){
            const url = `/profile/all-products`;
            this.$inertia.post(url , {
                search : this.search,
            })
        },
    },
}
</script>

<style scoped>

</style>
