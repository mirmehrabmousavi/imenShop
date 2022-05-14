<template>
    <home-layout>
        <div class="allSuggestProduct width">
            <div class="allSuggestProductTitle">
                <p>
                    {{ $t('mySuggest') }}
                    <span>پیشنهادات رتبه {{rank.name}}</span>
                    &mdash; {{ $t('belowProduct') }} &mdash;
                </p>
            </div>
            <div class="suggestProducts">
                <div class="suggestProduct" v-for="item in posts">
                    <inertia-link class="suggestProductItem" :href="'/product/' + item.slug">
                        <div class="pic">
                            <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                            <img v-if="JSON.parse(item.image)[1]" :src="JSON.parse(item.image)[1]" :alt="item.title">
                            <img v-else :src="JSON.parse(item.image)[0]" :alt="item.title">
                        </div>
                        <div class="postTitle">
                            <inertia-link v-if="$i18n.locale == 'fa'" :href="'/product/' + item.slug">{{item.title}}</inertia-link>
                            <inertia-link v-if="$i18n.locale == 'en'" class="en" :href="'/product/' + item.slug">{{item.titleEn}}</inertia-link>
                        </div>
                        <div class="postPrice" v-if="item.count >= 1">
                            <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                <s>{{item.offPrice|NumFormat}} تومان</s>
                                <h3>
                                    {{(item.offPrice - item.offPrice * rank.off / 100)|NumFormat}}
                                    <span>تومان</span>
                                </h3>
                            </div>
                            <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                <s>{{item.offPrice|NumFormat}} toman</s>
                                <h3>
                                    {{(item.offPrice - item.offPrice * rank.off / 100)|NumFormat}}
                                    <span>tooman</span>
                                </h3>
                            </div>
                        </div>
                        <div class="checkCount" v-else>
                            <span>{{$t('notAvailable')}}</span>
                        </div>
                        <div class="productOption" title="علاقه مندی" @click.prevent="btnLike(item.id , index)">
                            <i v-if="loadingLike == index">
                                <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                            </i>
                            <i v-for="values in like" v-if="values == item.id && loadingLike != index">
                                <svg-icon :icon="'#like'"></svg-icon>
                            </i>
                            <i>
                                <svg-icon :icon="'#unlike'"></svg-icon>
                            </i>
                        </div>
                    </inertia-link>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
export default {
    name: "SuggestProduct",
    props:['posts','rank'],
    components: {HomeLayout}
}
</script>

<style scoped>

</style>
