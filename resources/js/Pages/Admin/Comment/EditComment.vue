<template>
    <admin-layout>
        <div class="allEditComment">
            <div class="allPayPanelTop">
                <h1>ویرایش دیدگاه ها</h1>
                <div class="allPayPanelTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/comment">دیدگاه ها</inertia-link>
                </div>
            </div>
            <div class="allCommentContainerSend">
                <div class="allCommentContainerTop">
                    <div class="allCommentContainerTopPic">
                        <img v-lazy="JSON.parse(comments.post.image)[0]" :alt="comments.post.title">
                    </div>
                    <div class="allCommentContainerTopItems">
                        <div class="allCommentContainerTopItemsTitle">
                            <h3>{{comments.post.title}}</h3>
                        </div>
                        <div class="allCommentContainerTopItemsRate">
                            <div class="allCommentContainerTopItemsRateItem" v-for="item in form.rate">
                                <label>{{item.name}}</label>
                                <div class="rateItemsCount">
                                    <div class="rateItemsCountItem">
                                        <div class="rateItemsCountItemBarActive" v-if="item.rate >= 1"></div>
                                        <div class="rateItemsCountItemBar" v-if="item.rate == 0"></div>
                                        <div class="rateItemsCountItemCircleActives" @click.prevent="item.rate = 0" v-if="item.rate >= 1"></div>
                                        <div class="rateItemsCountItemCircleActive" @click.prevent="item.rate = 0" v-if="item.rate == 0"></div>
                                    </div>
                                    <div class="rateItemsCountItem">
                                        <div class="rateItemsCountItemBarActive" v-if="item.rate >= 2"></div>
                                        <div class="rateItemsCountItemBar" v-if="item.rate <= 1"></div>
                                        <div class="rateItemsCountItemCircleActives" @click.prevent="item.rate = 1" v-if="item.rate >= 2"></div>
                                        <div class="rateItemsCountItemCircleActive" @click.prevent="item.rate = 1" v-if="item.rate == 1"></div>
                                        <div class="rateItemsCountItemCircle" @click.prevent="item.rate = 1" v-if="item.rate <= 0"></div>
                                    </div>
                                    <div class="rateItemsCountItem">
                                        <div class="rateItemsCountItemBarActive" v-if="item.rate >= 3"></div>
                                        <div class="rateItemsCountItemBar" v-if="item.rate <= 2"></div>
                                        <div class="rateItemsCountItemCircleActives" @click.prevent="item.rate = 2" v-if="item.rate >= 3"></div>
                                        <div class="rateItemsCountItemCircleActive" @click.prevent="item.rate = 2" v-if="item.rate == 2"></div>
                                        <div class="rateItemsCountItemCircle" @click.prevent="item.rate = 2" v-if="item.rate <= 1"></div>
                                    </div>
                                    <div class="rateItemsCountItem">
                                        <div class="rateItemsCountItemBarActive" v-if="item.rate >= 4"></div>
                                        <div class="rateItemsCountItemBar" v-if="item.rate <= 3"></div>
                                        <div class="rateItemsCountItemCircleActives" @click.prevent="item.rate = 3" v-if="item.rate >= 4"></div>
                                        <div class="rateItemsCountItemCircleActive" @click.prevent="item.rate = 3" v-if="item.rate == 3"></div>
                                        <div class="rateItemsCountItemCircle" @click.prevent="item.rate = 3" v-if="item.rate <= 2"></div>
                                    </div>
                                    <div class="rateItemsCountItem">
                                        <div class="rateItemsCountItemCircleActive" @click.prevent="item.rate = 4" v-if="item.rate == 4"></div>
                                        <div class="rateItemsCountItemCircle" @click.prevent="item.rate = 4" v-if="item.rate <= 3"></div>
                                    </div>
                                    <span v-if="item.rate == 0">خیلی بد</span>
                                    <span v-if="item.rate == 1">بد</span>
                                    <span v-if="item.rate == 2">متوسط</span>
                                    <span v-if="item.rate == 3">خوب</span>
                                    <span v-if="item.rate == 4">عالی</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allCommentContainerBottom">
                    <div class="allCommentContainerBottomItem">
                        <label>عنوان نظر شما</label>
                        <label>
                            <input type="text" placeholder="عنوان نظر خود را وارد کنید" v-model="form.title">
                        </label>
                    </div>
                    <div class="allCommentContainerBottomItem">
                        <div class="allCommentContainerBottomItemTitle">
                            <i>
                                <svg-icon :icon="'#circle'"></svg-icon>
                            </i>
                            <label>نقاط قوت</label>
                        </div>
                        <label>
                            <input type="text" placeholder="نقاط قوت را وارد کنید" v-model="form.good" @keyup.enter="addGood">
                            <i @click.prevent="addGood" v-if="form.good">
                                <svg-icon :icon="'#plus'"></svg-icon>
                            </i>
                        </label>
                        <span v-for="(item , index) in form.goods">
                            {{item}}
                            <i @click.prevent="removeGood(index)">
                                <svg-icon :icon="'#cancel'"></svg-icon>
                            </i>
                        </span>
                    </div>
                    <div class="allCommentContainerBottomItem">
                        <div class="allCommentContainerBottomItemTitle">
                            <i>
                                <svg-icon :icon="'#circle'"></svg-icon>
                            </i>
                            <label>نقاط ضعف</label>
                        </div>
                        <label>
                            <input type="text" placeholder="نقاط ضعف را وارد کنید" v-model="form.bad" @keyup.enter="addBad">
                            <i @click.prevent="addBad" v-if="form.bad">
                                <svg-icon :icon="'#plus'"></svg-icon>
                            </i>
                        </label>
                        <span v-for="(item , index) in form.bads">
                            {{item}}
                            <i @click.prevent="removeBad(index)">
                                <svg-icon :icon="'#cancel'"></svg-icon>
                            </i>
                        </span>
                    </div>
                    <div class="allCommentContainerBottomItem">
                        <label>متن نظر شما</label>
                        <textarea v-model="form.body" placeholder="متن نظر خود را وارد کنید"></textarea>
                    </div>
                    <div class="allCommentContainerBottomSuggests">
                        <div class="allCommentContainerBottomSuggest">
                            <label>آیا خرید این محصول را به دوستانتان پیشنهاد می کنید؟</label>
                            <div class="allCategoryPanel" @click="showStatus = !showStatus">
                                <div class="categoryShow">
                                    <h4 v-if="form.status == 0">نظری ندارم</h4>
                                    <h4 v-if="form.status == 1">خیر ، پیشنهاد نمی‌کنم</h4>
                                    <h4 v-if="form.status == 2">پیشنهاد می‌کنم</h4>
                                    <i>
                                        <svg-icon :icon="'#down'"></svg-icon>
                                    </i>
                                </div>
                                <ul v-if="showStatus">
                                    <li @click="form.status = 0">نظری ندارم</li>
                                    <li @click="form.status = 1">خیر ، پیشنهاد نمی‌کنم</li>
                                    <li @click="form.status = 2">پیشنهاد می‌کنم</li>
                                </ul>
                            </div>
                        </div>
                        <div class="allCommentContainerBottomSuggest">
                            <label>وضعیت</label>
                            <div class="allCategoryPanel" @click="showApproved = !showApproved">
                                <div class="categoryShow">
                                    <h4 v-if="form.approved == 0">در حال بررسی</h4>
                                    <h4 v-if="form.approved == 1">تایید شد</h4>
                                    <i>
                                        <svg-icon :icon="'#down'"></svg-icon>
                                    </i>
                                </div>
                                <ul v-if="showApproved">
                                    <li @click="form.approved = 0">در حال بررسی</li>
                                    <li @click="form.approved = 1">تایید شد</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="allCommentButtons">
                    <button @click.prevent="sendComment">
                        <i>
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </i>
                        ارسال نظر
                        <img src="/img/loaderHome.gif" alt="صبر کنید ..." v-if="showLoader">
                    </button>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import SvgIcon from "../../Svg/SvgIcon";
