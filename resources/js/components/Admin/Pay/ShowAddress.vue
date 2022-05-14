<template>
    <div class="allAddAddress">
        <div class="CreateAddress">
            <div class="titleAddress">
                <span>نمایش آدرس</span>
                <i @click="btnClose">
                    <svg-icon :icon="'#cancel'"></svg-icon>
                </i>
            </div>
            <div class="map" v-if="level == 0">
                <mapir :apiKey="mapirToken" :center="geo">
                    <mapMarker
                        :coordinates.sync="geo"
                        color="red"
                    />
                </mapir>
            </div>
            <div class="form" v-if="level == 1">
                <div class="items">
                    <div class="item">
                        <h4>استان</h4>
                        <h5>{{pay.address[0].state}}</h5>
                    </div>
                    <div class="item">
                        <h4>شهر</h4>
                        <h5>{{pay.address[0].city}}</h5>
                    </div>
                </div>
                <div class="item">
                    <h4>نشانی پستی</h4>
                    <h5>{{pay.address[0].address}}</h5>
                </div>
                <div class="items">
                    <div class="item">
                        <h4>پلاک</h4>
                        <h5>{{ pay.address[0].plaque }}</h5>
                    </div>
                    <div class="item">
                        <h4>واحد</h4>
                        <h5>{{ pay.address[0].unit }}</h5>
                    </div>
                    <div class="item">
                        <h4>کد پستی</h4>
                        <h5>{{pay.address[0].post}}</h5>
                    </div>
                </div>
                <div class="items">
                    <div class="item">
                        <h4>نام گیرنده</h4>
                        <h5>{{ pay.address[0].name }}</h5>
                    </div>
                    <div class="item">
                        <h4>شماره تماس</h4>
                        <h5>{{pay.address[0].number}}</h5>
                    </div>
                </div>
            </div>
            <div class="bottomAddress">
                <span v-if="level == 0">مرسوله باید به این موقعیت ارسال شود.</span>
                <span class="toMap" v-if="level == 1" @click="level = 0">نمایش موقعیت روی نقشه</span>
                <button @click="btnNext" v-else>جزییات</button>
            </div>
        </div>
    </div>
</template>

<script>
// import { mapir, mapMarker } from "mapir-vue";
import SvgIcon from "../../../Pages/Svg/SvgIcon";
export default {
    name: "ShowAddress",
    props:['map','pay'],
    components: {
        SvgIcon,
        // mapir,
        // mapMarker,
    },
    data(){
        return{
            geo: '',
            mapirToken: this.map,
            level: 0,
        }
    },
    methods:{
        btnClose(){
            this.$emit('sendClose');
        },
        checkOnMap(){
            if(this.pay.address.length){
                this.geo = [JSON.parse(this.pay.address[0].geo).lng , JSON.parse(this.pay.address[0].geo).lat];
            }
        },
        btnNext(){
            if(this.level == 0){
                this.level = 1;
            }
        },
    },
    mounted(){
        this.checkOnMap();
    }
}
</script>

<style scoped>

</style>
