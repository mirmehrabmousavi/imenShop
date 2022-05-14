<template>
    <admin-layout>
        <div class="allSettingComment">
            <div class="title">s
                <inertia-link href="/">خانه</inertia-link>
                <span>/</span>
                <inertia-link href="/admin">داشبورد</inertia-link>
                <span>/</span>
                <inertia-link href="/admin/setting/setting-manage">تنظیمات دیدگاه</inertia-link>
            </div>
            <div class="settingCommentContainer">
                <div class="settingCommentContainerRight">
                    <div class="allSettingCommentItem">
                        <label>کلماتی که ممنوع هستند با (,) جدا کنید</label>
                        <textarea placeholder="کلماتی که ممنوع هستند" v-model="form.forbiddens"></textarea>
                    </div>
                    <div class="allSettingCommentItem">
                        <label>نمایش کاربر</label>
                        <select v-model="form.showUser">
                            <option value="0">نمایش کاربر همراه عکس و نام</option>
                            <option value="1">نمایش کاربر همراه فقط نام</option>
                            <option value="2">نمایش کاربر همراه فقط عکس</option>
                        </select>
                    </div>
                </div>
                <div class="settingCommentContainerLeft">
                    <div class="allSettingCommentItems">
                        <label for="s1d" class="item">
                            نیاز به تایید دیدگاه
                            <input id="s1d" type="checkbox" class="switch" v-model="form.approved">
                        </label>
                        <label for="s2d" class="item">
                            فعال کردن دکمه پاسخ به دیدگاه
                            <input id="s2d" type="checkbox" class="switch" v-model="form.reply">
                        </label>
                        <label for="s3d" class="item">
                            اجبار به نوشتن ایمیل و نام
                            <input id="s3d" type="checkbox" class="switch" v-model="form.coercion">
                        </label>
                        <label for="s4d" class="item">
                            نمایش دیدگاه فقط برای کاربران سایت
                            <input id="s4d" type="checkbox" class="switch" v-model="form.checkUser">
                        </label>
                        <label for="s5d" class="item">
                            نمایش زمان آنلاین بودن کاربر
                            <input id="s5d" type="checkbox" class="switch" v-model="form.checkOnline">
                        </label>
                        <div class="allSettingCommentItemNumber">
                            <label>محدود سازی دیدگاه گذاشتن</label>
                            <input type="number" min="1" max="30" step="1" v-model="form.limited">
                        </div>
                        <div class="allSettingCommentItemNumber">
                            <label>تعداد نمایش دیدگاه ها در هر صفحه</label>
                            <input type="number" min="0" max="100" step="10" v-model="form.pages">
                        </div>
                    </div>
                </div>
                <div class="button" @click="sendSetting">
                    <button>ارسال</button>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "CommentSetting",
    components:{
        AdminLayout,
        SvgIcon
    },
    props:['pic','forbidden','show','limit','page','approve','replay','coercions','check','online','allow','userData'],
    data(){
        return{
            form:{
                forbiddens: this.forbidden,
                limited : this.limit,
                pages : this.page,
                showUser : this.show,
                userPic : this.pic,
                approved : this.approve,
                reply : this.replay,
                coercion : this.coercions,
                checkUser : this.check,
                checkOnline : this.online,
            },
            allows : [],
        }
    },
    metaInfo: {
      title: 'تنظیمات دیدگاه'
    },
    methods:{
        sendSetting(){
            const url = "/admin/setting/setting-comment";
            this.$inertia.put(url , this.form)
            this.$swal.fire(
                'با موفقیت انجام شد',
                'تنظیمات با موفقیت انجام شد',
                'success'
            );
        },
        checks(){
            if (this.show == '0'){
                this.form.showUser = 0;
            }
            if (this.approve == '0'){
                this.form.approved = 0;
            }
            if (this.replay == '0'){
                this.form.reply = 0;
            }
            if (this.coercions == '0'){
                this.form.coercion = 0;
            }
            if (this.check == '0'){
                this.form.checkUser = 0;
            }
            if (this.online == '0'){
                this.form.checkOnline = 0;
            }
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '2');
        },
    },
    mounted() {
        this.checks();
        this.sidebar();
    }
}
</script>

<style scoped>

</style>
