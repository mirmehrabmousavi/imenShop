<template>
    <home-layout>
        <div class="allNews width">
            <div class="allNewsRight">
                <news-index :top="1" :post="accept"></news-index>
                <div class="allArchiveIndexDataPostsPaginate">
                    <paginate-panel :link="news.links"></paginate-panel>
                </div>
                <div class="allNewsRightItems">
                    <a class="allNewsRightItem" v-for="(item,index) in news.data" :href="'/news/' + item.slug" :key="index" :title="item.title">
                        <img :src="item.image" :alt="item.title">
                        <div class="allNewsRightItemOver">
                            <span class="active" v-if="item.accept">{{$t('accepted')}}</span>
                            <span v-else>{{$t('awaitingApproval')}}</span>
                            <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                            <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                        </div>
                    </a>
                </div>
                <div class="allArchiveIndexDataPostsPaginate">
                    <paginate-panel :link="news.links"></paginate-panel>
                </div>
            </div>
            <div class="allNewsSideBar">
                <div class="allNewsSideBarItem" v-if="suggest.length">
                    <label>{{$t('offers2')}} :</label>
                    <ul>
                        <li v-for="item in suggest">
                            <a :href="'/news/' + item.slug">
                                <img :src="item.image" alt="">
                                <div class="showInfo">
                                    <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                    <h4 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h4>
                                    <span>{{item.created_at}}</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import NewsIndex from "../../../components/Index/NewsIndex";
import PaginatePanel from "../../Admin/PaginatePanel";
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "AllNews",
    props: ['news','suggest','titleSite','url','accept'],
    components: {
        HomeLayout,
        PaginatePanel,
        NewsIndex,
    },
}
</script>

<style scoped>

</style>
