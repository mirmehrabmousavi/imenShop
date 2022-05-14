<template>
    <div :class="'newsIndex ' + widths">
        <div class="title" v-if="top != 1">
            <h3 v-if="$i18n.locale == 'fa'">{{ data.title }}</h3>
            <h3 class="en" v-if="$i18n.locale == 'en'">{{ data.titleEn }}</h3>
            <inertia-link href="/archive/news">
                <svg width="18px" height="17px" viewBox="0 0 18 17" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="prev" transform="translate(8.500000, 8.500000) scale(-1, 1) translate(-8.500000, -8.500000)">
                        <polygon class="arrow" points="16.3746667 8.33860465 7.76133333 15.3067621 6.904 14.3175671 14.2906667 8.34246869 6.908 2.42790698 7.76 1.43613596"></polygon>
                        <polygon class="arrow-fixed" points="16.3746667 8.33860465 7.76133333 15.3067621 6.904 14.3175671 14.2906667 8.34246869 6.908 2.42790698 7.76 1.43613596"></polygon>
                        <path d="M-1.48029737e-15,0.56157424 L-1.48029737e-15,16.1929159 L9.708,8.33860465 L-2.66453526e-15,0.56157424 L-1.48029737e-15,0.56157424 Z M1.33333333,3.30246869 L7.62533333,8.34246869 L1.33333333,13.4327013 L1.33333333,3.30246869 L1.33333333,3.30246869 Z"></path>
                    </g>
                </svg>
                <span v-if="$i18n.locale == 'fa'">{{ data.more }}</span>
                <span v-if="$i18n.locale == 'en'" class="en">{{ data.moreEn }}</span>
            </inertia-link>
        </div>
        <div class="newsIndexItems">
            <div class="right">
                <inertia-link v-for="(item,index) in post.slice(0,2)" :key="index" :href="'/news/'+item.slug" class="newsItem">
                    <img :src="item.image" :alt="item.title">
                    <div class="over">
                        <div class="detail">
                            <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                            <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                            <ul>
                                <li>
                                    <svg-icon :icon="'#edit2'"></svg-icon>
                                    {{ item.user.name }}
                                </li>
                                <li>
                                    <svg-icon :icon="'#clock'"></svg-icon>
                                    {{ item.created_at }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-for="value in item.category.slice(0,1)">
                        <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                        <span v-if="$i18n.locale == 'en'" class="en">{{value.nameEn}}</span>
                    </div>
                </inertia-link>
            </div>
            <div class="left">
                <hooper :settings="hooperSettings">
                    <slide v-for="(item,index) in post.slice(4,10)" :key="index">
                        <inertia-link :href="'/news/'+item.slug" class="newsItem">
                            <img :src="item.image" :alt="item.title">
                            <div class="over">
                                <div class="detail">
                                    <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                                    <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                                    <ul>
                                        <li>
                                            <svg-icon :icon="'#edit2'"></svg-icon>
                                            {{ item.user.name }}
                                        </li>
                                        <li>
                                            <svg-icon :icon="'#clock'"></svg-icon>
                                            {{ item.created_at }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-for="value in item.category.slice(0,1)">
                                <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                                <span v-if="$i18n.locale == 'en'" class="en">{{value.nameEn}}</span>
                            </div>
                        </inertia-link>
                    </slide>
                    <hooper-navigation slot="hooper-addons"></hooper-navigation>
                </hooper>
            </div>
            <div class="right">
                <inertia-link v-for="(item,index) in post.slice(2,4)" :key="index" :href="'/news/'+item.slug" class="newsItem">
                    <img :src="item.image" :alt="item.title">
                    <div class="over">
                        <div class="detail">
                            <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                            <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                            <ul>
                                <li>
                                    <svg-icon :icon="'#edit2'"></svg-icon>
                                    {{ item.user.name }}
                                </li>
                                <li>
                                    <svg-icon :icon="'#clock'"></svg-icon>
                                    {{ item.created_at }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-for="value in item.category.slice(0,1)">
                        <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                        <span v-if="$i18n.locale == 'en'" class="en">{{value.nameEn}}</span>
                    </div>
                </inertia-link>
            </div>
        </div>
    </div>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Slide} from "hooper";
import SvgIcon from "../../Pages/Svg/SvgIcon";
import 'hooper/dist/hooper.css';
export default {
    name: "NewsIndex",
    props: ['data','top','post'],
    components:{
        SvgIcon,
        Hooper,
        HooperNavigation,
        Slide,
    },
    data() {
        return {
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                rtl: true,
                transition: 300,
                itemsToShow: 1,
                autoPlay:true,
                playSpeed : 5000
            },
            widths: 'width'
        };
    },
    mounted() {
        if(this.top == 1){
            this.widths = '';
        }
    }
}
</script>

<style scoped>

</style>
