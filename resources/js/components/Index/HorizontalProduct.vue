<template>
    <div class="allHorizontalProduct width">
        <div class="allHorizontalProductTitle">
            <h3 v-if="$i18n.locale == 'fa'">{{ data.title }}</h3>
            <h3 v-if="$i18n.locale == 'en'">{{ data.titleEn }}</h3>
        </div>
        <div class="allHorizontalProducts">
            <inertia-link class="allHorizontalProductItem" v-for="(item,index) in data.post.data" :href="'/product/' + item.slug" :key="index">
                <div class="allHorizontalProductItemTop">
                    <div class="allHorizontalImage">
                        <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                    </div>
                    <div class="allHorizontalText">
                        <h3 v-if="$i18n.locale == 'fa'">{{ item.title}}</h3>
                        <h3 v-if="$i18n.locale == 'en'" class="en">{{ item.titleEn}}</h3>
                        <div class="allHorizontalOption">
                            <h5 v-if="$i18n.locale == 'fa'">
                                {{ item.price|NumFormat }}
                                <span>تومان</span>
                            </h5>
                            <h5 v-if="$i18n.locale == 'en'" class="en">
                                {{ item.price|NumFormat }}
                                <span>tooman</span>
                            </h5>
                            <div class="allHorizontalLike" @click.prevent="btnLike(item.id , index)">
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
                        </div>
                    </div>
                </div>
                <div class="allHorizontalData">
                    <div class="allHorizontalDataTitle">
                        <h4>{{$t('productSales')}}</h4>
                        <span v-if="$i18n.locale == 'fa'">{{item.count}} {{$t('exist')}}</span>
                        <span v-if="$i18n.locale == 'en'" class="en"> {{item.count}}{{$t('exist')}} </span>
                    </div>
                    <div class="allHorizontalDatas">
                        <div class="allHorizontalDataItem" :style="{'width' : (data.pay[index] * '100') / (data.pay[index] + item.count) +'%'}">
                            <div></div>
                        </div>
                    </div>
                </div>
            </inertia-link>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "HorizontalProduct",
    props:['data'],
    components: {SvgIcon},
    data() {
        return {
            like: [],
            loadingLike: -1,
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
        };
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
