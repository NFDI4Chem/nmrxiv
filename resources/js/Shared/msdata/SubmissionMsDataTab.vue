<template>
    <SelectDataset v-model:selected-option="selectedDs" :all-options="selectedStudy" label-text="Select Experiment" name="selectExp" />
    <SelectDataset v-model:selected-option="selectedDataType" :all-options="dataTypes" label-text="Select Data/Metadata" name="selectData"/>
    <InstrumentData  v-if="selectedDataType.id === 1" :data="msdata"></InstrumentData>
    <RunData v-if="selectedDataType.id === 2" :data="msdata"></RunData>
    <ScanData v-if="selectedDataType.id === 3" :data="msdata" :fileid="selectedDs.fs_id" ></ScanData>
    <Chromatogramdata v-if="selectedDataType.id === 4" :data="msdata"></Chromatogramdata>
</template>
<script>
import SelectDataset from "@/Shared/msdata/SelectDataset.vue";
import InstrumentData from "@/Shared/msdata/InstrumentData.vue";
import RunData from "@/Shared/msdata/RunData.vue";
import ScanData from "@/Shared/msdata/ScanData.vue";
import Chromatogramdata from "@/Shared/msdata/Chromatogramdata.vue";

let fileids = {}
export default {
  name: 'SubmissionMsDataTab',
    components: {Chromatogramdata, ScanData, RunData, InstrumentData, SelectDataset},
    props: {
    selectedDataset: {},
    selectedStudy: {}
  },
    data() {
      return {
          dataTypes: [{name: "Instrumentdata",id: 1},{name: "Rundata",id:2},{name: "Scans",id:3},{name: "Chromatograms",id:4}],
          selectedDataType: {},
          selectedDs: this.selectedDataset,
          msdata: ""
      }
    },
    watch: {
        selectedDs: {
            handler() {
                this.updateData()
            }
        },
        selectedDataType: {
              handler() {
                  console.log("datatype")
                  this.updateData()
              },
            deep: true
        }
    },
    methods: {
      updateData() {
        switch(this.selectedDataType.id) {
            case 1:
                this.getInstrumentdata();
                break;
            case 2:
                this.getRunData()
                break;
            case 3:
                this.getScanData()
                break;
            case 4:
                this.getChromatogramData()
                break;
        }
      },
        async getScanData() {
            this.getMsData("scans");
        },
        async getChromatogramData() {
            this.getMsData("chromatograms");
        },
      async getInstrumentdata() {
          this.getMsData("instrument");
      },
        async getRunData() {
            this.getMsData("run");
        },
        async getMsData(datatype) {
            let fileid = this.selectedDs.fs_id;
            if (!(fileid in fileids)) {
                await this.getMsdataId(fileid);
            }
            let msdataId = fileids[fileid]['id'];
            let url = fileids[fileid].msdata_base_url;
            axios.get(url + msdataId + "/" + datatype, {
                headers: {
                }
            }).then(
                resp => {
                    console.log(resp.data);
                    this.msdata = resp.data;
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
