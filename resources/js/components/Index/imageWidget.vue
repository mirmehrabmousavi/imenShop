<template>
    <div class="imageWidget" :style="'background-image: url('+ data.background +')'">
        <div class="imageWidgetBlock width">
            <inertia-link :href="'/archive/products/' + data.slug" class="imageWidgetPic" v-if="JSON.parse(data.title)">
                <img :src="JSON.parse(data.title)[0].image" :alt="JSON.parse(data.title)[0].image">
            </inertia-link>
            <div class="imageWidgetPosts">
                <hooper :settings="hooperSettings">
                    <slide v-for="(item,index) in data.post.data" :key="index">
                        <inertia-link class="imageWidgetItem" :href="'/product/' + item.slug">
                            <div class="offProduct" v-if="item.off && $i18n.locale == 'fa'">٪{{item.off}}</div>
                            <div class="offProduct en" v-if="item.off && $i18n.locale == 'en'">%{{item.off}}</div>
                            <div class="pic">
                                <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                            </div>
                            <div class="postPrice" v-if="$i18n.locale == 'fa'">
                                <s v-if="item.off">{{item.offPrice|NumFormat}}</s>
                                <span>
                                    {{item.price|NumFormat}}
                                    <span>تومان</span>
                                </span>
                            </div>
                            <div class="postPrice en" v-if="$i18n.locale == 'en'">
                                <s v-if="item.off">{{item.offPrice|NumFormat}}</s>
                                <span>
                                    {{item.price|NumFormat}}
                                    <span>toman</span>
                                </span>
                            </div>
                            <div class="postTitle">
                                <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                                <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                            </div>
                            <div class="productOptions">
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
                                <div class="productOption" title="مقایسه کردن" @click.prevent="btnComparison(item.id)">
                                    <i class="active" v-for="values in allComparison" v-if="values == item.id">
                                        <svg-icon :icon="'#chart'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#chart'"></svg-icon>
                                    </i>
                                </div>
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
    name: "imageWidget",
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
            loadingLike: -1,
            allComparison: [],
            i: 0,
        };
    },
    methods:{
        btnComparison(id){
            this.i = 0;
            if (this.allComparison.length <= 7){
                for ( this.i ; this.i <  this.allComparison.length; this.i++) {
                    if (this.allComparison[this.i] == id){
                        this.allComparison.splice(this.i , 1);
                        this.i = 100;
                    }
                }
                if (this.i != 101){
                    this.allComparison.push(id);
                }
                this.i = 0;
            }
            this.$eventHub.emit('allComparisons' , this.allComparison);
        },
        checkLike(item){
            this.like = item;
        },
        btnLike(id,index){
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
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
                .catch(err =>{
                    this.loadingLike = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        getCompares(item){
            this.allComparison = item;
        }
    },
    created: function() {
        this.$eventHub.on('getLike', this.checkLike);
        this.$eventHub.on('allComparisons', this.getCompares);
    },
}
</script>

<style scoped>

</style>
