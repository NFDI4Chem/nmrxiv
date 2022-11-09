<template>
  <div class="p-6">
    <div>
      <label
          for="location"
          class="block text-sm font-medium text-gray-700"
      >Select
        Experiment
      </label>
        <SelectDataset :selected-dataset="selectedDataset" :selected-study="selectedStudy"/>
    </div>
  </div>
</template>
<script>
import SelectDataset from "@/Shared/msdata/SelectDataset.vue";

export default {
  name: 'SubmissionMsDataTab',
    components: {SelectDataset},
    props: {
    selectedDataset: {},
    selectedStudy: {}
  }
}
</script>
