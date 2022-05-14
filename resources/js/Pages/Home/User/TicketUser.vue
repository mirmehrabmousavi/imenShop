<template>
    <home-layout>
        <div class="allUserIndex">
            <div class="allUserLists">
                <user-list tab="5"></user-list>
            </div>
            <div class="allUserIndexInfo">
                <label>{{$t('myTickets')}}</label>
                <div class="allUserIndexInfoPayFilter">
                    <div class="allUserIndexInfoPayFilterItem" @click="btnTicket(0)">
                            <span class="active" v-if="show == 0">{{$t('all')}}</span>
                            <span v-else>{{$t('all')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnTicket(1)">
                        <span class="active" v-if="show == 1">{{$t('answered')}}</span>
                        <span v-else>{{$t('answered')}}</span>
                    </div>
                    <div class="allUserIndexInfoPayFilterItem" @click="btnTicket(2)">
                        <span class="active" v-if="show == 2">{{$t('awaitingReview')}}</span>
                        <span v-else>{{$t('awaitingReview')}}</span>
                    </div>
                </div>
                <div class="paginate" v-if="tickets.links">
                    <paginate-panel :link="tickets.links"></paginate-panel>
                </div>
                <div class="allUserIndexInfoPay">
                    <table>
                        <tr>
                            <th>#</th>
                            <th>{{$t('ticket')}}</th>
                            <th>{{$t('reply')}}</th>
                            <th>{{$t('dateRegistration')}}</th>
                            <th>{{$t('theOperation')}}</th>
                        </tr>
                        <div class="showTr">
                            <tr v-for="(item , index) in tickets.data">
                                <td>
                                    <span v-if="$i18n.locale == 'fa'">{{++index}}</span>
                                    <span class="numberPayEn" v-if="$i18n.locale == 'en'">{{++index}}</span>
                                </td>
                                <td>
                                    <span>{{item.body}}</span>
                                </td>
                                <td>
                                    <span>{{item.answer}}</span>
                                </td>
                                <td>
                                    <span>{{item.created_at}}</span>
                                </td>
                                <td>
                                    <i @click="remove(item.id)">
                                        <svg-icon :icon="'#trash'"></svg-icon>
                                    </i>
                                </td>
                            </tr>
                        </div>
                    </table>
                </div>
                <div class="paginate" v-if="tickets.links">
                    <paginate-panel :link="tickets.links"></paginate-panel>
                </div>
            </div>
        </div>
    </home-layout>
</template>

<script>
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../../Admin/PaginatePanel";
import UserList from "./UserList";
export default {
    name: "PayUser",
    components:{
        UserList,
        HomeLayout,
        SvgIcon,
        PaginatePanel
    },
    props: ['tickets','title'],
    metaInfo() {
        return {
            title: `درخواست ها - ${this.title}`,
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
    data() {
        return {
            showLoader : false,
            show : 0,
        }
    },
    methods:{
        btnTicket(index){
            const url = `/profile/ticket`;
            this.showLoader = true;
            this.show = index;
            this.$inertia.post(url , {
                show : this.show,
            })
                .then(response=>{
                    this.showLoader = false;
                })
        },
        remove(id){
            this.$swal.fire({
                title: 'آیا مطمینی ؟',
                text: "فایل وارد سطل اشغال میشود و سپس حذف میشود!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله حذف شود',
                cancelButtonText: 'پشیمون شدم'
            }).then((result) => {
                if (result.value) {
                    const url = `/profile/ticket`;
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
    }
}
</script>

