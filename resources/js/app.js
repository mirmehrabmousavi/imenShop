require('./bootstrap');
import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { InertiaProgress } from '@inertiajs/progress';
import { i18n } from '../../plugins/i18n';
import VueMeta from 'vue-meta';
import fullscreen from 'vue-fullscreen'
import EventHub from 'vue-event-hub';
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ZoomOnHover from "vue-zoom-on-hover";
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
import VueLazyload from 'vue-lazyload';
Vue.use(VueSweetalert2);
Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(fullscreen)
Vue.use(EventHub);
Vue.use( CKEditor );
Vue.use(ZoomOnHover);
import VueIziToast from 'vue-izitoast';
import 'izitoast/dist/css/iziToast.css';
import 'izitoast/dist/css/iziToast.min.css';
import VueCookies from 'vue-cookies';
Vue.use(VueCookies);
Vue.use(VueIziToast);
Vue.component('date-picker', VuePersianDatetimePicker);
Vue.use(VueLazyload, {
    preLoad: 1,
    error: '/img/404Image.jpg',
    loading: '/img/loadingImage.gif',
    attempt: 1
});
InertiaProgress.init({
    // The delay after which the progress bar will
    // appear during navigation, in milliseconds.
    delay: 250,

    // The color of the progress bar.
    color: '#F71938',

    // Whether to include the default NProgress styles.
    includeCSS: true,

    // Whether the NProgress spinner will be shown.
    showSpinner: false,
})

Vue.use(VueMeta, {
    refreshOnceOnNavigation: false
});
const app = document.getElementById('app');

new Vue({
    i18n,
    render: (h) =>
        h(
            InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent(name) {
                    if(name == 'ShowPayPanel'){
                        return require(`./Pages/Admin/Pay/ShowPayPanel`).default
                    }
                    return require(`./Pages/Home/${name}`).default;
                },
            },
        }),
}).$mount(app);

Vue.filter('NumFormat', function(value) {
    if(!value) return '0';
    value = `${value}`;
    var intPart = Number(value).toFixed(0);
    var intPartFormat = intPart.toString().replace(/(\d)(?=(?:\d{3})+$)/g, '$1,');
    var floatPart = "";
    var value2Array = value.split(".");
    if(value2Array.length == 2) {
        floatPart = value2Array[1].toString();
        if(floatPart.length == 1) {
            return intPartFormat + "." + floatPart + '0';
        } else {
            return intPartFormat + "." + floatPart;
        }
    } else {
        return intPartFormat + floatPart;
    }
})
