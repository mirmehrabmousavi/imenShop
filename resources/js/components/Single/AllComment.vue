<template>
    <div class="allComment">
        <div class="allCommentContainerSend" v-if="showSendLoader">
            <div class="allCommentContainerTop">
                <div class="allCommentContainerTopPic">
                    <img :src="JSON.parse(posts.image)[0]" :alt="posts.title">
                </div>
                <div class="allCommentContainerTopItems">
                    <div class="allCommentContainerTopItemsTitle">
                        <h3>{{posts.title}}</h3>
                    </div>
                    <div class="allCommentContainerTopItemsRate">
                        <div class="allCommentContainerTopItemsRateItem" v-for="item in rates">
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
                                <span v-if="item.rate == 0">{{$t('veryBad')}}</span>
                                <span v-if="item.rate == 1">{{$t('bad')}}</span>
                                <span v-if="item.rate == 2">{{$t('medium')}}</span>
                                <span v-if="item.rate == 3">{{$t('good')}}</span>
                                <span v-if="item.rate == 4">{{$t('awesome')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="allCommentContainerBottom">
                <div class="allCommentContainerBottomCoercion" v-if="coercion == 1">
                    <div class="allCommentContainerBottomCoercionItem">
                        <label>{{$t('enterName')}}</label>
                        <label>
                            <input type="text" :placeholder="$t('enterName')" v-model="UserName">
                        </label>
                    </div>
                    <div class="allCommentContainerBottomCoercionItem">
                        <label>{{$t('emailAddress')}}</label>
                        <label>
                            <input type="email" :placeholder="$t('emailAddress')" v-model="emailUser">
                        </label>
                    </div>
                </div>
                <div class="allCommentContainerBottomItem">
                    <label>{{$t('enterTitle')}}</label>
                    <label>
                        <input type="text" :placeholder="$t('enterTitle')" v-model="title">
                    </label>
                </div>
                <div class="allCommentContainerBottomItem">
                    <div class="allCommentContainerBottomItemTitle">
                        <i>
                            <svg-icon :icon="'#circle'"></svg-icon>
                        </i>
                        <label>{{$t('strengths')}}</label>
                    </div>
                    <label>
                        <input type="text" :placeholder="$t('strengths')" v-model="good" @keyup.enter="addGood">
                        <i @click.prevent="addGood" v-if="good">
                            <svg-icon :icon="'#plus2'"></svg-icon>
                        </i>
                    </label>
                    <span v-for="(item , index) in goods">
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
                        <label>{{$t('weakPoints')}}</label>
                    </div>
                    <label>
                        <input type="text" :placeholder="$t('weakPoints')" v-model="bad" @keyup.enter="addBad">
                        <i @click.prevent="addBad" v-if="bad">
                            <svg-icon :icon="'#plus2'"></svg-icon>
                        </i>
                    </label>
                    <span v-for="(item , index) in bads">
                        {{item}}
                        <i @click.prevent="removeBad(index)">
                            <svg-icon :icon="'#cancel'"></svg-icon>
                        </i>
                    </span>
                </div>
                <div class="allCommentContainerBottomItem">
                    <label>{{$t('textComment')}}</label>
                    <textarea v-model="body" :placeholder="$t('textComment')"></textarea>
                </div>
                <div class="allCommentContainerBottomSuggest">
                    <label>{{$t('recommendComment')}}</label>
                    <div class="allCategoryPanel" @click="showStatus = !showStatus">
                        <div class="categoryShow">
                            <h4 v-if="status == 0">{{$t('noIdea')}}</h4>
                            <h4 v-if="status == 1">{{$t('notSuggest')}}</h4>
                            <h4 v-if="status == 2">{{$t('iSuggest')}}</h4>
                            <i>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <ul v-if="showStatus">
                            <li @click="status = 0">{{$t('noIdea')}}</li>
                            <li @click="status = 1">{{$t('notSuggest')}}</li>
                            <li @click="status = 2">{{$t('iSuggest')}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="allCommentButtons">
                <button @click.prevent="sendComment">
                    <i v-if="showLoader">
                        <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                    </i>
                    <i v-else>
                        <svg-icon :icon="'#comment'"></svg-icon>
                    </i>
                    {{$t('send')}}
                </button>
                <button @click.prevent="showSendLoader = !showSendLoader">{{$t('cancel')}}</button>
            </div>
        </div>
        <div class="allCommentAllow" v-if="showSendLoader == false">
            <div class="allCommentAllowTitle" v-if="rates.length">
                {{$t('sellerPoints')}}
            </div>
            <div class="allCommentAllowItem">
                <div class="allCommentAllowItemRate" v-if="rates.length">
                    <ul>
                        <li v-for="item in rates">
                            <span>{{item.name}}</span>
                            <div class="allSingleHomeDetailBodyItemRate">
                                <div class="allSingleHomeDetailBodyItemRateValue" :style="{'width' : item.rate * '25' +'%'}"></div>
                            </div>
                            <span v-if="item.rate == 0">{{$t('veryBad')}}</span>
                            <span v-if="item.rate == 1">{{$t('bad')}}</span>
                            <span v-if="item.rate == 2">{{$t('medium')}}</span>
                            <span v-if="item.rate == 3">{{$t('good')}}</span>
                            <span v-if="item.rate == 4">{{$t('awesome')}}</span>
                        </li>
                    </ul>
                </div>
                <div class="allCommentAllowItemBtn">
                    <h5>{{$t('YouCanComment')}}</h5>
                    <p>{{$t('firstLogComment')}}</p>
                    <button @click.prevent="showSendLoader = !showSendLoader">{{$t('addComment')}}</button>
                </div>
            </div>
        </div>
        <div class="allCommentContainerGet">
            <div class="allCommentContainerGetTitle">
                <i>
                    <svg-icon :icon="'#down'"></svg-icon>
                </i>
                <span>{{$t('userComments')}}</span>
            </div>
            <div class="allCommentContainerGetItems">
                <i class="showGetLoader" v-if="showGetLoader">
                    <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                </i>
                <div class="paginate" v-if="comments.links">
                    <paginate-panel :link="comments.links"></paginate-panel>
                </div>
                <div class="allCommentContainerGetItem" v-for="(item , index) in comments.data">
                    <div class="allCommentContainerGetUser">
                        <div class="allCommentContainerUser">
                            <div class="allCommentContainerUserPic" v-if="showUser == 0 || showUser== 2">
                                <img v-if="item.user.profile == null" src="/img/user.png" :alt="item.user.name">
                                <img v-else :src="item.user.profile" :alt="item.user.name">
                            </div>
                            <div class="allCommentContainerUserName" v-if="showUser == 0 || showUser== 1">
                                {{item.user.name}}
                            </div>
                            <div class="allCommentContainerUserOnline" v-if="checkOnline == 1">
                                <span>{{$t('timeOnline')}} :</span>
                                <span>{{item.user.activity}}</span>
                            </div>
                        </div>
                        <div class="allCommentContainerCreated">
                            <span>{{item.created_at}}</span>
                        </div>
                        <div class="allCommentContainerStatusUnknown" v-if="item.status == 0">
                            <span>{{$t('notSure')}}</span>
                        </div>
                        <div class="allCommentContainerStatusBad" v-if="item.status == 1">
                            <i>
                                <svg-icon :icon="'#likeDown'"></svg-icon>
                            </i>
                            <span>{{$t('notRecommend')}}</span>
                        </div>
                        <div class="allCommentContainerStatusGood" v-if="item.status == 2">
                            <i>
                                <svg-icon :icon="'#likeUp'"></svg-icon>
                            </i>
                            <span>{{$t('iRecommend')}}</span>
                        </div>
                        <div class="allCommentContainerRate" v-if="JSON.parse(item.rate).length">
                            <label>{{$t('userRating')}}</label>
                            <div class="allCommentContainerRateContainer">
                                <div class="allCommentContainerRateContainerItem" v-for="value in JSON.parse(item.rate)">
                                    <span>{{value.name}}</span>
                                    <div class="allSingleHomeDetailBodyItemRate">
                                        <div class="allSingleHomeDetailBodyItemRateValue" :style="{'width' : value.rate * '25' +'%'}"></div>
                                    </div>
                                    <span v-if="value.rate == 0">{{$t('veryBad')}}</span>
                                    <span v-if="value.rate == 1">{{$t('bad')}}</span>
                                    <span v-if="value.rate == 2">{{$t('medium')}}</span>
                                    <span v-if="value.rate == 3">{{$t('good')}}</span>
                                    <span v-if="value.rate == 4">{{$t('awesome')}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="allCommentContainerGetBody">
                        <div class="allCommentTitle">
                            {{item.title}}
                        </div>
                        <div class="allCommentBody">
                            <p>{{item.body}}</p>
                        </div>
                        <div class="allCommentGoodItemsContainer">
                            <label>{{$t('strengths')}}</label>
                            <div class="allCommentGoodItems">
                                <div class="allCommentGoodItem" v-for="value in JSON.parse(item.good)">
                                    <i>
                                        <svg-icon :icon="'#circle'"></svg-icon>
                                    </i>
                                    {{value}}
                                </div>
                            </div>
                        </div>
                        <div class="allCommentBadItemsContainer">
                            <label>{{$t('weakPoints')}}</label>
                            <div class="allCommentBadItems">
                                <div class="allCommentBadItem" v-for="value in JSON.parse(item.bad)">
                                    <i>
                                        <svg-icon :icon="'#circle'"></svg-icon>
                                    </i>
                                    {{value}}
                                </div>
                            </div>
                        </div>
                        <div class="allCommentContainerAnswer" v-if="replyAllow == 1">
                            <div class="allCommentContainerAnswerTitle" v-if="showReply != index" @click.prevent="showReply = index">
                                <i>
                                    <svg-icon :icon="'#reply'"></svg-icon>
                                </i>
                                <span>{{$t('reply')}}</span>
                            </div>
                            <div class="allCommentContainerAnswerBody" v-if="showReply == index">
                                <div class="allCommentContainerAnswerBodyBtn">
                                    <div class="allCommentContainerAnswerBodyBtnItem">
                                        <i>
                                            <svg-icon :icon="'#reply'"></svg-icon>
                                        </i>
                                        <span>{{$t('reply')}}</span>
                                    </div>
                                    <div class="allCommentContainerAnswerBodyBtnItem">
                                        <button @click.prevent="sendReply(item.id)">{{$t('send')}}</button>
                                        <button @click.prevent="showReply = -1">{{$t('cancel')}}</button>
                                    </div>
                                </div>
                                <label>
                                    <textarea :placeholder="$t('reply')" v-model="reply"></textarea>
                                </label>
                            </div>
                        </div>
                        <div class="allCommentContainerReply">
                            <div class="allCommentContainerReplyItem" v-for="child in item.comments">
                                <div class="allCommentContainerReplyItemUser">
                                    <div class="allCommentContainerReplyItemUserPic" v-if="showUser == 0 || showUser== 2">
                                        <img v-if="child.user.profile == null" src="/img/user.png" :alt="child.user.name">
                                        <img v-else :src="child.user.profile" :alt="child.user.name">
                                    </div>
                                    <div class="allCommentContainerReplyItemUserName" v-if="showUser == 0 || showUser== 1">
                                        {{child.user.name}}
                                    </div>
                                    <div class="allCommentContainerReplyItemUserOnline" v-if="checkOnline == 1">
                                        <span>{{$t('timeOnline')}} :</span>
                                        <span>{{child.user.activity}}</span>
                                    </div>
                                </div>
                                <div class="allCommentContainerReplyItemP">
                                    <p>
                                        {{child.body}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="paginate" v-if="comments.links">
                    <paginate-panel :link="comments.links"></paginate-panel>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
import PaginatePanel from "../../Pages/Admin/PaginatePanel";

export default {
    name: "AllComment",
    props: ['rate','posts','errors' , 'replyAllow' , 'showUser' , 'coercion' , 'checkOnline'],
    components:{
        SvgIcon,
        PaginatePanel
    },
    data(){
        return{
            rates : this.rate,
            comments : [],
            i : 0,
            status : 0,
            showLoader : false,
            showStatus : false,
            showReply : -1,
            showGetLoader : false,
            showSendLoader : false,
            good : '',
            title : '',
            UserName : '',
            emailUser : '',
            body : '',
            reply : '',
            goods : [],
            bad : '',
            bads : [],
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
    methods:{
        sendComment(){
            if (this.body != ''){
                if (this.coercion == 1){
                    if (this.emailUser != '' || this.UserName != ''){
                        this.showLoader =true;
                        let rate = JSON.stringify(this.rates);
                        let bads = JSON.stringify(this.bads);
                        let goods = JSON.stringify(this.goods);
                        const url = `/send-comment`;
                        axios.post(url , {
                            rate : rate,
                            bads : bads,
                            goods : goods,
                            status : this.status,
                            title : this.title,
                            post : this.posts,
                            emailUser : this.emailUser,
                            UserName : this.UserName,
                            body : this.body,
                        })
                            .then(response =>{
                                if (response.data == 'noUser'){
                                    this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                                }
                                if (response.data == 'limit'){
                                    this.$toast.error('انجام نشد', 'بیش از حد دیدگاه ارسال کردید', this.notificationSystem.options.error);
                                }
                                if (response.data == 'badWord'){
                                    this.$toast.error('انجام نشد', 'از الفاظ توهین آمیز استفاده نکنید', this.notificationSystem.options.error);
                                }
                                if (response.data == 'success'){
                                    this.$toast.success('دیدگاه ارسال شد', 'دیدگاه بعد تایید نمایش داده میشود', this.notificationSystem.options.success);
                                    this.bads = [];
                                    this.goods = [];
                                    this.title = '';
                                    this.body = '';
                                    this.emailUser = '';
                                    this.UserName = '';
                                    this.good = '';
                                    this.bad = '';
                                    this.getComment();
                                }
                                this.showLoader = false
                            })
                    }
                }else{
                    this.showLoader =true;
                    let rate = JSON.stringify(this.rates);
                    let bads = JSON.stringify(this.bads);
                    let goods = JSON.stringify(this.goods);
                    const url = `/send-comment`;
                    axios.post(url , {
                        rate : rate,
                        bads : bads,
                        goods : goods,
                        status : this.status,
                        title : this.title,
                        post : this.posts,
                        emailUser : null,
                        UserName : null,
                        body : this.body,
                    })
                        .then(response =>{
                            if (response.data == 'noUser'){
                                this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                            }
                            if (response.data == 'limit'){
                                this.$toast.error('انجام نشد', 'بیش از حد دیدگاه ارسال کردید', this.notificationSystem.options.error);
                            }
                            if (response.data == 'badWord'){
                                this.$toast.error('انجام نشد', 'از الفاظ توهین آمیز استفاده نکنید', this.notificationSystem.options.error);
                            }
                            if (response.data == 'success'){
                                this.$toast.success('دیدگاه ارسال شد', 'دیدگاه بعد تایید نمایش داده میشود', this.notificationSystem.options.success);
                                this.bads = [];
                                this.goods = [];
                                this.title = '';
                                this.body = '';
                                this.good = '';
                                this.emailUser = '';
                                this.UserName = '';
                                this.bad = '';
                                this.getComment();
                            }
                            this.showLoader = false
                        })
                }
            }
        },
        sendReply(id){
            if (this.reply != ''){
                this.showReplyLoader =true;
                const url = `/send-reply`;
                axios.post(url , {
                    reply : this.reply,
                    post : this.posts,
                    commentId : id,
                })
                    .then(response =>{
                        if (response.data == 'noUser'){
                            this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        }
                        if (response.data == 'limit'){
                            this.$toast.error('انجام نشد', 'بیش از حد دیدگاه ارسال کردید', this.notificationSystem.options.error);
                        }
                        if (response.data == 'badWord'){
                            this.$toast.error('انجام نشد', 'از الفاظ توهین آمیز استفاده نکنید', this.notificationSystem.options.error);
                        }
                        else{
                            this.$toast.success('دیدگاه ارسال شد', 'دیدگاه بعد تایید نمایش داده میشود', this.notificationSystem.options.success);
                            this.reply = '';
                            this.getComment();
                            this.showReplyLoader = false
                        }
                    })
            }
        },
        getComment(){
            this.showGetLoader =true;
            const url = `/get-comment`;
            axios.post(url ,{
                postID : this.posts.id
            })
                .then(response=>{
                    this.showGetLoader =false;
                    this.comments = response.data;
                })
        },
        removeGood(index){
            this.goods.splice(index , 1);
        },
        addGood(){
            if (this.good != ''){
                this.goods.push(this.good);
                this.good = '';
            }
        },
        removeBad(index){
            this.bads.splice(index , 1);
        },
        addBad(){
            if (this.bad != ''){
                this.bads.push(this.bad);
                this.bad = '';
            }
        },
    },
    mounted() {
        this.getComment();
    }
}
</script>

<style scoped>

</style>
