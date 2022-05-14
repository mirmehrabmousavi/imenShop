<template>
    <div class="allBackProduct">
        <div class="allBackProductBlocks" :style="'background-image: url('+ data.background +')'">
            <div class="allBackProductBlock width">
                <div class="allBackProductTitle">
                    <h3 v-if="$i18n.locale == 'fa'">{{ data.title }}</h3>
                    <h3 v-if="$i18n.locale == 'en'">{{ data.titleEn }}</h3>
                    <p v-if="$i18n.locale == 'fa'">
                        {{$t('widget1')}}
                        <inertia-link :href="'/archive/products/'+data.slug">{{data.title}}</inertia-link>
                        {{$t('widget2')}}
                    </p>
                    <p v-if="$i18n.locale == 'en'">
                        {{$t('widget2')}}
                        <inertia-link class="en" :href="'/archive/products/'+data.slug">{{data.titleEn}}</inertia-link>
                        {{$t('widget1')}}
                    </p>
                </div>
                <hooper :settings="hooperSettings">
                    <slide v-for="(item , index) in data.post.data" :key="index" :title="item.title">
                        <inertia-link :href="'/product/' + item.slug">
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
                                    <s v-if="item.off">{{item.offPrice|NumFormat}} تومان</s>
                                    <h3>
                                        {{item.price|NumFormat}}
                                        <span>تومان</span>
                                    </h3>
                                </div>
                                <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                    <s v-if="item.off">{{item.offPrice|NumFormat}} toman</s>
                                    <h3>
                                        {{item.price|NumFormat}}
                                        <span>toman</span>
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
                    </slide>
                    <hooper-navigation slot="hooper-addons"></hooper-navigation>
                </hooper>
            </div>
        </div>
    </div>
</template>

<script>
import {Hooper, Navigation as HooperNavigation, Slide} from "hooper";
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "BackProduct",
    props:['data'],
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
                        itemsToShow: 3,
                        itemsToSlide: 1,
                    },
                    1000: {
                        itemsToShow: 4,
                        itemsToSlide: 1,
                    },
                    1200: {
                        itemsToShow: 5,
                        itemsToSlide: 1,
                    },
                    1600: {
                        itemsToShow: 6,
                        itemsToSlide: 1,
                    },
                }
            },
            like: [],
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
            loadingLike: -1
        };
    },
    methods:{
        checkLike(item){
            this.like = item;
        },
        btnLike(id , index){
            this.loadingLike = index;
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.$eventHub.emit('getLike' , []);
                    }else{
                        if (response.data == 'delete'){
                            this.$toast.success('انجام شد', 'علاقه مندی با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.$eventHub.emit('allLike');
                        }else{
                            this.$toast.success('انجام شد', 'به علاقه مندی با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.like.push(response.data.post_id);
                            this.$eventHub.emit('getLike' , this.like);
                        }
                    }
                    this.loadingLike = -1;
                })
                .catch(err =>{
                    this.loadingLike = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    },
    created: function() {
        this.$eventHub.on('getLike', this.checkLike);
    },
}
</script>

<style scoped>

</style>
