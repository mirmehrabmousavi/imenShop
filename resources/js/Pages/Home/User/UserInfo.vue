<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="6"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <label>{{$t('account')}}</label>
                <div class="errorsCreate" v-if="Object.keys(errors).length > 0">
                    <i>
                        <svg-icon :icon="'#error'"></svg-icon>
                    </i>
                    <span>
                               {{errors[Object.keys(errors)[0]][0]}}
                            </span>
                </div>
                <div class="allUserIndexInfoPersonal">
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('firstLastName')}}</label>
                            <input type="text" :placeholder="$t('firstLastName')" v-model="form.name">
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('userName')}}</label>
                            <input type="text" :placeholder="$t('userName')" v-model="form.user">
                        </div>
                    </div>
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('phoneNumber')}}</label>
                            <h4 @click="changeNum">{{form.number}}</h4>
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{ $t('emailAddress') }}</label>
                            <h4 @click="changeEmail">{{form.email}}</h4>
                        </div>
                    </div>
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('dateBirth')}}</label>
                            <div class="allUserIndexInfoPersonalItemDate">
                                <date-picker
                                    v-model="form.date"
                                    type="date"
                                    format="YYYY-MM-DD"
                                    display-format="jYYYY-jMM-jDD"
                                />
                            </div>
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('job')}}</label>
                            <input type="text" :placeholder="$t('job')" v-model="form.job">
                        </div>
                    </div>
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('nationalCode')}}</label>
                            <input type="text" :placeholder="$t('nationalCode')" v-model="form.code">
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('postalCode')}}</label>
                            <input type="text" :placeholder="$t('postalCode')" v-model="form.post">
                        </div>
                    </div>
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('editPassword')}}</label>
                            <input type="password" :placeholder="$t('editPassword')" v-model="form.password">
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('residenceAddress')}}</label>
                            <input type="text" :placeholder="$t('residenceAddress')" v-model="form.address">
                        </div>
                    </div>
                    <div class="allUserIndexInfoPersonalItems">
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('shabaNumber')}}</label>
                            <input type="text" :placeholder="$t('shabaNumber')" v-model="form.shaba">
                        </div>
                        <div class="allUserIndexInfoPersonalItem">
                            <label>{{$t('landlinePhone')}}</label>
                            <input type="text" :placeholder="$t('landlinePhone')" v-model="form.landlinePhone">
                        </div>
                    </div>
                </div>
                <button class="infoButton" @click.prevent="updateUser">{{$t('changeInformation')}}</button>
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
import VuePersianDatetimePicker from 'vue-persian-datetime-picker'
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import UserList from "./UserList";
export default {
    name: "UserInfo",
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        datePicker: VuePersianDatetimePicker,
    },
    props: ['user','errors','title'],
    data() {
        return {
            showLoader : false,
            form:{
                date: '',
                address: '',
                password: '',
                name: '',
                shaba: '',
                user: '',
                number: '',
                email: '',
                post: '',
                job: '',
                code: '',
                landlinePhone: '',
            },
            sendAgain:false,
            showChangeNum: false,
            code: '',
            phone: '',
            email: '',
            loading: false,
            errors2: [],
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
    metaInfo() {
        return {
            title: `اطلاعات شخصی - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                reptilian: 'gator',
                amp: true
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    methods:{
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
                        window.location.reload();
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
                        window.location.reload();
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
        updateUser() {
            const url = `/change-all-user-info/${this.user.id}`;
            this.$inertia.put(url , this.form)
                .then(response=>{
                    this.$toast.success('انجام شد', 'تغییر با موفقیت انجام شد', this.notificationSystem.options.success);
                })
                .catch(err =>{
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
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
        check(){
            if(this.user.user_meta.length){
                this.form.date = this.user.user_meta[0].date;
                this.form.name = this.user.user_meta[0].name;
                this.form.post = this.user.user_meta[0].post;
                this.form.job = this.user.user_meta[0].job;
                this.form.code = this.user.user_meta[0].code;
                this.form.address = this.user.user_meta[0].address;
            }
            this.form.user = this.user.name;
            this.form.number = this.user.number;
            this.form.email = this.user.email;
            this.form.shaba = this.user.shaba;
            this.form.landlinePhone = this.user.landlinePhone;
            this.form.password = this.user.password;
        },
    },
    mounted(){
        this.check();
    }
}
</script>

