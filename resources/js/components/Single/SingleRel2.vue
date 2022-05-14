<template>
    <div class="allSingleHomeRel2" v-if="related.length">
        <div class="title">
            <label>{{$t('relatedProducts')}}</label>
        </div>
        <hooper :settings="hooperSettings3">
            <slide v-for="(item , index) in related" :key="index" :title="item.title">
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
                            <s>{{item.offPrice|NumFormat}} تومان</s>
                            <h3>
                                {{item.price|NumFormat}}
                                <span>تومان</span>
                            </h3>
                        </div>
                        <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                            <s>{{item.offPrice|NumFormat}} toman</s>
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
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
import {Hooper, Navigation as HooperNavigation, Slide} from "hooper";

export default {
    name: "SingleRel2",
    props: ['related'],
    components:{
        SvgIcon,
        Hooper,
        HooperNavigation,
        Slide,
    },
    data(){
        return{
            loadingLike: -1,
            like : [],
            hooperSettings3: {
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
                }
            },
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
        }
    },
    methods:{
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
