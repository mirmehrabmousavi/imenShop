<template>
    <admin-layout>
        <div class="allShowPostPanel">
            <div class="allShowPostPanelTopTitle">
                <h1>داشبورد</h1>
                <div class="allShowPostPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/post">پست ها</inertia-link>
                    <span>/</span>
                    <inertia-link :href="'/product/' + posts.slug">{{posts.title}}</inertia-link>
                </div>
            </div>
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
                            <h3>{{posts.price|NumFormat}} تومان</h3>
                        </div>
                    </div>
                    <div class="checkCount" v-else>
                        <span>نا موجود</span>
                    </div>
                    <div class="allShowPostPanelTopInfo">
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#star'"></svg-icon>
                            <label>امتیاز : </label>
                            <span>{{allRate}}</span>
                        </div>
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#comment'"></svg-icon>
                            <label>تعداد دیدگاه ها : </label>
                            <span>{{posts.comments_count|NumFormat}}</span>
                        </div>
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#eye'"></svg-icon>
                            <label>تعداد بازدید ها : </label>
                            <span>{{posts.view_count|NumFormat}}</span>
                        </div>
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#unlike'"></svg-icon>
                            <label>تعداد علاقه مندی ها : </label>
                            <span>{{posts.like_count|NumFormat}}</span>
                        </div>
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#unbookmark'"></svg-icon>
                            <label>تعداد نشانه ها : </label>
                            <span>{{posts.bookmark_count|NumFormat}}</span>
                        </div>
                        <div class="allShowPostPanelTopItem">
                            <svg-icon :icon="'#bill'"></svg-icon>
                            <label>تعداد خریدار : </label>
                            <span>{{posts.pay_meta_count|NumFormat}}</span>
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
                                <th>نام خریدار</th>
                                <th>رنگ</th>
                                <th>سایز</th>
                                <th>گارانتی</th>
                                <th>تعداد</th>
                                <th>وضعیت پرداخت</th>
                                <th>تاریخ ثبت</th>
                            </div>
                        </tr>
                        <tr v-for="(item , index) in payMeta.data">
                            <div>
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
                                    <span>{{item.guarantee[0].name}}</span>
                                </td>
                                <td>
                                    <span>{{item.count}}</span>
                                </td>
                                <td>
                                    <span>
                                        <span class="activeStatus" v-if="item.pay.status == 100">پرداخت شده</span>
                                        <span class="unActive" v-else>پرداخت نشده</span>
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
    </admin-layout>
</template>

<script>
    import AdminLayout from "../../../components/layout/AdminLayout";
    import SvgIcon from "../../Svg/SvgIcon";
    import PaginatePanel from "../PaginatePanel";
    import AllComment from "../../../components/Single/AllComment";
    export default {
        name: "ShowPost",
        props: ['posts','reply','allow','userData','coercion','showUser','checkOnline' , 'payMeta'],
        components: {
            PaginatePanel,
            SvgIcon,
            AdminLayout,
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
            sidebar(){
                this.$eventHub.emit('sidebar' , '6');
            },
        },
        mounted() {
            this.getRate();
            this.sidebar();
        }
    }
</script>

<style scoped>

</style>
