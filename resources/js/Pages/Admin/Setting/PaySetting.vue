<template>
    <admin-layout>
        <div class="allSeoSetting">
            <div class="allSeoSettingTitle">
                <div class="allSeoSettingAddress">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/setting/setting-pay">تنظیمات درگاه پرداخت</inertia-link>
                </div>
            </div>
            <div class="allSeoSettingItems">
                <div class="allSeoSettingItem">
                    <label>کد مرچنت زرینپال</label>
                    <input v-model="form.zarinpal" type="text" placeholder="کد مرچنت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>کد مرچنت زیبال</label>
                    <input v-model="form.zibal" type="text" placeholder="کد مرچنت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>کد مرچنت نکست پی</label>
                    <input v-model="form.nextPay" type="text" placeholder="کد مرچنت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>کد مرچنت آیدی پی</label>
                    <input v-model="form.idpay" type="text" placeholder="کد مرچنت را وارد کنید ...">
                </div>
                <div class="allSeoSettingItem">
                    <label>درگاه پرداخت انتخابی</label>
                    <div class="allCategoryPanel" @click="showSort = !showSort">
                        <div class="categoryShow">
                            <h4 v-if="form.choicePay == 0">زرینپال</h4>
                            <h4 v-if="form.choicePay == 1">زیبال</h4>
                            <h4 v-if="form.choicePay == 2">نکست پی</h4>
                            <h4 v-if="form.choicePay == 3">آیدی پی</h4>
                            <i>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <ul v-if="showSort">
                            <li @click="form.choicePay = 0">زرینپال</li>
                            <li @click="form.choicePay = 1">زیبال</li>
                            <li @click="form.choicePay = 2">نکست پی</li>
                            <li @click="form.choicePay = 3">آیدی پی</li>
                        </ul>
                    </div>
                </div>
                <div class="allSeoSettingItem">
                    <label>درگاه پرداخت انتخابی اپلیکیشن</label>
                    <div class="allCategoryPanel" @click="showSortApp = !showSortApp">
                        <div class="categoryShow">
                            <h4 v-if="form.choicePayApp == 0">زرینپال</h4>
                            <h4 v-if="form.choicePayApp == 2">نکست پی</h4>
                            <i>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <ul v-if="showSortApp">
                            <li @click="form.choicePayApp = 0">زرینپال</li>
                            <li @click="form.choicePayApp = 2">نکست پی</li>
                        </ul>
                    </div>
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
    name: "PaySetting",
    props : ['zibal','zarinpal','choicePayApp','choicePay','idpay','nextPay'],
    components:{
        AdminLayout,
        SvgIcon,
    },
    data() {
        return {
            showSort: false,
            showSortApp: false,
            form:{
                zibal : this.zibal,
                zarinpal : this.zarinpal,
                nextPay : this.nextPay,
                idpay : this.idpay,
                choicePay : 0,
                choicePayApp : 0,
                update: 1
            },
        }
    },
    metaInfo: {
        title: 'تنظیمات درگاه پرداخت'
    },
    methods:{
        sendSetting(){
            const url = "/admin/setting/setting-pay";
            this.$inertia.post(url , this.form)
            this.$swal.fire(
                'با موفقیت انجام شد',
                'تنظیمات با موفقیت انجام شد',
                'success'
            );
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '2');
            if (this.choicePay != null){
                this.form.choicePay = this.choicePay;
            }
            if (this.choicePayApp != null){
                this.form.choicePayApp = this.choicePayApp;
            }
        },
    },
    mounted() {
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
