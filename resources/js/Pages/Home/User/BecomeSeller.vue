<template>
    <home-layout>
        <div class="allUserIndex width">
            <div class="allUserLists">
                <user-list tab="9"></user-list>
            </div>
            <div class="allBecomeSeller">
                <div class="allBecomeSellerTop">
                    <div class="allBecomeSellerLevels">
                        <div class="allBecomeSellerLevel" :title="$t('userInfo')">
                            <div class="allBecomeSellerLevelBarActive" v-if="form.level >= 2">
                            </div>
                            <div class="allBecomeSellerLevelBar" v-if="form.level == 1">
                            </div>
                            <div class="allBecomeSellerLevelCircleActives" v-if="form.level >= 2">
                                <svg-icon :icon="'#contact-info'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircleActive" v-if="form.level == 1">
                                <svg-icon :icon="'#contact-info'"></svg-icon>
                            </div>
                        </div>
                        <div class="allBecomeSellerLevel" :title="$t('sendDocuments')">
                            <div class="allBecomeSellerLevelBarActive" v-if="form.level >= 3">
                            </div>
                            <div class="allBecomeSellerLevelBar" v-if="form.level <= 2">
                            </div>
                            <div class="allBecomeSellerLevelCircleActives" v-if="form.level >= 3">
                                <svg-icon :icon="'#uploadDocuments'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircleActive" v-if="form.level == 2">
                                <svg-icon :icon="'#uploadDocuments'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircle" v-if="form.level <= 1">
                                <svg-icon :icon="'#uploadDocuments'"></svg-icon>
                            </div>
                        </div>
                        <div class="allBecomeSellerLevel" :title="$t('checkDocument')">
                            <div class="allBecomeSellerLevelBarActive" v-if="form.level >= 4">
                            </div>
                            <div class="allBecomeSellerLevelBar" v-if="form.level <= 3">
                            </div>
                            <div class="allBecomeSellerLevelCircleActives" v-if="form.level >= 4">
                                <svg-icon :icon="'#checkDocuments'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircleActive" v-if="form.level == 3">
                                <svg-icon :icon="'#checkDocuments'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircle" v-if="form.level <= 2">
                                <svg-icon :icon="'#checkDocuments'"></svg-icon>
                            </div>
                        </div>
                        <div class="allBecomeSellerLevel" :title="$t('sendSeller')">
                            <div class="allBecomeSellerLevelCircleActives" v-if="form.level >= 5">
                                <svg-icon :icon="'#welcomeSeller'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircleActive" v-if="form.level == 4">
                                <svg-icon :icon="'#welcomeSeller'"></svg-icon>
                            </div>
                            <div class="allBecomeSellerLevelCircle" v-if="form.level <= 3">
                                <svg-icon :icon="'#welcomeSeller'"></svg-icon>
                            </div>
                        </div>
                    </div>
                    <h4 v-if="form.level == 1">{{ $t('userInfo') }}</h4>
                    <p v-if="form.level == 1">{{ $t('userInfoBody') }}</p>
                    <h4 v-if="form.level == 2">{{ $t('sendDocuments') }}</h4>
                    <p v-if="form.level == 2">{{ $t('sendDocumentsBody') }}</p>
                    <h4 v-if="form.level == 3">{{ $t('checkDocument') }}</h4>
                    <p v-if="form.level == 3">{{ $t('checkDocumentBody') }}</p>
                    <h4 v-if="form.level == 4">{{ $t('sendSeller') }}</h4>
                    <p v-if="form.level == 4">{{ $t('sendSellerBody') }}</p>
                </div>
                <div class="allBecomeUserInfo" v-if="form.level == 1">
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <i>
                            <svg-icon :icon="'#error'"></svg-icon>
                        </i>
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <div class="allBecomeTip">
                        <i>
                            <svg-icon :icon="'#lamp'"></svg-icon>
                        </i>
                        <span>{{ $t('attention') }} :</span>
                        <p>{{$t('fillData')}}</p>
                    </div>
                    <div class="sellerType">
                        <h3>{{ $t('whichSeller') }}</h3>
                        <div class="allCategoryPanel" @click="btnShowStatus(1)">
                            <div class="categoryShow">
                                <h4 v-if="form.seller == 0">{{$t('realPerson')}}</h4>
                                <h4 v-if="form.seller == 1">{{ $t('legalPerson') }}</h4>
                                <i>
                                    <svg-icon :icon="'#down'"></svg-icon>
                                </i>
                            </div>
                            <ul v-if="showStatus == 1">
                                <li @click.stop="btnSeller(0)">{{$t('realPerson')}}</li>
                                <li @click.stop="btnSeller(1)">{{ $t('legalPerson') }}</li>
                            </ul>
                        </div>
                        <p v-if="form.seller == 0">{{$t('realPersonBody')}}</p>
                        <p v-if="form.seller == 1">{{ $t('legalPersonBody') }}</p>
                    </div>
                    <div class="personInfoSeller" v-if="form.seller == 0">
                        <h3>{{$t('userInfo')}}</h3>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('firstLastName')}}</h4>
                                <input type="text" :placeholder="$t('firstLastName')" v-model="form.name">
                            </div>
                            <div class="personInfoItem">
                                <h4>{{$t('nationalCode')}}</h4>
                                <input type="text" :placeholder="$t('nationalCode')" v-model="form.code">
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('genderSeller')}}</h4>
                                <div class="allCategoryPanel" @click="btnShowStatus(2)">
                                    <div class="categoryShow">
                                        <h4 v-if="form.gender == 0">{{$t('man')}}</h4>
                                        <h4 v-if="form.gender == 1">{{$t('woman')}}</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showStatus == 2">
                                        <li @click.stop="btnGender(0)">{{$t('man')}}</li>
                                        <li @click.stop="btnGender(1)">{{$t('woman')}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="personInfoItem">
                                <h4>{{$t('dateBirth')}}</h4>
                                <date-picker
                                    v-model="form.dateBirth"
                                    type="date"
                                    format="YYYY-MM-DD"
                                    display-format="jYYYY-jMM-jDD"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="personInfoSeller" v-if="form.seller == 1">
                        <h3>{{$t('companyInformation')}}</h3>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('companyName')}}</h4>
                                <input type="text" :placeholder="$t('companyName')" v-model="form.companyName">
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('companyType')}}</h4>
                                <div class="allCategoryPanel" @click="btnShowStatus(3)">
                                    <div class="categoryShow">
                                        <h4 v-if="form.type == 0">{{$t('publicStock')}}</h4>
                                        <h4 v-if="form.type == 1">{{$t('privateEquity')}}</h4>
                                        <h4 v-if="form.type == 2">{{$t('limitedResponsibility')}}</h4>
                                        <h4 v-if="form.type == 3">{{$t('cooperative')}}</h4>
                                        <h4 v-if="form.type == 4">{{$t('solidarity')}}</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showStatus == 3">
                                        <li @click.stop="btnType(0)">{{$t('publicStock')}}</li>
                                        <li @click.stop="btnType(1)">{{$t('privateEquity')}}</li>
                                        <li @click.stop="btnType(2)">{{$t('limitedResponsibility')}}</li>
                                        <li @click.stop="btnType(3)">{{$t('cooperative')}}</li>
                                        <li @click.stop="btnType(4)">{{$t('solidarity')}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="personInfoItem">
                                <h4>{{$t('registrationNumber')}}</h4>
                                <input type="text" :placeholder="$t('registrationNumber')" v-model="form.registrationNumber">
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{ $t('nationalID') }}</h4>
                                <input type="text" :placeholder="$t('nationalID')" v-model="form.nationalID">
                            </div>
                            <div class="personInfoItem">
                                <h4>{{ $t('economicCode') }}</h4>
                                <input type="text" :placeholder="$t('economicCode')" v-model="form.economicCode">
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('signatureOwners')}}</h4>
                                <input type="text" :placeholder="$t('signatureOwners')" v-model="form.signatureOwners">
                            </div>
                        </div>
                    </div>
                    <div class="contactSeller">
                        <h3>{{ $t('contacts') }}</h3>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('address')}}</h4>
                                <input type="text" :placeholder="$t('address')" v-model="form.residenceAddress">
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{$t('postalCode')}}</h4>
                                <input type="text" :placeholder="$t('postalCode')" v-model="form.post">
                            </div>
                            <div class="personInfoItem">
                                <h4>{{$t('emailAddress')}}</h4>
                                <div class="num" @click="changeEmail">{{form.email}}</div>
                            </div>
                        </div>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{ $t('landlinePhone') }}</h4>
                                <input type="text" :placeholder="$t('phoneBody')" v-model="form.landlinePhone">
                            </div>
                            <div class="personInfoItem">
                                <h4>{{ $t('phoneNumber') }}</h4>
                                <div class="num" @click="changeNum">{{form.number}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="contactSeller">
                        <h3>{{ $t('businessInformation') }}</h3>
                        <div class="personInfoItems">
                            <div class="personInfoItem">
                                <h4>{{ $t('userName') }}</h4>
                                <input type="text" :placeholder="$t('storeName')" v-model="form.userName">
                            </div>
                            <div class="personInfoItem">
                                <h4>{{$t('shabaNumber')}}</h4>
                                <input type="text" :placeholder="$t('shabaNumber')" v-model="form.shaba">
                            </div>
                        </div>
                    </div>
                    <div class="buttons" @click="sendLevel1">
                        <button>{{$t('send')}}</button>
                    </div>
                </div>
                <div class="uploadDocument" v-if="form.level == 2">
                    <h3>{{ $t('NationalCard') }}</h3>
                    <ul>
                        <li>{{$t('NationalCardBody1')}}</li>
                        <li>{{$t('NationalCardBody2')}}</li>
                        <li>{{$t('NationalCardBody3')}}</li>
                    </ul>
                    <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                        <i>
                            <svg-icon :icon="'#error'"></svg-icon>
                        </i>
                        <span>
                            {{errors[Object.keys(errors)[0]][0]}}
                        </span>
                    </div>
                    <div class="sendFileItem">
                        <dropzone ref="myVueDropzone3" id="dropzone" :options="dropzoneOptionsFile" :useCustomSlot=true v-on:vdropzone-success="uploadAllFront">
                            <div class="dropzone-custom-content">
                                <h3 class="dropzone-custom-title">{{$t('sendFrontNaturalId')}}</h3>
                                <div class="subtitle">{{$t('dropImage')}}</div>
                            </div>
                        </dropzone>
                    </div>
                    <div class="sendFileItem">
                        <dropzone ref="myVueDropzone3" id="dropzone" :options="dropzoneOptionsFile" :useCustomSlot=true v-on:vdropzone-success="uploadAllBack">
                            <div class="dropzone-custom-content">
                                <h3 class="dropzone-custom-title">{{$t('sendBackNaturalId')}}</h3>
                                <div class="subtitle">{{$t('dropImage')}}</div>
                            </div>
                        </dropzone>
                    </div>
                    <div class="buttons" @click="sendLevel2">
                        <button>{{$t('send')}}</button>
                    </div>
                </div>
                <div class="checkUploaded" v-if="form.level == 3">
                    <h3>{{$t('documentStatus')}}</h3>
                    <table>
                        <tr>
                            <div>
                                <th>#</th>
                                <th>{{$t('frontNaturalId')}}</th>
                                <th>{{$t('backNaturalId')}}</th>
                                <th>{{$t('documentStatus')}}</th>
                                <th>{{$t('dateRegistration')}}</th>
                            </div>
                        </tr>
                        <tr v-for="(item, index) in documents" :key="index">
                            <div>
                                <td>
                                    <span>{{++index}}</span>
                                </td>
                                <td>
                                    <span>
                                        <img :src="item.frontNaturalId" :alt="$t('frontNaturalId')">
                                    </span>
                                </td>
                                <td>
                                    <span>
                                        <img :src="item.backNaturalId" :alt="$t('backNaturalId')">
                                    </span>
                                </td>
                                <td>
                                    <span>
                                        <span v-if="item.status == 0">{{ $t('awaitingReview') }}</span>
                                        <span v-if="item.status == 1" class="unActive">{{$t('failed')}}</span>
                                        <span v-if="item.status == 2" class="activeStatus">{{$t('accepted')}}</span>
                                    </span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                            </div>
                        </tr>
                    </table>
                    <div class="buttons">
                        <button v-if="checkDocument == 1" @click="btnSendAgain">{{$t('sendAgain2')}}</button>
                        <button v-if="checkDocument == 2" @click="btnFinished">{{$t('finished')}}</button>
                    </div>
                </div>
                <div class="welcomeSeller"  v-if="form.level == 4">
                    <i>
                        <svg-icon :icon="'#online-shop'"></svg-icon>
                    </i>
                    <h2>{{ $t('congratulations') }}</h2>
                    <h3>{{$t('welcomeSeller')}}</h3>
                    <p>{{$t('welcomeSellerBody')}}</p>
                    <div class="nextButton">
                        <inertia-link href="/profile/product">{{ $t('allProduct') }}</inertia-link>
                        <inertia-link href="/profile/product/create">{{ $t('addProduct') }}</inertia-link>
                        <inertia-link href="/profile/product/pay">{{ $t('listSell') }}</inertia-link>
                    </div>
                </div>
            </div>
            <div class="showChangeNum" v-if="showChangeNum == 1 || showChangeNum == 2">
                <div class="showChangeNumItems">
                    <div class="showChangeNumContainer" v-if="showChangeNum == 1">
                        <label>{{ $t('phoneNumber') }}</label>
                        <div class="alert" v-if="errors['number']">
                            {{errors['number'][0]}}
                        </div>
                        <input @keyup="phoneFormat" type="text" v-model="phone" id="checkPhone" :placeholder="$t('phoneNumber')" maxlength="17" />
                        <div class="buttons">
                            <svg-icon v-if="loading" :icon="'#loading'"></svg-icon>
                            <button v-if="!loading" @click="btnSendCode">{{ $t('send') }}</button>
                            <button @click="showChangeNum = 0">{{ $t('cancel') }}</button>
                        </div>
                    </div>
                    <transition name="slide-fade">
                        <div class="allHeaderIndexRegisterItemsContainer" v-if="showChangeNum == 2">
                            <div class="allHeaderIndexRegisterItem">
                                <label>{{ $t('activeCode') }}</label>
                                <div class="alert" v-if="errors2['code']">
                                    {{errors2['code'][0]}}
                                </div>
                                <input v-model="code" type="text" :placeholder="$t('activeCode')"/>
                            </div>
                            <div class="allHeaderIndexRegisterItemsContainerButton">
                                <svg-icon v-if="loading" :icon="'#loading'"></svg-icon>
                                <button v-if="!loading" @click="btnCheckCode">{{ $t('send') }}</button>
                                <button @click="showChangeNum = 0">{{ $t('cancel') }}</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div class="showChangeNum" v-if="showChangeNum == 5 || showChangeNum == 6">
                <div class="showChangeNumItems">
                    <div class="showChangeNumContainer" v-if="showChangeNum == 5">
                        <label>{{ $t('emailAddress') }}</label>
                        <div class="alert" v-if="errors['email']">
                            {{errors['email'][0]}}
                        </div>
                        <input type="text" v-model="email" :placeholder="$t('emailAddress')"/>
                        <div class="buttons">
                            <svg-icon v-if="loading" :icon="'#loading'"></svg-icon>
                            <button v-if="!loading" @click="btnSendCodeEmail">{{ $t('send') }}</button>
                            <button @click="showChangeNum = 0">{{ $t('cancel') }}</button>
                        </div>
                    </div>
                    <transition name="slide-fade">
                        <div class="allHeaderIndexRegisterItemsContainer" v-if="showChangeNum == 6">
                            <div class="allHeaderIndexRegisterItem">
                                <label>{{ $t('activeCode') }}</label>
                                <div class="alert" v-if="errors2['code']">
                                    {{errors2['code'][0]}}
                                </div>
                                <input v-model="code" type="text" :placeholder="$t('activeCode')"/>
                            </div>
                            <div class="allHeaderIndexRegisterItemsContainerButton">
                                <svg-icon v-if="loading" :icon="'#loading'"></svg-icon>
                                <button v-if="!loading" @click="btnCheckCodeEmail">{{ $t('send') }}</button>
                                <button @click="showChangeNum = 0">{{ $t('cancel') }}</button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import UserList from "./UserList";
import HomeLayout from "../../../components/layout/HomeLayout";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import SvgIcon from "../../Svg/SvgIcon";
import Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
export default {
    name: "BecomeSeller",
    components:{
        Dropzone,
        UserList,
        HomeLayout,
        datePicker: VuePersianDatetimePicker,
        SvgIcon,
    },
    props:['users','levels','documents','errors'],
    metaInfo: {
        title: 'فروشنده شوید'
    },
    data() {
        return {
            showStatus : -1,
            showChangeNum: false,
            sendAgain:false,
            code: '',
            phone: '',
            email: '',
            loading: false,
            errors2: [],
            checkDocument: 0,
            form:{
                level: this.levels,
                code: '',
                name: '',
                post: '',
                dateBirth: '',
                residenceAddress: '',
                landlinePhone: this.users.landlinePhone,
                number: this.users.number,
                email: this.users.email,
                companyName: '',
                registrationNumber: '',
                nationalID: '',
                signatureOwners: '',
                economicCode: '',
                shaba: this.users.shaba,
                userName: this.users.name,
                seller : 0,
                gender: 0,
                type: 0,
                frontImage: '',
                backImage: '',
            },
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
            dropzoneOptionsFile: {
                url: '/upload-image',
                timeout: 999999999999999999999999999999999999,
                maxFilesize: 2,
                addRemoveLinks: true,
                parallelUploads: 1,
                maxFiles: 1,
                maxThumbnailFilesize: 100,
                dictDefaultMessage: 'یک فایل PDF را در اینجا رها کنید یا برای انتخاب پرونده برای بارگذاری کلیک کنید.',
                dictFileTooBig: 'فایل ارسالی باید کمتر از 2 مگابایت باشد',
                dictFallbackMessage: 'مرورگر شما از بارگذاری پرونده drag\'n\'drop پشتیبانی نمی کند.',
                dictFallbackText: 'لطفاً برای بارگذاری پرونده های خود مانند روزهای گذشته از فرم بازگشت به پایین استفاده کنید.',
                dictInvalidFileType: 'نمی توانید پرونده هایی از این نوع را بارگذاری کنید.',
                dictResponseError: 'سرور با کد {{statusCode}} پاسخ داد.',
                dictCancelUpload: 'لغو بارگذاری',
                dictCancelUploadConfirmation: 'آیا مطمئن هستید که می خواهید این بارگذاری را لغو کنید؟',
                dictRemoveFile: 'حذف فایل',
                dictMaxFilesExceeded : 'دیگر نمی توانید پرونده بارگذاری کنید.',
                headers: {'X-CSRF-TOKEN': document.head.querySelector("[name=csrf-token]").content}
            },
        }
    },
    methods:{
        btnFinished(){
            const url = `/become-seller`;
            this.$inertia.post(url,this.form)
                .then(response=>{
                    this.form.level = 4;
                })
        },
        btnSendAgain(){
            this.form.level = 2;
            this.checkDocument = 0;
        },
        uploadAllFront(file , response){
            this.form.frontImage = response.url;
        },
        uploadAllBack(file , response){
            this.form.backImage = response.url;
        },
        getData(){
            if(this.users.user_meta){
                this.form.code = this.users.user_meta[0].code;
                this.form.name = this.users.user_meta[0].name;
                this.form.post = this.users.user_meta[0].post;
                this.form.dateBirth = this.users.user_meta[0].date;
                this.form.residenceAddress = this.users.user_meta[0].residenceAddress;
            }
            if(this.users.company){
                this.form.companyName = this.users.company.name;
                this.form.residenceAddress = this.users.company.residenceAddress;
                this.form.registrationNumber = this.users.company.registration;
                this.form.nationalID = this.users.company.NationalID;
                this.form.signatureOwners = this.users.company.signer;
                this.form.economicCode = this.users.company.economicCode;
                if(this.users.company.type){
                    this.form.type = this.users.company.type;
                }
            }
            if (this.documents.length){
                this.checkDocument = this.documents[0].status;
            }
        },
        sendLevel2(){
            const url = `/become-seller`;
            this.$inertia.post(url,this.form)
                .then(response=>{
                    if(Object.keys(this.errors).length <= 0){
                        this.form.level = 3;
                    }
                })
        },
        sendLevel1(){
            const url = `/become-seller`;
            this.$inertia.post(url,this.form)
                .then(response=>{
                    if(Object.keys(this.errors).length <= 0){
                        if(this.documents.length){
                            this.form.level = 3;
                        }else{
                            this.form.level = 2;
                        }
                    }
                })
        },
        btnCheckCode(){
            this.loading = !this.loading;
            const url  = '/profile/check-code';
            axios.post(url,{
                phone : this.phone,
                code : this.code,
            })
                .then(response=>{
                    if(response.data == 'success'){
                        this.$toast.success('انجام شد', 'تغییر شماره با موفقیت انجام شد', this.notificationSystem.options.success);
                        this.showChangeNum= 0;
                        this.form.number = this.$page.userData.email;
                    }else{
                        this.$toast.error('انجام نشد', 'کد ارسالی اشتباه است', this.notificationSystem.options.error);
                    }
                    this.loading = !this.loading;
                })
                .catch((error)=>{
                    this.errors2 = error.response.data.errors;
                    this.loading = !this.loading;
                });
        },
        btnCheckCodeEmail(){
            this.loading = !this.loading;
            const url  = '/profile/check-email';
            axios.post(url,{
                phone : this.email,
                code : this.code,
            })
                .then(response=>{
                    if(response.data == 'success'){
                        this.$toast.success('انجام شد', 'تغییر ایمیل با موفقیت انجام شد', this.notificationSystem.options.success);
                        this.showChangeNum= 0;
                        this.form.email = this.$page.userData.email;
                    }else{
                        this.$toast.error('انجام نشد', 'کد ارسالی اشتباه است', this.notificationSystem.options.error);
                    }
                    this.loading = !this.loading;
                })
                .catch((error)=>{
                    this.errors2 = error.response.data.errors;
                    this.loading = !this.loading;
                });
        },
        changeNum(){
            this.phone = '';
            this.code = '';
            this.showChangeNum = 1;
        },
        changeEmail(){
            this.email = '';
            this.code = '';
            this.showChangeNum = 5;
        },
        phoneFormat(){
            const isNumericInput = (event) => {
                const key = event.keyCode;
                return ((key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105)
                );
            };

            const isModifierKey = (event) => {
                const key = event.keyCode;
                return (event.shiftKey === true || key === 35 || key === 36) ||
                    (key === 8 || key === 9 || key === 13 || key === 46) ||
                    (key > 36 && key < 41) ||
                    (
                        (event.ctrlKey === true || event.metaKey === true) &&
                        (key === 65 || key === 67 || key === 86 || key === 88 || key === 90)
                    )
            };

            const enforceFormat = (event) => {
                if(!isNumericInput(event) && !isModifierKey(event)){
                    event.preventDefault();
                }
            };

            const formatToPhone = (event) => {
                if(isModifierKey(event)) {return;}

                const input = event.target.value.replace(/\D/g,'').substring(0,17);
                const areaCode = input.substring(0,4);
                const middle = input.substring(4,7);
                const last = input.substring(7,17);

                if(input.length > 7){event.target.value = `${areaCode} - ${middle} - ${last}`;}
                else if(input.length > 4){event.target.value = `${areaCode} - ${middle}`;}
                else if(input.length > 0){event.target.value = `${areaCode}`;}
            };

            const inputElement = document.getElementById('checkPhone');
            inputElement.addEventListener('keydown',enforceFormat);
            inputElement.addEventListener('keyup',formatToPhone);
        },
        btnSendCode(){
            this.loading = !this.loading;
            const url  = '/check-auth';
            axios.post(url,{
                number : this.phone,
                show : 1
            })
                .then(response=>{
                    if(response.data == 3){
                        this.showChangeNum= 2;
                    }else{
                        this.$toast.error('انجام نشد', 'شماره تماس وجود دارد', this.notificationSystem.options.error);
                    }
                    this.loading = !this.loading;
                })
                .catch((error)=>{
                    this.loading = !this.loading;
                    this.$toast.error('انجام نشد', 'شماره تماس وجود دارد', this.notificationSystem.options.error);
                });
        },
        btnSendCodeEmail(){
            this.loading = !this.loading;
            const url  = '/check-email';
            axios.post(url,{
                email : this.email,
                show : 1
            })
                .then(response=>{
                    if(response.data == 3){
                        this.showChangeNum= 6;
                    }else{
                        this.$toast.error('انجام نشد', 'ایمیل وجود دارد', this.notificationSystem.options.error);
                    }
                    this.loading = !this.loading;
                })
                .catch((error)=>{
                    this.loading = !this.loading;
                    this.$toast.error('انجام نشد', 'ایمیل وجود دارد', this.notificationSystem.options.error);
                });
        },
        btnSeller(num){
            this.form.seller = num;
            this.showStatus = -1;
        },
        btnGender(num){
            this.form.gender = num;
            this.showStatus = -1;
        },
        btnType(num){
            this.form.type = num;
            this.showStatus = -1;
        },
        btnShowStatus(num){
            if (this.showStatus == num){
                this.showStatus = -1;
            }else{
                this.showStatus = num;
            }
        }
    },
    mounted() {
        this.getData();
    }
}
</script>

<style scoped>

</style>
