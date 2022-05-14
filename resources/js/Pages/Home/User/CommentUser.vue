<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="2"></user-list>
            </div>
            <div class="allUserIndexComment">
                <label>{{$t('myComments')}}</label>
                <div class="allUserIndexInfoPayFilter">
                    <div class="allUserIndexInfoPayFilterItem" @click="btnComment(0)">
                            <span class="active" v-if="show == 0">{{$t('all')}}</span>
                            <span v-else>{{$t('all')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnComment(1)">
                        <span class="active" v-if="show == 1">{{$t('awaitingReview')}}</span>
                        <span v-else>{{$t('awaitingReview')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnComment(2)">
                        <span class="active" v-if="show == 2">{{$t('accepted')}}</span>
                        <span v-else>{{$t('accepted')}}</span>
                    </div>
                </div>
                <div class="paginate" v-if="allComment.links">
                    <paginate-panel :link="allComment.links"></paginate-panel>
                </div>
                <div class="allUserIndexCommentItems">
                    <div class="allUserIndexCommentItem" v-for="(item , index) in allComment.data">
                        <inertia-link :href="'/product/' + item.post.slug">
                            <div class="allUserIndexCommentItemPic">
                                <img v-lazy="JSON.parse(item.post.image)[0]" :alt="item.post.title">
                            </div>
                            <div class="allUserIndexCommentItemSubject">
                                <div class="allUserIndexCommentItemSubjectTitle">
                                    {{item.title}}
                                </div>
                                <div class="allUserIndexCommentItemSubjectStatus">
                                    <span v-if="item.status == 0">{{$t('notSure')}}</span>
                                    <span v-if="item.status == 1">{{$t('notRecommend')}}</span>
                                    <span v-if="item.status == 2">{{$t('iRecommend')}}</span>
                                </div>
                                <div class="allUserIndexCommentItemSubjectBody">
                                    <p>{{item.body}}</p>
                                </div>
                                <div class="allUserIndexCommentItemSubjectAccept">
                                    <span v-if="item.approved == 0">{{$t('awaitingReview')}}</span>
                                    <span v-if="item.approved == 1" class="active">{{$t('accepted')}}</span>
                                </div>
                            </div>
                        </inertia-link>
                        <div class="allUserIndexCommentItemDelete">
                            <i @click.stop="remove(item.id , index)">
                                <svg-icon :icon="'#trash'"></svg-icon>
                            </i>
                        </div>
                    </div>
                </div>
                <div class="paginate" v-if="allComment.links">
                    <paginate-panel :link="allComment.links"></paginate-panel>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import PaginatePanel from "../../Admin/PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import UserList from "./UserList";
export default {
    name: "CommentUser",
    props: ['comments','title'],
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    metaInfo() {
        return {
            title: `دیدگاه ها - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                amp: true,
                reptilian: 'gator'
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    data() {
        return {
            showLoader : false,
            show : 0,
            allComment : this.comments,
        }
    },
    methods:{
        btnComment(index){
            const url = `/profile/comment`;
            this.showLoader = true;
            this.show = index;
            this.$inertia.post(url , {
                show : this.show,
            })
                .then(response=>{
                    this.showLoader = false;
                })
        },
        remove(id , index){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل حذف شده برنمیگردد!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    const url = `/profile/comment`;
                    this.showLoader = true;
                    this.$inertia.post(url , {
                        removeId : id,
                        show : this.show,
                    })
                        .then(response=>{
                            this.showLoader = false;
                        })
                }
            })
        },
    },
    watch: {
        'comments': function(val, oldVal){
            this.allComment = this.comments;
        }
    }
}
</script>
