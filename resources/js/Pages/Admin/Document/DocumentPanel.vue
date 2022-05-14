<template>
      <admin-layout>
          <div class="allDocument">
              <div class="allDocumentTop">
                  <h1>مدارک</h1>
                  <div class="allDocumentTitle">
                      <inertia-link href="/admin">داشبورد</inertia-link>
                      <span>/</span>
                      <inertia-link href="/admin/document">مدارک</inertia-link>
                  </div>
              </div>
              <div class="allTable">
                  <div class="allTopTable">
                      <div class="allTopTableItem">
                          <button class="button" @click="btnRemoveArray('')" v-if="value.length">حذف</button>
                      </div>
                  </div>
                  <div class="paginate" v-show="document == ''">
                      <paginate-panel :link="documents.links"></paginate-panel>
                  </div>
                  <div class="allTableContainer" v-show="document == ''">
                      <all-table :table="documents.data" v-on:sendEdit="openEdit" :labels="labels" :deletes="1" :shows="0" :edits="1" v-on:sendCheck="getCheck" v-on:sendDelete="btnRemoveArray"></all-table>
                  </div>
                  <div class="paginate" v-show="document == ''">
                      <div class="showInfo">
                          نمایش
                          {{documents.from}}
                          تا
                          {{documents.to}}
                          از
                          {{documents.total}}
                          ورودی
                      </div>
                      <paginate-panel :link="documents.links"></paginate-panel>
                  </div>
              </div>
              <div class="createDocumentPanel" v-if="document != ''">
                  <div class="createDocumentPanelItems">
                      <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                          <i>
                              <svg-icon :icon="'#error'"></svg-icon>
                          </i>
                          <span>
                               {{errors[Object.keys(errors)[0]][0]}}
                          </span>
                      </div>
                      <div class="allBecomeUserInfo">
                          <div class="sellerType">
                              <h3>وضعیت اهراز هویت</h3>
                              <div class="allCategoryPanel" @click="btnShowStatus(1)">
                                  <div class="categoryShow">
                                      <h4 v-if="form.status == 0">در حال بررسی</h4>
                                      <h4 v-if="form.status == 1">رد شده</h4>
                                      <h4 v-if="form.status == 2">تایید شده</h4>
                                      <i>
                                          <svg-icon :icon="'#down'"></svg-icon>
                                      </i>
                                  </div>
                                  <ul v-if="showStatus == 1">
                                      <li @click.stop="btnStatus(0)">در حال بررسی</li>
                                      <li @click.stop="btnStatus(1)">رد شده</li>
                                      <li @click.stop="btnStatus(2)">تایید شده</li>
                                  </ul>
                              </div>
                          </div>
                          <div class="sellerType">
                              <h3>چه نوع فروشنده ای هستید؟</h3>
                              <div class="allCategoryPanel" @click="btnShowStatus(4)">
                                  <div class="categoryShow">
                                      <h4 v-if="form.seller == 0">فروشنده حقیقی</h4>
                                      <h4 v-if="form.seller == 1">فروشنده حقوقی</h4>
                                      <i>
                                          <svg-icon :icon="'#down'"></svg-icon>
                                      </i>
                                  </div>
                                  <ul v-if="showStatus == 4">
                                      <li @click.stop="btnSeller(0)">فروشنده حقیقی</li>
                                      <li @click.stop="btnSeller(1)">فروشنده حقوقی</li>
                                  </ul>
                              </div>
                          </div>
                          <div class="personInfoSeller">
                              <h3>اطلاعات فروشنده</h3>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>نام و نام خانوادگی</h4>
                                      <input type="text" placeholder="نام و نام خانوادگی" v-model="form.name">
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>کد ملی</h4>
                                      <input type="text" placeholder="کد ملی" v-model="form.code">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>جنسیت</h4>
                                      <div class="allCategoryPanel" @click="btnShowStatus(2)">
                                          <div class="categoryShow">
                                              <h4 v-if="form.gender == 0">مرد</h4>
                                              <h4 v-if="form.gender == 1">زن</h4>
                                              <i>
                                                  <svg-icon :icon="'#down'"></svg-icon>
                                              </i>
                                          </div>
                                          <ul v-if="showStatus == 2">
                                              <li @click.stop="btnGender(0)">مرد</li>
                                              <li @click.stop="btnGender(1)">زن</li>
                                          </ul>
                                      </div>
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>سال تولد</h4>
                                      <date-picker
                                          v-model="form.dateBirth"
                                          type="date"
                                          format="YYYY-MM-DD"
                                          display-format="jYYYY-jMM-jDD"
                                      />
                                  </div>
                              </div>
                          </div>
                          <div class="personInfoSeller">
                              <h3>اطلاعات شرکت</h3>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>نام شرکت</h4>
                                      <input type="text" placeholder="نام شرکت" v-model="form.companyName">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>نوع شرکت</h4>
                                      <div class="allCategoryPanel" @click="btnShowStatus(3)">
                                          <div class="categoryShow">
                                              <h4 v-if="form.type == 0">سهامی عام</h4>
                                              <h4 v-if="form.type == 1">سهامی خاص</h4>
                                              <h4 v-if="form.type == 2">مسئولیت محدود</h4>
                                              <h4 v-if="form.type == 3">تعاونی</h4>
                                              <h4 v-if="form.type == 4">تضامنی</h4>
                                              <i>
                                                  <svg-icon :icon="'#down'"></svg-icon>
                                              </i>
                                          </div>
                                          <ul v-if="showStatus == 3">
                                              <li @click.stop="btnType(0)">سهامی عام</li>
                                              <li @click.stop="btnType(1)">سهامی خاص</li>
                                              <li @click.stop="btnType(2)">مسئولیت محدود</li>
                                              <li @click.stop="btnType(3)">تعاونی</li>
                                              <li @click.stop="btnType(4)">تضامنی</li>
                                          </ul>
                                      </div>
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>شماره ثبت</h4>
                                      <input type="text" placeholder="شماره ثبت" v-model="form.registrationNumber">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>شناسه ملی</h4>
                                      <input type="text" placeholder="شناسه ملی" v-model="form.nationalID">
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>کد اقتصادی</h4>
                                      <input type="text" placeholder="کد اقتصادی" v-model="form.economicCode">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>صاحبان حق امضا</h4>
                                      <input type="text" placeholder="صاحبان حق امضا" v-model="form.signatureOwners">
                                  </div>
                              </div>
                          </div>
                          <div class="contactSeller">
                              <h3>اطلاعات تماس</h3>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>آدرس</h4>
                                      <input type="text" placeholder="آدرس" v-model="form.residenceAddress">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>کد پستی</h4>
                                      <input type="text" placeholder="کد پستی" v-model="form.post">
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>آدرس ایمیل</h4>
                                      <input type="text" placeholder="آدرس ایمیل" v-model="form.email">
                                  </div>
                              </div>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>تلفن ثابت</h4>
                                      <input type="text" placeholder="پیش شماره بدون صفر + شماره" v-model="form.landlinePhone">
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>شماره تماس</h4>
                                      <input type="text" placeholder="شماره تماس" v-model="form.number">
                                  </div>
                              </div>
                          </div>
                          <div class="contactSeller">
                              <h3>اطلاعات تجاری</h3>
                              <div class="personInfoItems">
                                  <div class="personInfoItem">
                                      <h4>نام کاربری</h4>
                                      <input type="text" placeholder="نام کاربری" v-model="form.userName">
                                  </div>
                                  <div class="personInfoItem">
                                      <h4>شماره شبا</h4>
                                      <input type="text" placeholder="شماره شبا" v-model="form.shaba">
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="uploadDocument">
                          <h3>تصویر کارت ملی</h3>
                          <a class="download" :href="form.frontImage" download>دانلود تصویر جلو کارت ملی</a>
                          <div class="sendGallery">
                              <show-image v-on:sendClose="getClose" v-if="showImage1" v-on:sendUrl="getUrl1"></show-image>
                              <div class="getImageItem" @click="showImage1 = !showImage1">
                                  <span v-if="form.frontImage == ''">تصویر جلو کارت ملی را وارد کنید</span>
                                  <div class="getImagePic" v-else>
                                      <img :src="form.frontImage">
                                  </div>
                              </div>
                          </div>
                          <a class="download" :href="form.backImage" download>دانلود تصویر پشت کارت ملی</a>
                          <div class="sendGallery">
                              <show-image v-on:sendClose="getClose" v-if="showImage2" v-on:sendUrl="getUrl2"></show-image>
                              <div class="getImageItem" @click="showImage2 = !showImage2">
                                  <span v-if="form.backImage == ''">تصویر پشت کارت ملی را وارد کنید</span>
                                  <div class="getImagePic" v-else>
                                      <img :src="form.backImage">
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="buttons">
                          <button @click="updateDocument">بروزرسانی</button>
                          <button @click="btnCancel">انصراف</button>
                      </div>
                  </div>
              </div>
          </div>
      </admin-layout>
