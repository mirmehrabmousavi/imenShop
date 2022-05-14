<template>
    <div class="allPaginatePanel" v-if="link.length != 3">
        <div class="pageItem" v-for="item in link">
            <div class="pageItemLabel" v-if="item.label == 'قبلی' || item.label == 'بعدی'">
                <inertia-link :href="item.url + '&' + urls" v-if="item.label == 'قبلی'"><svg-icon :icon="'#back'"></svg-icon></inertia-link>
                <inertia-link :href="item.url + '&' + urls" v-if="item.label == 'بعدی'"><svg-icon :icon="'#forward'"></svg-icon></inertia-link>
            </div>
            <div class="pageItemLabel" v-else>
                <inertia-link :href="item.url + '&' + urls" v-if="item.active == true" class="active">{{item.label}}</inertia-link>
                <inertia-link :href="item.url + '&' + urls" v-else>{{item.label}}</inertia-link>
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../Svg/SvgIcon";
export default {
    name: "PaginatePanel",
    components: {SvgIcon},
    props : ['link','urls']
}
</script>

<style scoped>

</style>
