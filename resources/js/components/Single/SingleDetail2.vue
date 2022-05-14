<template>
    <div class="allSingleDetail2">
        <div class="tabs">
            <div @click="tab = 0" class="tab">
                <div class="tabItem active" v-if="tab == 0">
                    <div>
                        <i>
                            <svg-icon :icon="'#review'"></svg-icon>
                        </i>
                        {{$t('review')}}
                    </div>
                </div>
                <div class="tabItem" v-else>
                    <div>
                        <i>
                            <svg-icon :icon="'#review'"></svg-icon>
                        </i>
                        {{$t('review')}}
                    </div>
                </div>
            </div>
            <div @click="tab = 1" class="tab">
                <div class="tabItem active" v-if="tab == 1">
                    <div>
                        <i>
                            <svg-icon :icon="'#property'"></svg-icon>
                        </i>
                        {{$t('specifications')}}
                    </div>
                </div>
                <div class="tabItem" v-else>
                    <div>
                        <i>
                            <svg-icon :icon="'#property'"></svg-icon>
                        </i>
                        {{$t('specifications')}}
                    </div>
                </div>
            </div>
            <div @click="tab = 2" class="tab">
                <div class="tabItem active" v-if="tab == 2">
                    <div>
                        <i>
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </i>
                        {{$t('userComments')}}
                    </div>
                </div>
                <div class="tabItem" v-else>
                    <div>
                        <i>
                            <svg-icon :icon="'#comment'"></svg-icon>
                        </i>
                        {{$t('userComments')}}
                    </div>
                </div>
            </div>
            <div @click="tab = 3" class="tab">
                <div class="tabItem active" v-if="tab == 3">
                    <div>
                        <i>
                            <svg-icon :icon="'#question'"></svg-icon>
                        </i>
                        {{$t('qa')}}
                    </div>
                </div>
                <div class="tabItem" v-else>
                    <div>
                        <i>
                            <svg-icon :icon="'#question'"></svg-icon>
                        </i>
                        {{$t('qa')}}
                    </div>
                </div>
            </div>
        </div>
        <div class="body" v-if="tab == 0 && posts.body && this.posts.review[0].body != null">
            <div class="bodyItem" v-if="posts.body != null">
                <label>
                    <i>
                        <svg-icon :icon="'#review'"></svg-icon>
                    </i>
                    {{$t('overview')}}
                </label>
                <p v-html="posts.body" v-if="$i18n.locale == 'fa'"></p>
                <p v-html="posts.bodyEn" v-if="$i18n.locale == 'en'"></p>
            </div>
            <div class="bodyItem" v-if="this.posts.review[0].body != null">
                <label>
                    <i>
                        <svg-icon :icon="'#review'"></svg-icon>
                    </i>
                    {{$t('expertReview')}}
                </label>
                <p v-html="this.posts.review[0].body" v-if="$i18n.locale == 'fa'"></p>
                <p v-html="this.posts.review[0].bodyEn" v-if="$i18n.locale == 'en'"></p>
            </div>
        </div>
        <div class="property" v-if="tab == 1">
            <label>
                <i>
                    <svg-icon :icon="'#property'"></svg-icon>
                </i>
                {{$t('technicalSpecifications')}}
            </label>
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
        <div class="comment" v-if="tab == 2 && show">
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
    name: "SingleDetail2",
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
