<template>
  <table>
    <tr>
      <div>
        <th @click="checkAll">
          <svg-icon v-if="table.length == value.length" :icon="'#check'"></svg-icon>
          <svg-icon v-else :icon="'#uncheck'"></svg-icon>
        </th>
        <th v-for="label in labels">{{ label }}</th>
      </div>
    </tr>
    <tr v-for="(item, index) in table" :key="index">
      <div v-for="(focus , index3) in value" v-if="focus == item.id" :key="index3" class="active">
        <td @click="getCheck(item.id)">
          <i v-for="(values , index2) in value" v-if="values == item.id" :key="index2">
            <svg-icon :icon="'#check'"></svg-icon>
          </i>
          <i>
            <svg-icon :icon="'#uncheck'"></svg-icon>
          </i>
        </td>
        <td v-for="label in labels">
          <span v-if="label == '#'">{{ ++index }}</span>
          <span v-if="label == 'آیدی'">{{ item.id }}</span>
          <span v-if="label == 'تصویر' && nameTable == 'post'">
                        <img :src="JSON.parse(item.image)[0]">
                    </span>
          <span v-if="label == 'تصویر' && nameTable != 'post'">
                        <img v-if="item.profile_photo_url" src="/img/user.png">
                        <img v-else :src="item.image">
                    </span>
          <span v-if="label == 'نام'">{{ item.name }}</span>
            <span v-if="label == 'تصویر پشت کارت ملی'">
                <img :src="item.backNaturalId">
            </span>
            <span v-if="label == 'تصویر جلو کارت ملی'">
                <img :src="item.frontNaturalId">
            </span>
          <span v-if="label == 'عنوان'">{{ item.title }}</span>
          <span v-if="label == 'روز'">{{ item.day }}</span>
          <span v-if="label == 'آخرین بازدید'">{{ item.activity }}</span>
          <span v-if="label == 'تاریخ ثبت'">{{ item.created_at }}</span>
            <span v-if="label == 'پیوند'">{{ item.slug }}</span>
            <span v-if="label == 'امتیاز'">{{ item.name }} امتیاز</span>
            <span v-if="label == 'توضیحات'">{{ item.body }}</span>
            <span v-if="label == 'از امتیاز'">{{ item.from }}</span>
            <span v-if="label == 'تا امتیاز'">{{ item.to }}</span>
            <span v-if="label == 'توضیح'">{{ item.description }}</span>
            <span v-if="label == 'سیستم'">{{ item.platform }}</span>
            <span v-if="label == 'مرورگر'">{{ item.browser }}</span>
            <span v-if="label == 'آی پی'">{{ item.user_ip }}</span>
            <span v-if="label == 'کاربر'">{{ item.user.name }}</span>
            <span v-if="label == 'گیرنده'">{{ item.user[0].name }}</span>
          <span v-if="label == 'شماره ﺗﺮﺍﻛﻨﺶ ﭘﺮﺩﺍﺧﺖ'">{{ item.refId }}</span>
          <span v-if="label == 'شماره سفارش'">{{ item.property }}</span>
            <span v-if="label == 'وضعیت پرداخت'">
                        <span v-if="item.status == 100" class="activeStatus">پرداخت شده</span>
                        <span v-else class="unActive">در حال پرداخت</span>
                    </span>
            <span v-if="label == 'بازخورد'" class="ul">
                        <span v-for="data in JSON.parse(item.data)">{{ data }}</span>
                    </span>
          <span v-if="label == 'مبلغ'">{{ item.price|NumFormat }} تومان</span>
            <span v-if="label == 'وضعیت'">
                        <span v-if="item.status == 0" class="unActive">پیشنویس</span>
                        <span v-else class="activeStatus">منتشر شده</span>
                    </span>
            <span v-if="label == 'نوع شارژ'">
                        <span v-if="item.type == 0">شارژ حساب</span>
                        <span v-else>خرید از حساب</span>
                    </span>
            <span v-if="label == 'وضعیت بررسی'">
                        <span v-if="item.status == 0">در حال بررسی</span>
                        <span v-if="item.status == 1" class="unActive">رد شده</span>
                        <span v-else class="activeStatus">تایید شده</span>
                    </span>
            <span v-if="label == 'وضعیت تسویه'">
                        <span v-if="item.status == 0">در حال بررسی</span>
                        <span v-if="item.status == 1" class="unActive">رد شده</span>
                        <span v-if="item.status == 2" class="activeStatus">تسویه شده</span>
                    </span>
            <span v-if="label == 'نوع امتیاز'">
                        <span v-if="item.type == 0" class="activeStatus">افزایش امتیاز</span>
                        <span v-else class="unActive">کاهش امتیاز</span>
                    </span>
            <span v-if="label == 'وضعیت فعالیت'">
                        <span v-if="item.status == 0" class="unActive">غیرفعال</span>
                        <span v-else class="activeStatus">فعال</span>
                    </span>
          <span v-if="label == 'وضعیت ارسال'">
                        <span class="unActive" v-if="item.deliver == 0">دریافت سفارش</span>
                        <span class="unActive" v-if="item.deliver == 1">در انتظار بررسی</span>
                        <span class="unActive" v-if="item.deliver == 2">بسته بندی شده</span>
                        <span class="unActive" v-if="item.deliver == 3">تحویل پیک</span>
                        <span class="activeStatus" v-if="item.deliver == 4">تکمیل شده</span>
                    </span>
            <span v-else-if="nameTable == 'news' && label == 'عملیات'">
                    <inertia-link v-if="edits == 1" :href="'/admin/news/' + item.id + '/edit'">
                        <svg-icon :icon="'#edit'"></svg-icon>
                    </inertia-link>
                        <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                            :icon="'#trash'"></svg-icon></i>
            </span>
            <span v-else-if="nameTable == 'pay' && label == 'عملیات'">
                <a @click="btnPrint(item.id)">
                    <svg-icon :icon="'#print'"></svg-icon>
                </a>
                <inertia-link :href="'/admin/show-pay/' + item.id">
                    <svg-icon :icon="'#eye'"></svg-icon>
                </inertia-link>
                <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                    :icon="'#trash'"></svg-icon></i>
            </span>
          <span v-else>
                        <i v-if="label == 'عملیات' && edits" @click="openEdit(item.id)"><svg-icon
                            :icon="'#edit'"></svg-icon></i>
                        <i v-if="label == 'عملیات' && shows" @click="openShow(item.id)"><svg-icon
                            :icon="'#eye'"></svg-icon></i>
                        <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                            :icon="'#trash'"></svg-icon></i>
                    </span>
        </td>
      </div>
      <div>
        <td @click="getCheck(item.id)">
          <i v-for="(values , index2) in value" v-if="values == item.id" :key="index2">
            <svg-icon :icon="'#check'"></svg-icon>
          </i>
          <i>
            <svg-icon :icon="'#uncheck'"></svg-icon>
          </i>
        </td>
        <td v-for="label in labels">
          <span v-if="label == '#'">{{ ++index }}</span>
          <span v-if="label == 'آیدی'">{{ item.id }}</span>
          <span v-if="label == 'تصویر' && nameTable == 'file'">
                        <img :src="JSON.parse(item.image)[0]">
                    </span>
          <span v-if="label == 'تصویر' && nameTable != 'file'">
                        <img v-if="item.profile_photo_url" src="/img/user.png">
                        <img v-else :src="item.image">
                    </span>
          <span v-if="label == 'نام'">{{ item.name }}</span>
            <span v-if="label == 'تصویر پشت کارت ملی'">
                <img :src="item.backNaturalId">
            </span>
            <span v-if="label == 'تصویر جلو کارت ملی'">
                <img :src="item.frontNaturalId">
            </span>
          <span v-if="label == 'عنوان'">{{ item.title }}</span>
          <span v-if="label == 'آخرین بازدید'">{{ item.activity }}</span>
          <span v-if="label == 'تاریخ ثبت'">{{ item.created_at }}</span>
            <span v-if="label == 'توضیحات'">{{ item.body }}</span>
            <span v-if="label == 'روز'">{{ item.day }}</span>
            <span v-if="label == 'امتیاز'">{{ item.name }} امتیاز</span>
            <span v-if="label == 'توضیح'">{{ item.description }}</span>
          <span v-if="label == 'پیوند'">{{ item.slug }}</span>
          <span v-if="label == 'شماره سفارش'">{{ item.property }}</span>
            <span v-if="label == 'سیستم'">{{ item.platform }}</span>
            <span v-if="label == 'از امتیاز'">{{ item.from }}</span>
            <span v-if="label == 'تا امتیاز'">{{ item.to }}</span>
            <span v-if="label == 'مرورگر'">{{ item.browser }}</span>
            <span v-if="label == 'آی پی'">{{ item.user_ip }}</span>
          <span v-if="label == 'شماره ﺗﺮﺍﻛﻨﺶ ﭘﺮﺩﺍﺧﺖ'">{{ item.refId }}</span>
            <span v-if="label == 'کاربر'">{{ item.user.name }}</span>
            <span v-if="label == 'گیرنده'">{{ item.user[0].name }}</span>
          <span v-if="label == 'مبلغ'">{{ item.price|NumFormat }} تومان</span>
          <span v-if="label == 'وضعیت'">
                        <span v-if="item.status == 0" class="unActive">پیشنویس</span>
                        <span v-else class="activeStatus">منتشر شده</span>
                    </span>
          <span v-if="label == 'نوع امتیاز'">
                        <span v-if="item.type == 0" class="activeStatus">افزایش امتیاز</span>
                        <span v-else class="unActive">کاهش امتیاز</span>
                    </span>
            <span v-if="label == 'وضعیت بررسی'">
                        <span v-if="item.status == 0">در حال بررسی</span>
                        <span v-if="item.status == 1" class="unActive">رد شده</span>
                        <span v-if="item.status == 2" class="activeStatus">تایید شده</span>
                    </span>
            <span v-if="label == 'وضعیت تسویه'">
                        <span v-if="item.status == 0">در حال بررسی</span>
                        <span v-if="item.status == 1" class="unActive">رد شده</span>
                        <span v-if="item.status == 2" class="activeStatus">تسویه شده</span>
                    </span>
            <span v-if="label == 'وضعیت فعالیت'">
                        <span v-if="item.status == 0" class="unActive">غیرفعال</span>
                        <span v-else class="activeStatus">فعال</span>
                    </span>
            <span v-if="label == 'تاییدیه'">
                        <span v-if="item.approved == 0" class="unActive">در حال بررسی</span>
                        <span v-else class="activeStatus">تایید شده</span>
                    </span>
            <span v-if="label == 'بازخورد'" class="ul">
                        <span v-for="data in JSON.parse(item.data)">{{ data }}</span>
                    </span>
          <span v-if="label == 'وضعیت ارسال'">
                        <span class="unActive" v-if="item.deliver == 0">دریافت سفارش</span>
                        <span class="unActive" v-if="item.deliver == 1">در انتظار بررسی</span>
                        <span class="unActive" v-if="item.deliver == 2">بسته بندی شده</span>
                        <span class="unActive" v-if="item.deliver == 3">تحویل پیک</span>
                        <span class="activeStatus" v-if="item.deliver == 4">تکمیل شده</span>
                    </span>
          <span v-if="label == 'وضعیت پرداخت'">
                        <span v-if="item.status == 100" class="activeStatus">پرداخت شده</span>
                        <span v-else class="unActive">در حال پرداخت</span>
                    </span>
            <span v-if="label == 'نوع شارژ'">
                        <span v-if="item.type == 0">شارژ حساب</span>
                        <span v-else>خرید از حساب</span>
                    </span>
            <span v-if="nameTable == 'variety' && label == 'عملیات'">
                        <inertia-link v-if="edits == 1" :href="'/admin/variety/' + item.id + '/edit'">
                            <svg-icon :icon="'#edit'"></svg-icon>
                        </inertia-link>
                        <inertia-link :href="'/admin/variety/' + item.id + '/show'">
                            <svg-icon :icon="'#eye'"></svg-icon>
                        </inertia-link>
                        <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                            :icon="'#trash'"></svg-icon></i>
                    </span>
            <span v-if="nameTable == 'file' && label == 'عملیات'">
                        <inertia-link v-if="edits == 1" :href="'/admin/file/' + item.id + '/edit'">
                            <svg-icon :icon="'#edit'"></svg-icon>
                        </inertia-link>
                        <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                            :icon="'#trash'"></svg-icon></i>
                    </span>
            <span v-else-if="nameTable == 'pay' && label == 'عملیات'">
                <a @click="btnPrint(item.id)">
                    <svg-icon :icon="'#print'"></svg-icon>
                </a>
                <inertia-link :href="'/admin/show-pay/' + item.id">
                    <svg-icon :icon="'#eye'"></svg-icon>
                </inertia-link>
                <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                    :icon="'#trash'"></svg-icon></i>
            </span>
            <span v-else-if="nameTable == 'news' && label == 'عملیات'">
                    <inertia-link v-if="edits == 1" :href="'/admin/news/' + item.id + '/edit'">
                        <svg-icon :icon="'#edit'"></svg-icon>
                    </inertia-link>
                        <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                            :icon="'#trash'"></svg-icon></i>
            </span>
            <span v-else>
                <i v-if="label == 'عملیات' && edits" @click="openEdit(item.id)"><svg-icon
                    :icon="'#edit'"></svg-icon></i>
                <i v-if="label == 'عملیات' && shows" @click="openShow(item.id)"><svg-icon
                    :icon="'#eye'"></svg-icon></i>
                <i v-if="label == 'عملیات' && deletes" @click="btnRemoveArray(item.id)"><svg-icon
                    :icon="'#trash'"></svg-icon></i>
            </span>
        </td>
      </div>
    </tr>
  </table>
