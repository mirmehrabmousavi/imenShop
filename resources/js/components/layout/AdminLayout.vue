<template>
    <fullscreen ref="fullscreen" @change="fullscreenChange">
        <div class="allPanel">
            <svg-content></svg-content>
            <header-panel></header-panel>
            <div class="content">
                <slot></slot>
            </div>
            <side-bar-panel></side-bar-panel>
        </div>
    </fullscreen>
</template>

<script>
import HeaderPanel from "../../Pages/Admin/Header/HeaderPanel";
import SideBarPanel from "../../Pages/Admin/SideBar/SideBarPanel";
import SvgContent from "../../Pages/Svg/SvgContent";
export default {
    name: "AdminLayout",
    components:{
        HeaderPanel,
        SideBarPanel,
        SvgContent
    },
    data() {
        return {
            fullscreen: false
        }
    },
    methods:{
        toggle () {
            this.$refs['fullscreen'].toggle();
        },
        fullscreenChange (fullscreen) {
            this.fullscreen = fullscreen
        },
    },
    created: function() {
        this.$eventHub.on('fullS', this.toggle);
    },
}
</script>

<style scoped>

</style>
