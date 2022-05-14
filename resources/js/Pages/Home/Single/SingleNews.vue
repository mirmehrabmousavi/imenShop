<template>
    <home-layout>
        <div class="allSingleNews">
            <div class="newsBlog width">
                <div class="right">
                    <div class="blog">
                        <div class="guidSite">
                            <i>
                                <svg-icon :icon="'#location'"></svg-icon>
                            </i>
                            <inertia-link href="/">{{ $t('home') }}</inertia-link>
                            <inertia-link href="/archive/news">{{$t('allNews')}}</inertia-link>
                            <inertia-link  :href="'/news/' + post.slug" v-if="$i18n.locale == 'fa'">{{post.title}}</inertia-link>
                            <inertia-link  :href="'/news/' + post.slug" v-if="$i18n.locale == 'en'" class="en">{{post.titleEn}}</inertia-link>
                        </div>
                        <div class="titleNews">
                            <h1 v-if="$i18n.locale == 'fa'">{{post.title}}</h1>
                            <h1 v-if="$i18n.locale == 'en'" class="en">{{post.titleEn}}</h1>
                        </div>
                        <div class="detailNews">
                            <div class="user">
                                <img v-if="post.user.profile == null" src="/img/user.png" :alt="post.user.name">
                                <img v-else :src="post.user.profile" :alt="post.user.name">
                                <h4>{{post.user.name}}</h4>
                                <h5>{{post.created_at}}</h5>
                            </div>
                            <div class="catNews">
                                <ul>
                                    <li>{{ $t('category') }} :</li>
                                    <li v-for="item in post.category">
                                        <inertia-link :href="'/news/archive/category/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.name}}</inertia-link>
                                        <inertia-link :href="'/news/archive/category/' + item.slug" v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</inertia-link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="newsTime">
                            <svg-icon :icon="'#clock'"></svg-icon>
                            <span>{{$t('studyTime')}} :</span>
                            <span v-if="$i18n.locale == 'fa'">{{post.time}} دقیقه</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{post.time}} min</span>
                        </div>
                        <div class="acceptNews">
                            <h4 class="accept" v-if="post.accept">
                                {{$t('newsConfirmed')}}
                            </h4>
                            <h4 v-else>
                                {{$t('newsWaiting')}}
                            </h4>
                        </div>
                        <div class="singleInfo">
                            <div class="picNews">
                                <img :src="post.image" :alt="post.title">
                            </div>
                            <p v-html="post.body" v-if="$i18n.locale == 'fa'"></p>
                            <p v-html="post.bodyEn" v-if="$i18n.locale == 'en'" class="en"></p>
                        </div>
                        <div class="tagNews" v-if="post.tag.length">
                            <i class="icon-tag"></i>
                            <label>{{$t('tags')}} :</label>
                            <ul>
                                <li v-for="item in post.tag">
                                    <a :href="'/news/archive/tag/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.name}}</a>
                                    <a :href="'/news/archive/tag/' + item.slug" v-if="$i18n.locale == 'en'" class="en">{{item.nameEn}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="left">
                    <div class="item" v-if="related.length">
                        <label>{{$t('relatedNews')}}</label>
                        <ul>
                            <li v-for="item in related">
                                <inertia-link :href="'/news/' + item.slug">
                                    <img :src="item.image" :alt="item.title">
                                    <div class="showInfo">
                                        <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                        <h4 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h4>
                                        <span>{{item.created_at}}</span>
                                    </div>
                                </inertia-link>
                            </li>
                        </ul>
                    </div>
                    <div class="item" v-if="suggest.length">
                        <label>{{$t('offers2')}} :</label>
                        <ul>
                            <li v-for="item in suggest">
                                <inertia-link :href="'/news/' + item.slug">
                                    <img :src="item.image" :alt="item.title">
                                    <div class="showInfo">
                                        <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                        <h4 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h4>
                                        <span>{{item.created_at}}</span>
                                    </div>
                                </inertia-link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "SingleNews",
    props:['post','suggest','related'],
    components: {
        HomeLayout,
        SvgIcon,
    },
}
</script>

<style scoped>

</style>
