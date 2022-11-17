<template>
    <div>
        <h1>Scan Data</h1>
        <SelectDataset v-if="data && data.scans" :all-options="scandata" label-text="Select Scan" name="selectScan"  v-model:selected-option="selectedScan"></SelectDataset>
        <div id="script">
        </div>

    </div>
</template>
<script>

let fileids = {}

import SelectDataset from "@/Shared/msdata/SelectDataset.vue";
export default {
    name: "ScanData",
    components: {SelectDataset},
    props: {
        data: {},
        fileid: "",
        fileids: {}
    },
    data() {
        return {
            scandata: {},
            selectedScan: {},
            scanimage: ""
        }
    },
    watch: {
        data: function(data) {
            this.scandata =  (typeof data === 'undefined' || !data.scans) ? {} :
            data.scans.map(scan => {return {id: scan, name: scan}}) ;
        },
        selectedScan: {
            handler() {
                this.getScan()
            }
        },
        scanimage: {
            handler() {
                const e = document.getElementById('script');
                this.setInner(e,this.scanimage)
            }
        }
    },
    methods: {
        async getScan() {
            let fileid = this.fileid;
            if (!(fileid in fileids)) {
                await this.getMsdataId(fileid);
            }
            let msdataId = fileids[fileid]['id'];
            let url = fileids[fileid].msdata_base_url;
            axios.get(url + msdataId + "/scan/" + this.selectedScan.id+"/image", {
                headers: {
                }
            }).then(
                resp => {
                    console.log(resp.data);
                    this.scanimage = resp.data;
                }
            )
        },
        async getMsdataId() {
            let fileId = this.fileid;
            let resp = await axios.get("/dashboard/storage/msdata-id/" + fileId)
            fileids[fileId] = resp.data;

        },
        htmlDecode(input) {
            if (input === 'undefined' || input ==="") return "";
            console.log("input " ,input)
            const e = document.getElementById('script');
            e.innerHTML = input;
            this.setInner(e,input)
            return e.childNodes[3].innerText
        },
        setInner(elm, html) {
            elm.innerHTML = html;
            Array.from(elm.querySelectorAll("script")).forEach(oldScript => {
                const newScript = document.createElement("script");
                Array.from(oldScript.attributes)
                    .forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        }

    },
    computed: {
        strWithScriptTag() {
            const scriptStr = this.scanimage;
            return this.htmlDecode(scriptStr)
        }
    },
}
</script>

<style scoped>

</style>
