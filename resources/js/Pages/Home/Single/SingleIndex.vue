<template>
    <home-layout>
        <div class="allSingleIndex width">
            <div class="allSingleItems" v-for="(item,index) in vidgets" :key="index">
                <div class="allSingleItem" v-if="item.name == 'اطلاعات محصول 2'">
                    <single-top2 :posts="posts" :finalPrice="finalPrice" :notification="notification" :feedback="feedback"></single-top2>
                    <single-variety :post="posts.post"></single-variety>
                </div>
                <div class="allSingleItem" v-if="item.name == 'اطلاعات محصول 1'">
                    <single-top1 :posts="posts" :finalPrice="finalPrice" :notification="notification" :feedback="feedback"></single-top1>
                    <single-variety :post="posts.post"></single-variety>
                </div>
                <div class="allSingleItem" v-if="item.name == 'محصولات مرتبط 2'">
                    <single-rel2 :related="related"></single-rel2>
                </div>
                <div class="allSingleItem" v-if="item.name == 'محصولات مرتبط 1'">
                    <single-rel :related="related"></single-rel>
                </div>
                <div class="allSingleItem" v-if="item.name == 'توضیحات 1'">
                    <single-detail :posts="posts" :checkOnline="checkOnline" :coercion="coercion" :showUser="showUser" :reply="reply" :show="show"></single-detail>
                </div>
                <div class="allSingleItem" v-if="item.name == 'توضیحات 2'">
                    <single-detail2 :posts="posts" :checkOnline="checkOnline" :coercion="coercion" :showUser="showUser" :reply="reply" :show="show"></single-detail2>
                </div>
            </div>
        </div>
        <div class="showSingleItem">
            <show-fast></show-fast>
        </div>
        <div class="showSingleItem">
            <all-compare></all-compare>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import ShowFast from "../ShowFast";
import AllCompare from "../AllCompare";
import SingleRel from "../../../components/Single/SingleRel";
import SingleTop1 from "../../../components/Single/SingleTop1";
import SingleTop2 from "../../../components/Single/SingleTop2";
import SingleRel2 from "../../../components/Single/SingleRel2";
import SingleDetail from "../../../components/Single/SingleDetail";
import SingleDetail2 from "../../../components/Single/SingleDetail2";
import SingleVariety from "../../../components/Single/SingleVariety";
export default {
    name: "SingleIndex",
    props : ['posts' , 'show' , 'name' , 'finalPrice' , 'feedback' , 'vidgets' , 'notification' , 'address' , 'reply' , 'title' , 'showUser' , 'coercion' , 'checkOnline' , 'related'],
    components:{
        SingleVariety,
        SingleDetail2,
        SingleDetail,
        SingleRel2,
        SingleTop2,
        SingleTop1,
        SingleRel,
        AllCompare,
        ShowFast,
        HomeLayout,
        SvgIcon,
    },
    metaInfo() {
        return {
            title: `${this.posts.title} - ${this.title}`,
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
        allViews(){
            const url = "/view";
            axios.post(url,{
                postId : this.posts.id
            })
        },
    },
    mounted() {
        this.allViews();
    },
}
</script>

<style scoped>

</style>