</template>

<script>
import SvgIcon from "../../Svg/SvgIcon";
import ShowImage from "../ShowImage";
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import PaginatePanel from '../PaginatePanel.vue';

export default {
  name: "AllTable",
  props: ['labels', 'nameTable', 'table', 'shows', 'deletes', 'edits'],
  components: {
    SvgIcon,
    VuePerfectScrollbar,
    ShowImage,
    PaginatePanel,
  },
  data() {
    return {
      value: [],
      i: 0,
    }
  },
  methods: {
    checkAll() {
      this.i = 0;
      if (this.table.length == this.value.length) {
        this.value = [];
      } else {
        this.value = [];
        for (this.i; this.i < this.table.length; this.i++) {
          this.value.push(this.table[this.i].id);
        }
        this.i = 0;
      }
      this.$emit('sendCheck', this.value);
    },
    getCheck(id) {
      for (this.i; this.i < this.value.length; this.i++) {
        if (this.value[this.i] == id) {
          this.value.splice(this.i, 1);
          this.i = 100;
        }
      }
      if (this.i != 101) {
        this.value.push(id);
      }
      this.i = 0;
      this.$emit('sendCheck', this.value);
    },
    openEdit(id) {
      this.$emit('sendEdit', id);
    },
    openShow(id) {
      this.$emit('sendShow', id);
    },
    btnPrint(id){
      this.$emit('sendPrint', id);
    },
    btnRemoveArray(id) {
      this.value = [id]
      this.$emit('sendDelete', id);
    },
  },
}
</script>

<style>

</style>
