<template>
    <svg class="icon">
        <use :xlink:href="icon"></use>
    </svg>
</template>

<script>
export default {
    name: "SvgIcon",
    props:['icon']
}
</script>

<style scoped>

</style>
