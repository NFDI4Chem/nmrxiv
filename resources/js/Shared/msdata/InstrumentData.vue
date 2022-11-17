<template>
    <div>
<h1 class="text-xl font-bold text-gray-900">Instrument Data</h1>
        <PropertyList :data="data"/>
    </div>
</template>

<script>
import PropertyList from "@/Shared/msdata/PropertyList.vue";

export default {
    name: "InstrumentData",
    components: {PropertyList},
    props: {
        data: {}
    }
}
</script>

