<template>
    <div class="allHome">
        <svg-content></svg-content>
        <header-home></header-home>
        <div class="contentHome">
            <slot></slot>
        </div>
        <footer-index></footer-index>
    </div>
</template>

<script>
import SvgContent from "../../Pages/Svg/SvgContent";
import HeaderHome from "../../Pages/Home/Header/HeaderHome";
import FooterIndex from "../../Pages/Home/Footer/FooterIndex";

export default {
    name: "HomeLayout",
    components:{
        SvgContent,
        HeaderHome,
        FooterIndex,
    },
}
</script>

<style scoped>

</style>
