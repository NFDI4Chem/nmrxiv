<template>
    <SelectDataset v-model:selected-option="selectedDs" :all-options="selectedStudy" label-text="Select Experiment" name="selectExp" />
    <SelectDataset v-model:selected-option="selectedDataType" :all-options="dataTypes" label-text="Select Data/Metadata" name="selectData"/>
    {{msdata}}
</template>
<script>
import SelectDataset from "@/Shared/msdata/SelectDataset.vue";

let fileids = {}
export default {
  name: 'SubmissionMsDataTab',
    components: {SelectDataset},
    props: {
    selectedDataset: {},
    selectedStudy: {}
  },
    data() {
      return {
          dataTypes: [{name: "Instrumentdata",id: 1},{name: "Sampledata",id:1}],
          selectedDataType: {},
          selectedDs: this.selectDataset,
          msdata: ""
      }
    },
    watch: {
        selectedDs: {
            handler() {
                this.getInstrumentdata()
            }
    }},
    methods: {
      async getInstrumentdata() {
          let fileid = this.selectedDs.fs_id;
          if (!(fileid in fileids)) {
              await this.getMsdataId(fileid);
          }
          let msdataId = fileids[fileid]['id'];
          let url = fileids[fileid].msdata_base_url;
          axios.get(url + msdataId + "/" + "instrument", {
              headers: {
                  "Access-Control-Allow-Origin": "*"
              }
          }).then(
              resp => {
                  console.log(resp.data);
              }
          )
      },
      async getMsdataId(fileid) {
          let fileId = this.selectedDs.fs_id;
          let resp = await axios.get("/dashboard/storage/msdata-id/" + fileId)
          fileids[fileId] = resp.data;

      }
    }
}
</script>
