<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="3"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <div class="allUserIndexInfoLike">
                    <label>{{$t('favorites')}}</label>
                    <div class="paginate" v-if="allLike.links">
                        <paginate-panel :link="allLike.links"></paginate-panel>
                    </div>
                    <div class="allUserIndexInfoLikeItem">
                        <ul>
                            <li v-for="(item , index) in allLike.data">
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
                                <i @click.stop="btnLike(item.id , index)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </li>
                        </ul>
                    </div>
                    <div class="paginate" v-if="allLike.links">
                        <paginate-panel :link="allLike.links"></paginate-panel>
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
import PaginatePanel from "../../Admin/PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import UserList from "./UserList";
export default {
    name: "LikeUser",
    props: ['likePosts','title'],
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    metaInfo() {
        return {
            title: `علاقه مندی های من - ${this.title}`,
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
            allLike : this.likePosts,
        }
    },
    methods:{
        btnLike(id , index){
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data === 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        if (response.data === 'delete'){
                            this.$toast.success('انجام شد', 'علاقه مندی با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.allLike.splice(index , 1);
                        }
                    }
                })
        },
    }
}
</script>
