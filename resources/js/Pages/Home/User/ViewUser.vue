<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="7"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <label>{{$t('recentVisits')}}</label>
                <div class="paginate" v-if="views.links">
                    <paginate-panel :link="views.links"></paginate-panel>
                </div>
                <div class="allUserIndexInfoViews">
                    <div class="allProductArchiveContainerPosts">
                        <div class="allProductArchiveContainerPost" v-for="(item,index) in views.data">
                            <inertia-link :href="'/product/' + item.slug">
                                <div class="offProduct" v-if="item.off != null & $i18n.locale == 'fa'">
                                    <span>
                                        ٪
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="offProduct en" v-if="item.off != null & $i18n.locale == 'en'">
                                    <span>
                                        %
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="allProductArchiveContainerPostPic">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                </div>
                                <div class="allProductArchiveContainerPostTitle">
                                    <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                    <h4 v-if="$i18n.locale == 'en'">{{item.titleEn}}</h4>
                                </div>
                                <ul v-if="item.review[0].ability != ''">
                                    <li v-for="value in JSON.parse(item.review[0].ability).slice(0 , 3)">
                                        <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{value.nameEn}}</span>
                                    </li>
                                </ul>
                                <div class="postPrice" v-if="item.count >= 1">
                                    <div class="postPriceItem" @click.prevent="addCart(item.id , index)">
                                        <i v-if="loadingAdd == index">
                                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                        </i>
                                        <i v-if="loadingAdd != index">
                                            <svg-icon :icon="'#plus'"></svg-icon>
                                        </i>
                                    </div>
                                    <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} تومان</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>تومان</span>
                                        </h3>
                                    </div>
                                    <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} toman</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>toman</span>
                                        </h3>
                                    </div>
                                </div>
                                <div class="checkCount" v-else>
                                    <span>{{$t('notAvailable')}}</span>
                                </div>
                            </inertia-link>
                        </div>
                    </div>
                </div>
                <div class="paginate" v-if="views.links">
                    <paginate-panel :link="views.links"></paginate-panel>
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
    name: "ViewUser",
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    props: ['views','title'],
    data() {
        return {
            loadingAdd : -1,
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
    metaInfo() {
        return {
            title: `بازدید های اخیر - ${this.title}`,
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
    methods:{
        addCart(id , index){
            this.loadingAdd = index;
            const url = `/add-cart`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data === 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                    this.loadingAdd = -1;
                })
                .catch(err =>{
                    this.loadingAdd = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    }
}
</script>

