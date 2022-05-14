<template>
    <div class="allSingleVar" v-if="post.length">
        <div class="allVarieties">
            <div class="allVarietiesTitle">
                <span>لیست فروشندگان</span>
            </div>
            <ul>
                <li v-for="(item,index) in post">
                    <div class="sellerProfile">
                        <img v-if="item.user.profile" :src="item.user.profile" :alt="item.user.name">
                        <img v-else src="/img/user.png" :alt="item.user.name">
                        <span>{{item.user.name}}</span>
                    </div>
                    <div class="sellerSize" v-if="JSON.parse(item.review[0].colors).length">
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="btnColorChange(index)">
                                <h4>{{sellerColors[index].name}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showColorsSeller == index">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li v-for="item in JSON.parse(item.review[0].colors)" v-if="item.count >= 0" :key="item.name" :title="item.name" @click.prevent="btnColorSeller(item,index)">
                                        <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                    </li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                    </div>
                    <div class="sellerSize" v-if="JSON.parse(item.review[0].size).length">
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="btnShowSizeSeller(index)">
                                <h4>{{sellerSizes[index].name}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showSizeSeller == index">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li v-for="item in JSON.parse(item.review[0].size)" v-if="item.count >= 0" :key="item.name" :title="item.name" @click.prevent="btnSizeSeller(item , index)">
                                        <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                    </li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                    </div>
                    <div class="sellerSize" v-if="item.guarantee.length">
                        <div class="allCategoryPanel">
                            <div class="categoryShow" @click="btnShowGuaranteeSeller(index)">
                                <h4>{{sellerGuarantees[index].name}}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showGuaranteeSeller == index">
                                <VuePerfectScrollbar class="scroll-area">
                                    <li v-for="item in item.guarantee" :key="item.name" :title="item.name" @click.prevent="btnGuaranteeSeller(item,index)">
                                        <span v-if="$i18n.locale == 'fa'">{{item.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</span>
                                    </li>
                                </VuePerfectScrollbar>
                            </ul>
                        </div>
                    </div>
                    <div class="sellerPrice">{{sellerPrice[index]|NumFormat}} تومان</div>
                    <div class="sellerAdd">
                        <button v-if="sellerCount[index] >= 1" @click="btnAddSeller(item.id , index)">
                            <svg-icon class="loading" v-if="showLoaderSeller == index" :icon="'#loading'"></svg-icon>
                            <span v-else>افزودن به سبد خرید</span>
                        </button>
                        <button v-else>ناموجود</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name: "SingleVariety",
    props:['post'],
    components: { SvgIcon ,VuePerfectScrollbar},
    data(){
        return{
            showSizeSeller: -1,
            showColorsSeller: -1,
            showGuaranteeSeller: -1,
            sellerColors: [],
            sellerSizes: [],
            sellerGuarantees: [],
            sellerPrice: [],
            sellerCount: [],
            showLoaderSeller: -1,
            showSort: false,
            i: 0,
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
    methods: {
        btnAddSeller(id , index){
            this.showLoaderSeller = index;
            const url = `/add-cart2`;
            axios.post(url ,{
                postID : id,
                colorName : this.sellerColors[index],
                sizeName : this.sellerSizes[index],
                price: this.sellerPrice[index],
                guarantee: this.sellerGuarantees[index].id,
            })
                .then(response=>{
                    this.showLoaderSeller = -1;
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data == 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                })
                .catch(err =>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        btnGuaranteeSeller(item,index){
            this.sellerGuarantees[index] = item;
            this.showGuaranteeSeller = -1;
        },
        btnColorSeller(item,index){
            this.showColorsSeller = -1;
            this.sellerColors[index] = item;
            if (this.sellerSizes[index]){
                this.sellerPrice[index] = parseInt(this.post[index].price) + parseInt(this.sellerSizes[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.sellerSizes[index].count <= 0 || this.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }else{
                this.sellerPrice[index] = parseInt(this.post[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }
        },
        btnSizeSeller(item,index){
            this.showSizeSeller = -1;
            this.sellerSizes[index] = item;
            if (this.sellerColors[index]){
                this.sellerPrice[index] = parseInt(this.post[index].price) + parseInt(this.sellerColors[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.sellerColors[index].count <= 0 || this.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }else{
                this.sellerPrice[index] = parseInt(this.post[index].price) + parseInt(item.price);
                if(item.count <= 0 || this.post[index].count <= 0){
                    this.sellerCount[index] = 0;
                }else{
                    this.sellerCount[index] = 1;
                }
            }
        },
        btnColorChange(index){
            if(this.showColorsSeller == index){
                this.showColorsSeller = -1;
            }else{
                this.showColorsSeller = index;
            }
        },
        btnShowGuaranteeSeller(index){
            if(this.showGuaranteeSeller == index){
                this.showGuaranteeSeller = -1;
            }else{
                this.showGuaranteeSeller = index;
            }
        },
        btnShowSizeSeller(index){
            if(this.showSizeSeller == index){
                this.showSizeSeller = -1;
            }else{
                this.showSizeSeller = index;
            }
        },
        getGuarantees(guarantee){
            this.form.allGuarantee = guarantee;
        },
        checkData(){
            for ( this.i ; this.i <  this.post.length; this.i++) {
                this.sellerPrice.push(this.post[this.i].price);
                this.sellerCount.push(this.post[this.i].count);
                if (JSON.parse(this.post[this.i].review[0].colors).length){
                    this.sellerPrice[this.i] = parseInt(this.sellerPrice[this.i]) + parseInt(JSON.parse(this.post[this.i].review[0].colors)[0].price);
                    this.sellerColors.push(JSON.parse(this.post[this.i].review[0].colors)[0]);
                    if(this.sellerColors[this.i].count <= 0){
                        this.sellerCount[this.i] = 0;
                    }
                }
                if (JSON.parse(this.post[this.i].review[0].size).length){
                    this.sellerPrice[this.i] = parseInt(this.sellerPrice[this.i]) + parseInt(JSON.parse(this.post[this.i].review[0].size)[0].price);
                    this.sellerSizes.push(JSON.parse(this.post[0].review[0].size)[0]);
                    if(this.sellerSizes[this.i].count <= 0){
                        this.sellerCount[this.i] = 0;
                    }
                }
                if (this.post[this.i].guarantee.length){
                    this.sellerGuarantees.push(this.post[this.i].guarantee[0]);
                }
            }
            this.i = 0;
        }
    },
    mounted() {
        this.checkData();
    }
}
</script>

<style scoped>

</style>
