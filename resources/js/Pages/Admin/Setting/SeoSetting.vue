<template>
    <admin-layout>
        <div class="allSeoSetting">
            <div class="allSeoSettingTitle">
                <div class="allSeoSettingAddress">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/setting/seo">تنظیمات سئو</inertia-link>
                </div>
            </div>
            <div class="allSeoSettingItems">
                <div class="allSeoSettingItem">
                    <label>عنوان فعالیت</label>
                    <input v-model="form.titleSeo" type="text" placeholder="عنوان فعالیت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>معرفی کوتاه فعالیت سایت</label>
                    <input v-model="form.descriptionSeo" type="text" placeholder="فعالیت سایت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>کلمات کلیدی سایت با ( , ) جدا کنید</label>
                    <input v-model="form.keywords" type="text" placeholder="کلمات کلیدی را وارد کنید ...">
                </div>
                <button @click.prevent="sendSetting">ارسال</button>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "SeoSetting",
    props : ['descriptionSeo','keywords','titleSeo','allow','userData'],
    components:{
        AdminLayout,
        SvgIcon,
    },
    data() {
        return {
            form:{
                keywords : this.keywords,
                descriptionSeo : this.descriptionSeo,
                titleSeo : this.titleSeo,
            },
            allows : [],
        }
    },
    metaInfo: {
      title: 'تنظیمات سیو'
    },
    methods:{
        sendSetting(){
            const url = "/admin/setting/seo";
            this.$inertia.put(url , this.form)
            this.$swal.fire(
                'با موفقیت انجام شد',
                'تنظیمات با موفقیت انجام شد',
                'success'
            );
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '2');
        },
    },
    mounted() {
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
