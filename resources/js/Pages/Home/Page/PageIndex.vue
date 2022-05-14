<template>
    <home-layout>
        <div class="allPages width">
            <div class="allPagesTitle">
                <h1 v-if="$i18n.locale == 'fa'">{{page.title}}</h1>
                <h1 v-if="$i18n.locale == 'en'" class="en">{{page.titleEn}}</h1>
            </div>
            <div class="allPagesBody">
                <p v-if="$i18n.locale == 'fa'" v-html="page.body"></p>
                <p v-if="$i18n.locale == 'en'" class="en" v-html="page.bodyEn"></p>
                <div class="forms">
                    <div class="form" v-if="page.form">
                        <div class="items">
                            <div class="item">
                                <h3>{{ $t('firstLastName') }}</h3>
                                <input type="text" v-model="form.name" :placeholder="$t('firstLastName')">
                            </div>
                            <div class="item">
                                <h3>{{ $t('emailAddress') }}</h3>
                                <input type="text" v-model="form.email" :placeholder="$t('emailAddress')">
                            </div>
                        </div>
                        <div class="item">
                            <h3>{{ $t('ticket') }}</h3>
                            <textarea v-model="form.body" :placeholder="$t('ticket')"></textarea>
                        </div>
                        <div class="buttons">
                            <button v-if="loader">
                                <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                            </button>
                            <button v-else @click="sendReport">{{ $t('send') }}</button>
                        </div>
                    </div>
                    <div class="map" v-if="page.form">
                        <mapir :apiKey="map" :center="geo">
                            <mapMarker
                                :coordinates.sync="geo"
                                color="red"
                                :draggable="false"
                            />
                        </mapir>
                    </div>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import { mapir, mapMarker } from "mapir-vue";
export default {
    components: { HomeLayout , SvgIcon , mapir , mapMarker },
    props:['page','title','map'],
    metaInfo() {
        return {
            title: `${this.page.title} - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                amp: true,
                reptilian: 'gator'
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    data(){
        return{
            form:{
                name: '',
                email: '',
                body: '',
            },
            forms: '',
            geo: [0,0],
            loader: false,
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
        sendReport(){
            this.loader = true;
            this.forms = JSON.stringify(this.form);
            const url = '/send-call'
            axios.post(url,{
                forms:this.forms
            })
                .then(response=>{
                    this.loader = false;
                    this.$toast.success('انجام شد', 'بازخورد شما با موفقیت ثبت شد', this.notificationSystem.options.success);
                    window.location.reload();
                })
                .catch(err =>{
                    this.loader = false;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
    },
    mounted(){
        if(this.page.map){
            this.geo= [JSON.parse(this.page.map).lng , JSON.parse(this.page.map).lat];
        }
    }
}
</script>

<style>

</style>
