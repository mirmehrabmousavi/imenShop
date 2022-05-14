<template>
    <div class="allFileIndex width">
        <div class="allFileTitle">
            <h3 v-if="$i18n.locale == 'fa'">{{data.title}}</h3>
            <h3 class="en" v-if="$i18n.locale == 'en'">{{data.titleEn}}</h3>
            <div class="moreProduct">
                <inertia-link :href="'/archive/products/'+data.slug" v-if="$i18n.locale == 'fa'">{{ data.more }}</inertia-link>
                <inertia-link :href="'/archive/products/'+data.slug" v-if="$i18n.locale == 'en'">{{ data.moreEn }}</inertia-link>
            </div>
        </div>
        <hooper :settings="hooperSettings">
            <slide v-for="(item,index) in data.post" :key="index">
                <inertia-link class="fileItem" :href="'/download-product/' + item.slug" :title="item.title">
                    <div class="pic">
                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                    </div>
                    <div class="titleTop">
                        <i>
                            <svg-icon :icon="'#link'"></svg-icon>
                        </i>
                        <div class="title" v-if="$i18n.locale == 'fa'">
                            <h3>{{ item.title }}</h3>
                            <h4 v-if="item.category.length">{{ item.category[0].name }}</h4>
                        </div>
                        <div class="title" v-if="$i18n.locale == 'en'">
                            <h3>{{ item.titleEn }}</h3>
                            <h4 v-if="item.category.length">{{ item.category[0].nameEn }}</h4>
                        </div>
                    </div>
                    <p v-if="$i18n.locale == 'fa'">{{item.body}}</p>
                    <p v-if="$i18n.locale == 'en'">{{item.bodyEn}}</p>
                    <div class="options">
                        <div class="option">
                            <span v-if="$i18n.locale == 'fa'">{{ item.price|NumFormat }}</span>
                            <span v-if="$i18n.locale == 'en'" class="en">{{ item.price|NumFormat }}</span>
                            <span>{{ $t('arz') }}</span>
                        </div>
                        <div class="option">
                            <i>
                                <svg-icon :icon="'#left'"></svg-icon>
                            </i>
                            <h4 v-if="$i18n.locale == 'fa'">اطلاعات بیشتر</h4>
                            <h4 v-if="$i18n.locale == 'en'">more info</h4>
                        </div>
                    </div>
                </inertia-link>
            </slide>
            <hooper-navigation slot="hooper-addons"></hooper-navigation>
        </hooper>
    </div>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Slide} from "hooper";
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "FileIndex",
    props: ['data'],
    components:{
        Hooper,
        HooperNavigation,
        Slide,
        SvgIcon,
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                transition: 700,
                breakpoints: {
                    100: {
                        itemsToShow: 1,
                        itemsToSlide: 1,
                    },
                    700: {
                        itemsToShow: 2,
                        itemsToSlide: 1,
                    },
                    1000: {
                        itemsToShow: 3,
                        itemsToSlide: 1,
                    },
                    1200: {
                        itemsToShow: 4,
                        itemsToSlide: 1,
                    },
                }
            },
        }
    }
}
</script>

<style scoped>

</style>
