<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="1"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <div class="allUserIndexInfoLike">
                    <label>{{$t('mySigns')}}</label>
                    <div class="paginate" v-if="allBookmark.links">
                        <paginate-panel :link="allBookmark.links"></paginate-panel>
                    </div>
                    <div class="allUserIndexInfoLikeItem">
                        <ul>
                            <li v-for="(item , index) in allBookmark.data">
                                <inertia-link :href="'/product/' + item.slug">
                                    <div class="userItemPic">
                                        <img v-lazy="JSON.parse(item.image)[0]" :alt="item.title">
                                    </div>
                                    <div class="userItemSubject">
                                        <div class="userItemSubjectTitle" v-if="$i18n.locale == 'fa'">{{item.title}}</div>
                                        <div class="userItemSubjectTitle" v-if="$i18n.locale == 'en'">{{item.titleEn}}</div>
                                        <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.price|NumFormat}} تومان</s>
                                            </div>
                                            <h3 v-if="item.off != null">{{item.offPrice|NumFormat}} تومان</h3>
                                            <h3 v-else>{{item.price|NumFormat}} تومان</h3>
                                        </div>
                                        <div class="postPriceItemEn" v-if="$i18n.locale == 'en'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.price|NumFormat}} toman</s>
                                            </div>
                                            <h3 v-if="item.off != null">{{item.offPrice|NumFormat}} toman</h3>
                                            <h3 v-else>{{item.price|NumFormat}} toman</h3>
                                        </div>
                                    </div>
                                </inertia-link>
                                <i @click.stop="btnBookmark(item.id , index)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </li>
                        </ul>
                    </div>
                    <div class="paginate" v-if="allBookmark.links">
                        <paginate-panel :link="allBookmark.links"></paginate-panel>
                    </div>
                </div>
            </div>
            <div class="archiveLoader" v-if="showLoader">
                <div class="archiveLoaderItem">
                    <img src="/img/loadingImage.gif" alt="صبر کنید">
                </div>
            </div>

        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../../Admin/PaginatePanel";
import UserList from "./UserList";
export default {
    name: "BookmarkUser",
    props: ['bookmarkPosts','title'],
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    metaInfo() {
        return {
            title: `نشانه ها - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                reptilian: 'gator',
                amp: true
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    data() {
        return {
            showPayMeta : [],
            showLoader : false,
            allBookmark : this.bookmarkPosts,
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
    methods:{
        btnBookmark(id , index){
            const url = `/bookmark`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data === 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        if (response.data === 'delete'){
                            this.$toast.success('انجام شد', 'نشانه با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.allBookmark.splice(index , 1);
                        }
                    }
                })
                .catch(err =>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    }
}
</script>
