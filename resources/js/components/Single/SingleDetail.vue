<template>
    <div class="allSingleHomeDetail">
        <div class="allSingleHomeDetailTitle">
            <ul>
                <li @click="tab = 0" v-if="posts.body != null && posts.review[0].body != null">
                            <span class="active" v-if="tab == 0">
                                <i>
                                    <svg-icon :icon="'#review'"></svg-icon>
                                </i>
                                {{$t('review')}}
                            </span>
                    <span class="unActive" v-else>
                                <i>
                                    <svg-icon :icon="'#review'"></svg-icon>
                                </i>
                                {{$t('review')}}
                            </span>
                </li>
                <li @click="tab = 1" v-if="JSON.parse(this.posts.review[0].specifications).length">
                            <span class="active" v-if="tab == 1">
                                        <i>
                                            <svg-icon :icon="'#property'"></svg-icon>
                                        </i>
                                {{$t('specifications')}}
                            </span>
                    <span class="unActive" v-else>
                                    <i>
                                        <svg-icon :icon="'#property'"></svg-icon>
                                    </i>
                            {{$t('specifications')}}
                        </span>
                </li>
                <li @click="tab = 2">
                        <span class="active" v-if="tab == 2">
                                    <i>
                                        <svg-icon :icon="'#comment'"></svg-icon>
                                    </i>
                                  {{$t('userComments')}}
                        </span>
                    <span class="unActive" v-else>
                                    <i>
                                        <svg-icon :icon="'#comment'"></svg-icon>
                                    </i>
                                    {{$t('userComments')}}
                            </span>
                </li>
                <li @click="tab = 3">
                            <span class="active" v-if="tab == 3">
                                <i>
                                    <svg-icon :icon="'#question'"></svg-icon>
                                </i>
                              {{$t('qa')}}
                            </span>
                    <span class="unActive" v-else>
                                <i>
                                    <svg-icon :icon="'#question'"></svg-icon>
                                </i>
                                {{$t('qa')}}
                            </span>
                </li>
            </ul>
        </div>
        <div class="allSingleHomeDetailBody" v-if="tab == 0 && posts.body && this.posts.review[0].body != null">
            <div class="allSingleHomeDetailBodyItem" v-if="posts.body != null">
                <label>{{$t('overview')}}</label>
                <p v-html="posts.body" v-if="$i18n.locale == 'fa'"></p>
                <p v-html="posts.bodyEn" v-if="$i18n.locale == 'en'"></p>
            </div>
            <div class="allSingleHomeDetailBodyItem" v-if="this.posts.review[0].body != null">
                <label>{{$t('expertReview')}}</label>
                <p v-html="this.posts.review[0].body" v-if="$i18n.locale == 'fa'"></p>
                <p v-html="this.posts.review[0].bodyEn" v-if="$i18n.locale == 'en'"></p>
            </div>
        </div>
        <div class="allSingleHomeDetailProperties" v-if="tab == 1">
            <label>{{$t('technicalSpecifications')}}</label>
            <ul>
                <li v-for="(item , check) in JSON.parse(this.posts.review[0].specifications)" :key="check">
                    <h3>
                        <span v-if="$i18n.locale == 'fa'">{{item.title}}</span>
                        <span class="en" v-if="$i18n.locale == 'en'">{{item.titleEn}}</span>
                    </h3>
                    <p>
                        <span v-if="$i18n.locale == 'fa'">{{item.body}}</span>
                        <span class="en" v-if="$i18n.locale == 'en'">{{item.bodyEn}}</span>
                    </p>
                </li>
            </ul>
        </div>
        <div class="allSingleHomeDetailComment" v-if="tab == 2 && show">
            <all-comment :posts="posts" :rate="JSON.parse(posts.review[0].rate)" :replyAllow="reply" :showUser="showUser" :coercion="coercion" :checkOnline="checkOnline"></all-comment>
        </div>
        <div class="allSingleHomeDetailCommentUser" v-if="tab == 2 && show == false">
            <h4>{{$t('firstRegisterComments')}}</h4>
            <a href="/login">{{$t('clickJoin')}}</a>
        </div>
        <div class="allSingleQuestion" v-if="tab == 3">
            <div class="allSingleQuestionSends">
                <div class="allSingleQuestionSend">
                    <i>
                        <svg-icon :icon="'#question'"></svg-icon>
                    </i>
                    <p>{{$t('sendQuestionDescription')}}</p>
                    <h3 @click="btnShowQuestion(0)">{{$t('sendQuestion')}}</h3>
                </div>
            </div>
            <div class="allSingleQuestionBody">
                <div class="allSingleQuestionItems" v-if="posts.question.length">
                    <div class="allSingleQuestionItem" v-for="item in posts.question">
                        <div class="allSingleQuestionTitle">
                            <i>
                                <svg-icon :icon="'#question'"></svg-icon>
                            </i>
                            <p>{{ item.body}}</p>
                        </div>
                        <div class="allSingleQuestionAnswers">
                            <div class="allSingleQuestionAnswer" v-for="answer in item.questions">
                                <h4>{{$t('reply')}} :</h4>
                                <p>{{ answer.body }}</p>
                            </div>
                        </div>
                        <div class="allSingleQuestionItemSend" @click="btnShowQuestion(item.id)">
                            <span>{{$t('sendResponse')}}</span>
                            <i>
                                <svg-icon :icon="'#left'"></svg-icon>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="allSingleQuestionEmpty" v-else>
                    <i>
                        <svg-icon :icon="'#question'"></svg-icon>
                    </i>
                    <p>{{$t('emptyQuestion')}}</p>
                </div>
            </div>
        </div>
        <div class="showSingleItem" v-if="showQuestion">
            <all-question v-on:sendClose="getClose" :parentId="parentId" :post="posts.id"></all-question>
        </div>
    </div>
</template>

<script>
import AllComment from "./AllComment";
import AllQuestion from "./AllQuestion";
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
    name: "SingleDetail",
    components: {SvgIcon, AllQuestion, AllComment},
    props:['posts' , 'showUser' , 'coercion' , 'reply' , 'show' , 'checkOnline'],
    data(){
        return{
            tab : 0,
            parentId:0,
            showQuestion: false,
            hooperSettings3: {
                wheelControl:false,
                centerMode: false,
                transition: 700,
                breakpoints: {
                    100: {
                        itemsToShow: 2,
                        itemsToSlide: 1,
                    },
                    700: {
                        itemsToShow: 3,
                        itemsToSlide: 1,
                    },
                    1000: {
                        itemsToShow: 4,
                        itemsToSlide: 1,
                    },
                    1200: {
                        itemsToShow: 5,
                        itemsToSlide: 1,
                    },
                    1600: {
                        itemsToShow: 6,
                        itemsToSlide: 1,
                    },
                    1800: {
                        itemsToShow: 7,
                        itemsToSlide: 1,
                    },
                }
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
        }
    },
    methods:{
        btnShowQuestion(id){
            this.parentId = id;
            this.showQuestion = true;
        },
        getClose(item){
            this.showQuestion = false;
        },
        getData(){
            if(this.posts.body == null && this.posts.review[0].body == null){
                this.tab = 1;
            }
        },
    },
    mounted() {
        this.getData();
    },
}
</script>

<style scoped>

</style>
