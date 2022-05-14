<template>
    <admin-layout>
        <div class="allUserRole">
            <div class="allUserPanelTop">
                <h1>کارمندان</h1>
                <div class="allUserTitle">
                    <inertia-link href="/admin">داشبورد</inertia-link>
                    <span>/</span>
                    <inertia-link href="/admin/user/role">کارمندان</inertia-link>
                </div>
            </div>
            <div class="allUserOptions">
                <div class="allUserOptionsButtons">
                    <button v-if="value.length" @click="btnRemoveArray('')">حذف</button>
                </div>
                <div class="filterItems">
                    <div class="imageContentOptionsFilterButton" @click.stop="showFilter = !showFilter">
                        <svg-icon :icon="'#filter'"></svg-icon>
                        فیلتر اطلاعات
                    </div>
                    <transition name="bounce">
                        <div class="filterContent" v-if="showFilter">
                            <div class="filterContentItem">
                                <label>فیلتر عنوان یا آیدی</label>
                                <input type="text" v-model="search"  placeholder="عنوان یا آیدی را وارد کنید" @keypress.enter="btnSearch(0)">
                            </div>
                            <div class="filterContentItem">
                                <label>فیلتر تاریخ</label>
                                <div class="allTicketPanelTitleDate">
                                    <date-picker
                                        v-model="date"
                                        type="datetime"
                                        format="YYYY-MM-DD"
                                        display-format="jYYYY-jMM-jDD"
                                        @close="btnSearch(0)"
                                        placeholder="تاریخ را وارد کنید"
                                        :timezone="true"
                                    />
                                    <i @click.stop="btnSearch(1)" v-if="date">
                                        <svg-icon :icon="'#cancel'"></svg-icon>
                                    </i>
                                </div>
                            </div>
                            <div class="filterContentItem">
                                <label>فیلتر مقام</label>
                                <div class="allCategoryPanel" @click="showAllRole = !showAllRole">
                                    <div class="categoryShow">
                                        <h4 v-if="sortRole == ''">همه</h4>
                                        <h4 v-else>{{sortRole}}</h4>
                                        <i>
                                            <svg-icon :icon="'#down'"></svg-icon>
                                        </i>
                                    </div>
                                    <ul v-if="showAllRole">
                                        <VuePerfectScrollbar class="scroll-area" v-once :settings="settings">
                                            <li @click="sendSortRole('')">همه</li>
                                            <li v-for="item in roles" @click="sendSortRole(item.name)">{{item.name}}</li>
                                        </VuePerfectScrollbar>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div class="pages">
                <paginate-panel :link="allUser.links"></paginate-panel>
            </div>
            <transition name="slide-fade">
                <div class="showTable">
                    <div class="postItems" v-for="item in allUser.data" @click="getCheck(item.id)">
                        <div class="postItem">
                            <div class="check">
                                <i v-for="(values , index2) in value" v-if="values == item.id" :key="index2">
                                    <svg-icon :icon="'#check'"></svg-icon>
                                </i>
                                <i>
                                    <svg-icon :icon="'#uncheck'"></svg-icon>
                                </i>
                            </div>
                            <div class="postItemPic">
                                <img v-if="item.profile == null" src="/img/user.png">
                                <img v-else :src="item.profile">
                            </div>
                            <h3>{{item.name}}</h3>
                            <h4>{{item.roles[0].name}}</h4>
                            <div class="postItemOptions">
                                <a :href="'sms:'+item.number">
                                    <svg-icon :icon="'#comment2'"></svg-icon>
                                </a>
                                <a :href="'tel:'+item.number">
                                    <svg-icon :icon="'#phone'"></svg-icon>
                                </a>
                                <i @click.prevent="btnRemoveArray(item.id)">
                                    <svg-icon :icon="'#trash2'"></svg-icon>
                                </i>
                                <a :href="'mailto:'+item.email">
                                    <svg-icon :icon="'#mail3'"></svg-icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
            <div class="pages">
                <paginate-panel :link="allUser.links"></paginate-panel>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import PaginatePanel from "../PaginatePanel";
import SvgIcon from "../../Svg/SvgIcon";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import ShowImage from "../ShowImage";
import AllTable from "../Table/AllTable";
export default {
    name: "UserRole",
    props:['allUser','errors','labels','deletes','edits','shows','roles','permissions','edits','deletes'],
    components:{
        SvgIcon,
        VuePerfectScrollbar,
        ShowImage,
        AdminLayout,
        PaginatePanel,
        AllTable
    },
    metaInfo: {
        title: 'کارمندان'
    },
    data(){
        return{
            settings: {
                maxScrollbarLength: 60
            },
            showFilter: false,
            showImages: false,
            showAllRole: false,
            show: false,
            date:'',
            search: '',
            value: [],
            sortRole: '',
            showRole: false,
            showPermission: false,
            i: 0,
        }
    },
    methods:{
        sidebar(){
            this.$eventHub.emit('sidebar' , '3');
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
        },
        btnSearch(number){
            if(number == 1){
                this.date = '';
            }
            const url = `/admin/user/role`;
            this.$inertia.post(url , {
                date : this.date,
                sortRole : this.sortRole,
                search : this.search
            })
        },
        sendSortRole(item){
            this.sortRole = item;
            const url = `/admin/user/role`;
            this.$inertia.post(url , {
                search : this.search,
                sortRole : this.sortRole,
                date : this.date,
            })
        },
        btnRemoveArray(id){
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
                    if(id){
                        this.value = [id];
                    }
                    const url = `/admin/user/role`;
                    this.$inertia.post(url ,{'value' : this.value})
                }
            })
        },
    },
    mounted(){
        this.sidebar();
    }

}
</script>

<style scoped>

</style>