export default {
    name: "EditComment",
    components:{
        AdminLayout,
        SvgIcon
    },
    props:['comments'],
    metaInfo: {
        title: 'ویرایش دیدگاه'
    },
    data() {
        return {
            form:{
                goods : [],
                body : this.comments.body,
                good : '',
                approved : this.comments.approved,
                rate : [],
                title : this.comments.title,
                bad : '',
                status : this.comments.status,
                bads : [],
            },
            showLoader : false,
            showApproved : false,
            showStatus : false,
            i : 0,
        }
    },
    methods:{
        removeGood(index){
            this.form.goods.splice(index , 1);
        },
        addGood(){
            if (this.form.good != ''){
                this.form.goods.push(this.form.good);
                this.form.good = '';
            }
        },
        removeBad(index){
            this.form.bads.splice(index , 1);
        },
        addBad(){
            if (this.form.bad != ''){
                this.form.bads.push(this.form.bad);
                this.form.bad = '';
            }
        },
        sendComment(){
            const url = `/admin/comment/${this.comments.id}`;
            this.$inertia.post(url ,this.form)
        },
        getData(){
            this.$eventHub.emit('sidebar' , 5);
            for ( this.i ; this.i <  JSON.parse(this.comments.rate).length; this.i++) {
                this.form.rate.push({rate : '',name : '',});
                this.form.rate[this.i].name = JSON.parse(this.comments.rate)[this.i].name;
                this.form.rate[this.i].rate = JSON.parse(this.comments.rate)[this.i].rate;
            }
            for ( this.i = 0 ; this.i <  JSON.parse(this.comments.good).length; this.i++) {
                this.form.goods.push(JSON.parse(this.comments.good)[this.i]);
            }
            for ( this.i = 0 ; this.i <  JSON.parse(this.comments.bad).length; this.i++) {
                this.form.bads.push(JSON.parse(this.comments.bad)[this.i]);
            }
        },
    },
    mounted() {
        this.getData();
    },
}
</script>
