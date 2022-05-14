<template>
    <div class="container_shop width">
        <div class="container_shop_right">
            <div class="container_shop_right_item" v-for="item in JSON.parse(data.titleEn)">
                <a :href="item.address">
                    <img :src="item.image" :alt="item.address">
                </a>
            </div>
        </div>
        <div class="container_shop_left">
            <div class="container_shop_left_top">
                <div class="container_shop_left_top_item" v-for="item in JSON.parse(data.title)">
                    <a :href="item.address">
                        <img :src="item.image" :alt="item.address">
                    </a>
                </div>
            </div>
            <div class="container_shop_left_bot" v-if="showSuggests">
                <inertia-link :href="'/product/' + showSuggests.slug" :title="showSuggests.title">
                    <div class="container_shop_suggest">
                        <div class="container_shop_suggest_pic">
                            <span>{{$t('offers')}}</span>
                            <div class="container_shop_suggest_pic_img">
                                <zoom-on-hover :img-normal="JSON.parse(showSuggests.image)[0]" :scale="3"></zoom-on-hover>
                            </div>
                        </div>
                        <div class="container_shop_suggest_explanation">
                            <div class="container_shop_suggest_price" v-if="$i18n.locale == 'fa'">
                                <h3>
                                    {{showSuggests.price|NumFormat}}
                                    <span>تومان</span>
                                </h3>
                                <h5 v-if="showSuggests.off">٪{{showSuggests.off}}</h5>
                                <s v-if="showSuggests.off">{{showSuggests.offPrice|NumFormat}} تومان</s>
                            </div>
                            <div class="container_shop_suggest_price en" v-else>
                                <h3>
                                    {{showSuggests.price|NumFormat}}
                                    <span>toman</span>
                                </h3>
                                <h5 v-if="showSuggests.off">%{{showSuggests.off}}</h5>
                                <s v-if="showSuggests.off">{{showSuggests.offPrice|NumFormat}} toman</s>
                            </div>
                            <h4 v-if="$i18n.locale == 'fa'">{{showSuggests.title}}</h4>
                            <h4 v-if="$i18n.locale == 'en'" class="en">{{showSuggests.titleEn}}</h4>
                            <ul v-if="showSuggests.review[0].ability != ''">
                                <li v-for="value in JSON.parse(showSuggests.review[0].ability).slice(0 , 3)">
                                    <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                                    <span class="en" v-if="$i18n.locale == 'en'">{{value.nameEn}}</span>
                                </li>
                            </ul>
                            <div class="timer" v-if="$i18n.locale == 'fa'">
                                <flip-countdown :deadline="showSuggests.suggest"></flip-countdown>
                            </div>
                            <div class="timer en" v-if="$i18n.locale == 'en'">
                                <flip-countdown :deadline="showSuggests.suggest"></flip-countdown>
                            </div>
                            <h6>{{$t('end')}}</h6>
                        </div>
                    </div>
                </inertia-link>
                <div class="container_shop_list">
                    <ul>
                        <li v-for="item in data.post.data.slice(0,7)" @click="getSuggest(item.id)">
                            <span v-if="$i18n.locale == 'fa'">{{item.title}}</span>
                            <span v-else class="en">{{item.titleEn}}</span>
                            <div class="active" v-if="item.id == showSuggests.id"></div>
                        </li>
                    </ul>
                    <div class="container_shop_list_see_all">
                        <i>
                            <svg-icon :icon="'#left'"></svg-icon>
                        </i>
                        <inertia-link v-if="$i18n.locale == 'fa'" :href="'/archive/products/'+data.slug">{{data.more}}</inertia-link>
                        <inertia-link v-if="$i18n.locale == 'en'" class="en" :href="'/archive/products/'+data.slug">{{data.moreEn}}</inertia-link>
                    </div>
                </div>
                <div class="overLoad" v-if="loading">
                    <svg-icon :icon="'#loading'"></svg-icon>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FlipCountdown from "vue2-flip-countdown";
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "SuggestContainer",
    props:['data'],
    components:{
        SvgIcon,
        FlipCountdown,
    },
    data() {
        return {
            showSuggests: this.data.post.data[0],
            loading: false,
        };
    },
    methods:{
        getSuggest(id) {
            this.loading = true;
            const url = `/get-sugest-index`;
            axios.post(url,{
                postSuggestId : id
            })
                .then(response=>{
                    this.showSuggests = response.data;
                    this.loading = false;
                })
        },
    },
}
</script>

<style scoped>

</style>
