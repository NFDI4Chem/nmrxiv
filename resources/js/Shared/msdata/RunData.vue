<template>
<div>
    <h1>Run Data</h1>
    <PropertyList :data="data"/>
</div>
</template>

<script>
import PropertyList from "@/Shared/msdata/PropertyList.vue";


export default {
    name: "RunData",
    components: {PropertyList},
    props: {
        data: {}
    }
}
</script>

<style scoped>

</style>
