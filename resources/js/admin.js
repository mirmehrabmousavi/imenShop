require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { InertiaProgress } from '@inertiajs/progress';
import fullscreen from 'vue-fullscreen'
import EventHub from 'vue-event-hub';
import CKEditor from '@ckeditor/ckeditor5-vue2';
import VueMeta from 'vue-meta';
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
Vue.use(VueSweetalert2);
Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(fullscreen)
Vue.use(EventHub);
Vue.use( CKEditor );
Vue.component('date-picker', VuePersianDatetimePicker);
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
    render: (h) =>
        h(
            InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent :(name) => {
                    if(name == 'panel'){
                        return require(`./Pages/Admin/panel`).default
                    }
                    if(name == 'ChargePanel'){
                        return require(`./Pages/Admin/Charge/ChargePanel`).default
                    }
                    if(name == 'CheckoutPanel'){
                        return require(`./Pages/Admin/Checkout/CheckoutPanel`).default
                    }
                    if(name == 'CommentPanel'){
                        return require(`./Pages/Admin/Comment/CommentPanel`).default
                    }
                    if(name == 'EditComment'){
                        return require(`./Pages/Admin/Comment/EditComment`).default
                    }
                    if(name == 'DocumentPanel'){
                        return require(`./Pages/Admin/Document/DocumentPanel`).default
                    }
                    if(name == 'EventPanel'){
                        return require(`./Pages/Admin/Event/EventPanel`).default
                    }
                    if(name == 'ShowPayPanel'){
                        return require(`./Pages/Admin/Pay/ShowPayPanel`).default
                    }
                    if(name == 'ExcelPanel'){
                        return require(`./Pages/Admin/Excel/ExcelPanel`).default
                    }
                    if(name == 'ImportExcel'){
                        return require(`./Pages/Admin/Excel/ImportExcel`).default
                    }
                    if(name == 'ExcelCreate'){
                        return require(`./Pages/Admin/Excel/ExcelCreate`).default
                    }
                    if(name == 'FileCreate'){
                        return require(`./Pages/Admin/File/FileCreate`).default
                    }
                    if(name == 'AllFile'){
                        return require(`./Pages/Admin/File/AllFile`).default
                    }
                    if(name == 'EditFile'){
                        return require(`./Pages/Admin/File/EditFile`).default
                    }
                    if(name == 'GalleryPanel'){
                        return require(`./Pages/Admin/Gallery/GalleryPanel`).default
                    }
                    if(name == 'InventoryIndex'){
                        return require(`./Pages/Admin/Inventory/InventoryIndex`).default
                    }
                    if(name == 'NotificationPanel'){
                        return require(`./Pages/Admin/Notification/NotificationPanel`).default
                    }
                    if(name == 'CreatePay'){
                        return require(`./Pages/Admin/Pay/CreatePay`).default
                    }
                    if(name == 'PayPanel'){
                        return require(`./Pages/Admin/Pay/PayPanel`).default
                    }
                    if(name == 'PayChart'){
                        return require(`./Pages/Admin/Pay/PayChart`).default
                    }
                    if(name == 'AllNews'){
                        return require(`./Pages/Admin/Post/AllNews`).default
                    }
                    if(name == 'AllPost'){
                        return require(`./Pages/Admin/Post/AllPost`).default
                    }
                    if(name == 'CreateNews'){
                        return require(`./Pages/Admin/Post/CreateNews`).default
                    }
                    if(name == 'EditNews'){
                        return require(`./Pages/Admin/Post/EditNews`).default
                    }
                    if(name == 'EditPost'){
                        return require(`./Pages/Admin/Post/EditPost`).default
                    }
                    if(name == 'PostCreate'){
                        return require(`./Pages/Admin/Post/PostCreate`).default
                    }
                    if(name == 'ShowPost'){
                        return require(`./Pages/Admin/Post/ShowPost`).default
                    }
                    if(name == 'AllTaxonami'){
                        return require(`./Pages/Admin/Taxonami/AllTaxonami`).default
                    }
                    if(name == 'QuestionPanel'){
                        return require(`./Pages/Admin/Question/QuestionPanel`).default
                    }
                    if(name == 'RankPanel'){
                        return require(`./Pages/Admin/Rank/RankPanel`).default
                    }
                    if(name == 'RobotPanel'){
                        return require(`./Pages/Admin/Robot/RobotPanel`).default
                    }
                    if(name == 'RolePanel'){
                        return require(`./Pages/Admin/Role/RolePanel`).default
                    }
                    if(name == 'UserRole'){
                        return require(`./Pages/Admin/Role/UserRole`).default
                    }
                    if(name == 'ScorePanel'){
                        return require(`./Pages/Admin/Score/ScorePanel`).default
                    }
                    if(name == 'SellerPanel'){
                        return require(`./Pages/Admin/Seller/SellerPanel`).default
                    }
                    if(name == 'CategorySetting'){
                        return require(`./Pages/Admin/Setting/CategorySetting`).default
                    }
                    if(name == 'CommentSetting'){
                        return require(`./Pages/Admin/Setting/CommentSetting`).default
                    }
                    if(name == 'PaySetting'){
                        return require(`./Pages/Admin/Setting/PaySetting`).default
                    }
                    if(name == 'SeoSetting'){
                        return require(`./Pages/Admin/Setting/SeoSetting`).default
                    }
                    if(name == 'SettingDesign'){
                        return require(`./Pages/Admin/Setting/SettingDesign`).default
                    }
                    if(name == 'SettingManage'){
                        return require(`./Pages/Admin/Setting/SettingManage`).default
                    }
                    if(name == 'TicketPanel'){
                        return require(`./Pages/Admin/Ticket/TicketPanel`).default
                    }
                    if(name == 'UserPanel'){
                        return require(`./Pages/Admin/User/UserPanel`).default
                    }
                    if(name == 'AllVariety'){
                        return require(`./Pages/Admin/Variety/AllVariety`).default
                    }
                    if(name == 'CreateVariety'){
                        return require(`./Pages/Admin/Variety/CreateVariety`).default
                    }
                    if(name == 'EditVariety'){
                        return require(`./Pages/Admin/Variety/EditVariety`).default
                    }
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