</template>

<script>
import PaginatePanel from "../PaginatePanel";
import ShowImage from "../ShowImage";
import AllTable from "../Table/AllTable";
import SvgIcon from "../../Svg/SvgIcon";
import AdminLayout from "../../../components/layout/AdminLayout";
export default {
    name: "DocumentPanel",
    components: {AdminLayout,SvgIcon, AllTable, ShowImage, PaginatePanel},
    props:['documents','errors','labels','documentEdit'],
    metaInfo: {
        title: 'مدارک'
    },
    data(){
        return{
            showImage1: false,
            showImage2: false,
            showStatus : -1,
            value: [],
            document: [],
            form:{
                code: '',
                name: '',
                status: '',
                post: '',
                dateBirth: '',
                residenceAddress: '',
                landlinePhone: '',
                number: '',
                email: '',
                companyName: '',
                registrationNumber: '',
                nationalID: '',
                signatureOwners: '',
                economicCode: '',
                shaba: '',
                userName: '',
                gender: 0,
                type: 0,
                frontImage: '',
                backImage: '',
                taxId: '',
                update: 0,
                seller: 0,
            },
        }
    },
    methods:{
        getCheck(id) {
            this.value = id;
        },
        btnCancel(){
            this.document = '';
            this.form.taxId = '';
            this.form.update = 0;
        },
        updateDocument(){
            this.form.update = 1;
            const url = `/admin/document`;
            this.$inertia.post(url, this.form)
                .then(response => {
                    if(Object.keys(this.errors).length <= 0){
                        this.form.update = 0;
                        this.document = '';
                        this.form.taxId = '';
                    }
                })
        },
        openEdit(id) {
            this.form.taxId = id;
            const url = `/admin/document`;
            this.$inertia.post(url, {
                taxId: id
            })
                .then(response => {
                    this.document = this.documentEdit;
                    this.form.frontImage= this.documentEdit.frontNaturalId;
                    this.form.backImage= this.documentEdit.backNaturalId;
                    this.form.seller= this.documentEdit.user.seller;
                    this.form.status= this.documentEdit.status;
                    this.form.landlinePhone= this.documentEdit.user.landlinePhone;
                    this.form.number= this.documentEdit.user.number;
                    this.form.email= this.documentEdit.user.email;
                    this.form.shaba= this.documentEdit.user.shaba;
                    this.form.userName= this.documentEdit.user.name;
                    if(this.documentEdit.user.user_meta){
                        this.form.code = this.documentEdit.user.user_meta[0].code;
                        this.form.name = this.documentEdit.user.user_meta[0].name;
                        this.form.post = this.documentEdit.user.user_meta[0].post;
                        this.form.dateBirth = this.documentEdit.user.user_meta[0].date;
                        this.form.residenceAddress = this.documentEdit.user.user_meta[0].residenceAddress;
                    }
                    if(this.documentEdit.user.company){
                        this.form.companyName = this.documentEdit.user.company.name;
                        this.form.residenceAddress = this.documentEdit.user.company.residenceAddress;
                        this.form.registrationNumber = this.documentEdit.user.company.registration;
                        this.form.nationalID = this.documentEdit.user.company.NationalID;
                        this.form.signatureOwners = this.documentEdit.user.company.signer;
                        this.form.economicCode = this.documentEdit.user.company.economicCode;
                        if(this.documentEdit.user.company.type){
                            this.form.type = this.documentEdit.user.company.type;
                        }
                    }
                })
        },
        btnShowStatus(num){
            if (this.showStatus == num){
                this.showStatus = -1;
            }else{
                this.showStatus = num;
            }
        },
        btnRemoveArray(id) {
            if (id) {
                this.value = [id]
            }
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل حذف شده برگشتی ندارد!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    const url = `/admin/document`;
                    this.$inertia.post(url, {'value': this.value})
                        .then(response => {
                                this.value = []
                            }
                        )
                }
            })
        },
        btnGender(num){
            this.form.gender = num;
            this.showStatus = -1;
        },
        btnStatus(num){
            this.form.status = num;
            this.showStatus = -1;
        },
        btnSeller(num){
            this.form.seller = num;
            this.showStatus = -1;
        },
        btnType(num){
            this.form.type = num;
            this.showStatus = -1;
        },
        getClose(){
            this.showImage1 = false;
            this.showImage2 = false;
        },
        getUrl1(url){
            this.form.frontImage= url;
        },
        getUrl2(url){
            this.form.backImage= url;
        },
        sidebars() {
            this.$eventHub.emit('sidebar', 13);
        },
    },
    mounted() {
        this.sidebars();
    }
}
</script>

<style scoped>

</style>
