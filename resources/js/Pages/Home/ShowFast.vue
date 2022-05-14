<template>
    <div class="showFastContainer" v-if="showFast != null">
        <div class="showFastContainerIndex">
            <div class="showFastContainerPic">
                <zoom-on-hover :img-normal="JSON.parse(showFast.image)[0]" :scale="3"></zoom-on-hover>
            </div>
            <div class="showFastContainerItems">
                <div class="showFastContainerItem">
                    <div class="showFastContainerItemsTitle">
                        <h3 v-if="$i18n.locale == 'fa'">{{showFast.title}}</h3>
                        <h3 v-if="$i18n.locale == 'en'">{{showFast.titleEn}}</h3>
                    </div>
                    <div class="showFastContainerItemsBody">
                        <p v-if="$i18n.locale == 'fa'">{{showFast.body}}</p>
                        <p v-if="$i18n.locale == 'en'">{{showFast.bodyEn}}</p>
                    </div>
                    <div class="postPriceItem" v-if="showFast.count >= 1 & $i18n.locale == 'fa'">
                        <div class="offPrice" v-if="showFast.off != null">
                            <s>{{showFast.offPrice|NumFormat}} تومان</s>
                        </div>
                        <h3>
                            {{showFast.price|NumFormat}}
                            <span>تومان</span>
                        </h3>
                    </div>
                    <div class="postPriceItemEn" v-if="showFast.count >= 1 & $i18n.locale == 'en'">
                        <div class="offPrice" v-if="showFast.off != null">
                            <s>{{showFast.offPrice|NumFormat}} toman</s>
                        </div>
                        <h3>
                            {{showFast.price|NumFormat}}
                            <span>toman</span>
                        </h3>
                    </div>
                    <div class="showFastContainerItemAddCart" v-if="showFast.count >= 1" @click.prevent="addCart(showFast.id)">
                        <i>
                            <svg-icon :icon="'#cart'"></svg-icon>
                        </i>
                        {{$t('addCart')}}
                    </div>
                </div>
                <div class="showFastContainerItem">
                    <div class="showFastContainerPropertiesTitle">
                        <h3>{{$t('productfeatures')}}</h3>
                    </div>
                    <div class="showFastContainerProperties" v-if="showFast.review[0].ability != null">
                        <ul>
                            <li v-for="(item , checks) in JSON.parse(showFast.review[0].ability).slice(0,3)">
                                <span>{{item.name}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="showFastContainerItemCat">
                        <label>{{$t('category')}} :</label>
                        <ul>
                            <li v-for="(item , check) in showFast.category">
                                <inertia-link :href="'/archive/category/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.name}}</inertia-link>
                                <inertia-link :href="'/archive/category/' + item.slug" v-if="$i18n.locale == 'en'">{{item.nameEn}}</inertia-link>
                            </li>
                        </ul>
                    </div>
                    <div class="showFastContainerItemCat" v-if="showFast.brand.length">
                        <label>{{$t('brand')}} :</label>
                        <ul>
                            <li v-for="(item , check) in showFast.brand">
                                <inertia-link :href="'/archive/brand/' + item.slug" v-if="$i18n.locale == 'fa'">{{item.name}}</inertia-link>
                                <inertia-link :href="'/archive/brand/' + item.slug" v-if="$i18n.locale == 'en'">{{item.nameEn}}</inertia-link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="showFastContainerIndexClose" @click.stop="showFast = null">
                <i>
                    <svg-icon :icon="'#cancel'"></svg-icon>
                </i>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../Svg/SvgIcon";
export default {
    name: "ShowFast",
    components: {SvgIcon},
    data(){
        return{
            showFast: null
        }
    },
    methods:{
        getFast(id){
            const url = `/show-fast`;
            axios.post(url ,{
                postId : id
            })
                .then(response=>{
                    this.showFast = response.data
                })
        }
    },
    created: function() {
        this.$eventHub.on('showFast', this.getFast);
    },
}
</script>

<style scoped>

</style>
