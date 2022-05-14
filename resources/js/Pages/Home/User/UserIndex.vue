<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="0"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <div class="myRank" v-if="$page.myRank">
                    <div class="right">
                        <h4>{{$t('myRank')}} {{$page.myRank.name}}</h4>
                        <p>{{$t('myRankP')}}</p>
                    </div>
                    <div class="left">
                        <i>
                            <svg-icon :icon="'#trophy'"></svg-icon>
                        </i>
                        <a href="/rank/suggest">{{$t('mySuggest')}}</a>
                    </div>
                </div>
                <div class="allUserIndexInfoTop">
                    <div class="allUserIndexInfoTopItem">
                        <label>{{$t('latestFavorites')}}</label>
                        <ul>
                            <li v-for="(item , index) in allLike.slice(0,3)">
                                <inertia-link :href="'/product/' + item.slug">
                                    <div class="userItemPic">
                                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                    </div>
                                    <div class="userItemSubject">
                                        <div class="userItemSubjectTitle" v-if="$i18n.locale == 'fa'">{{item.title}}</div>
                                        <div class="userItemSubjectTitle en" v-if="$i18n.locale == 'en'">{{item.titleEn}}</div>
                                        <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.offPrice|NumFormat}} تومان</s>
                                            </div>
                                            <h3>{{item.price|NumFormat}} تومان</h3>
                                        </div>
                                        <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.offPrice|NumFormat}} toman</s>
                                            </div>
                                            <h3>{{item.price|NumFormat}} toman</h3>
                                        </div>
                                    </div>
                                </inertia-link>
                                <i @click.stop="btnLike(item.id , index)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </li>
                        </ul>
                        <div class="allUserIndexInfoTopItemAddress">
                            <inertia-link href="/profile/like">{{$t('editWishlist')}}</inertia-link>
                        </div>
                    </div>
                    <div class="allUserIndexInfoTopItem">
                        <label>{{$t('lastBookmarks')}}</label>
                        <ul>
                            <li v-for="(item , index) in allBookmark.slice(0,3)">
                                <inertia-link :href="'/product/' + item.slug">
                                    <div class="userItemPic">
                                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                    </div>
                                    <div class="userItemSubject">
                                        <div class="userItemSubjectTitle" v-if="$i18n.locale == 'fa'">{{item.title}}</div>
                                        <div class="userItemSubjectTitle en" v-if="$i18n.locale == 'en'">{{item.titleEn}}</div>
                                        <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.offPrice|NumFormat}} تومان</s>
                                            </div>
                                            <h3>{{item.price|NumFormat}} تومان</h3>
                                        </div>
                                        <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                            <div class="offPrice" v-if="item.off != null">
                                                <s>{{item.offPrice|NumFormat}} toman</s>
                                            </div>
                                            <h3>{{item.price|NumFormat}} toman</h3>
                                        </div>
                                    </div>
                                </inertia-link>
                                <i @click.stop="btnBookmark(item.id , index)">
                                    <svg-icon :icon="'#trash'"></svg-icon>
                                </i>
                            </li>
                        </ul>
                        <div class="allUserIndexInfoTopItemAddress">
                            <inertia-link href="/profile/bookmark">{{$t('editBookmarks')}}</inertia-link>
                        </div>
                    </div>
                </div>

                <label>{{$t('latestOrders')}}</label>

                <div class="allUserIndexInfoPay">
                    <table>
                        <tr>
                            <th>#</th>
                            <th>{{$t('postStatus')}}</th>
                            <th>{{$t('orderNumber')}}</th>
                            <th>{{$t('paymentStatus')}}</th>
                            <th>{{$t('orderDate')}}</th>
                            <th>{{$t('theOperation')}}</th>
                        </tr>
                        <div class="showTr">
                            <tr v-for="(item , index) in pays">
                                <td>
                                    <span :class="$i18n.locale">{{++index}}</span>
                                </td>
                                <td>
                                    <span class="unActive" v-if="item.deliver == 0">{{$t('takingOrders')}}</span>
                                    <span class="unActive" v-if="item.deliver == 1">{{ $t('awaitingReview') }}</span>
                                    <span class="unActive" v-if="item.deliver == 2">{{$t('packaged')}}</span>
                                    <span class="unActive" v-if="item.deliver == 3">{{$t('courierDelivery')}}</span>
                                    <span class="active" v-if="item.deliver == 4">{{$t('completed')}}</span>
                                </td>
                                <td>
                                    <span :class="$i18n.locale">{{item.property}}</span>
                                </td>
                                <td>
                                    <span class="active" v-if="item.status == 100">{{$t('paid')}}</span>
                                    <span class="active" v-else-if="item.status == 50">{{$t('buyDeposit')}}</span>
                                    <span class="unActive" v-else>{{$t('unpaid')}}</span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                                <td>
                                    <inertia-link :href="'/show-pay/'+item.property">
                                        <svg-icon :icon="'#left'"></svg-icon>
                                    </inertia-link>
                                </td>
                            </tr>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import UserList from "./UserList";
export default {
    name: "UserIndex",
    props: ['likePost','bookmarkPost','pays','title'],
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
    },
    data() {
        return {
            showPayMeta : [],
            showLoader : false,
            allLike : this.likePost,
            allBookmark : this.bookmarkPost,
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
            title: `حساب کاربری - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                amp: true,
                reptilian: 'gator'
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
        btnShow(id){
            this.showLoader = true;
            const url = `/pay/${id}`;
            axios.get(url)
                .then(response=>{
                    this.showPayMeta = response.data;
                    this.showLoader = false;
                })
        },
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
                        }else{
                            this.$toast.success('انجام شد', 'نشانه با موفقیت اضافه شد', this.notificationSystem.options.success);
                        }
                    }
                })
        },
        btnLike(id , index){
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data === 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.like = [];
                    }else{
                        if (response.data === 'delete'){
                            this.$toast.success('انجام شد', 'علاقه مندی با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.allLike.splice(index , 1);
                        }else{
                            this.$toast.success('انجام شد', 'به علاقه مندی با موفقیت اضافه شد', this.notificationSystem.options.success);
                        }
                    }
                })
        },
    }
}
</script>
