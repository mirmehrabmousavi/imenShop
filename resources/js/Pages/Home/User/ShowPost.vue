<template>
    <home-layout>
        <div class="allUserIndex width">
            <user-list></user-list>
            <div class="allShowPostPanel">
                <div class="allShowPostPanelTop">
                    <div class="allShowPostPanelPic">
                        <img :src="JSON.parse(posts.image)[0]">
                    </div>
                    <div class="allShowPostPanelTopItems">
                        <div class="allShowPostPanelTopTitle">
                            <h1>{{posts.title}}</h1>
                        </div>
                        <div class="postPrice" v-if="posts.count >= 1">
                            <div class="postPriceItem">
                                <div class="offPrice" v-if="posts.off != null">
                                    <s>{{posts.offPrice|NumFormat}} تومان</s>
                                </div>
                                <h3>
                                    {{posts.price|NumFormat}}
                                    <span>تومان</span>
                                </h3>
                            </div>
                        </div>
                        <div class="checkCount" v-else>
                            <span>ناموجود</span>
                        </div>
                        <div class="allShowPostPanelTopInfo">
                            <div class="allShowPostPanelTopItem">
                                <label>امتیاز : </label>
                                <span>{{allRate}}</span>
                            </div>
                            <div class="allShowPostPanelTopItem">
                                <label>تعداد دیدگاه : </label>
                                <span>{{posts.comments_count|NumFormat}}</span>
                            </div>
                            <div class="allShowPostPanelTopItem">
                                <label>تعداد بازدید : </label>
                                <span>{{posts.view_count|NumFormat}}</span>
                            </div>
                            <div class="allShowPostPanelTopItem">
                                <label>تعداد علاقه مندی : </label>
                                <span>{{posts.like_count|NumFormat}}</span>
                            </div>
                            <div class="allShowPostPanelTopItem">
                                <label>تعداد نشانه ها : </label>
                                <span>{{posts.bookmark_count|NumFormat}}</span>
                            </div>
                            <div class="allShowPostPanelTopItem">
                                <label>کل فروش : </label>
                                <span>{{allPay|NumFormat}}تومان</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allShowPostPanelComment">
                    <all-comment :posts="posts" :rate="JSON.parse(posts.review[0].rate)" :replyAllow="reply" :showUser="showUser" :coercion="coercion" :checkOnline="checkOnline"></all-comment>
                </div>
                <div class="allShowPostPanelPays">
                    <label>خریداران</label>
                    <div class="allTableContainer">
                        <div class="paginate">
                            <paginate-panel :link="payMeta.links"></paginate-panel>
                        </div>
                        <table>
                            <tr>
                                <div>
                                    <th>#</th>
                                    <th>نام خریدار</th>
                                    <th>رنگ محصول</th>
                                    <th>سایز محصول</th>
                                    <th>تعداد سفارش</th>
                                    <th>وضعیت پرداخت</th>
                                    <th>زمان خرید</th>
                                </div>
                            </tr>
                            <tr v-for="(item , index) in payMeta.data">
                                <div>
                                    <td>
                                        <span>{{++index}}</span>
                                    </td>
                                    <td>
                                        <span>{{item.user.name}}</span>
                                    </td>
                                    <td>
                                        <span v-if="item.color">{{JSON.parse(item.color).name}}</span>
                                    </td>
                                    <td>
                                        <span v-if="item.size">{{JSON.parse(item.size).name}}</span>
                                    </td>
                                    <td>
                                        <span>{{item.count}}</span>
                                    </td>
                                    <td>
                                        <span>
                                            <span class="activeStatus" v-if="item.pay.status == 100">پرداخت شده</span>
                                            <span class="unActiveStatus" v-else>پرداخت نشده</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span>{{item.created_at}}</span>
                                    </td>
                                </div>
                            </tr>
                        </table>
                        <div class="paginate">
                            <paginate-panel :link="payMeta.links"></paginate-panel>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../../Admin/PaginatePanel";
import AllComment from "../../../components/Single/AllComment";
import UserList from "./UserList";
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "ShowProduct",
    props: ['posts','reply','allPay','allow','userData','coercion','showUser','checkOnline' , 'payMeta'],
    components: {
        HomeLayout,
        UserList,
        PaginatePanel,
        SvgIcon,
        AllComment,
    },
    data(){
        return{
            allRate: '',
            rate: [],
            allows: [],
        }
    },
    metaInfo: {
        title: 'نمایش پست'
    },
    methods:{
        getRate(){
            const url = '/get-rate';
            axios.post(url ,{
                post_id : this.posts.id,
            })
                .then(response=>{
                    this.allRate = response.data[1];
                })
        },
    },
    mounted() {
        this.getRate();
    }
}
</script>

<style scoped>

</style>
