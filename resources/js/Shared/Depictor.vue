<template>
    <div v-if="!edit">
        <div>
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select
                    id="tabs"
                    name="tabs"
                    v-model="depictionSelection"
                    class="block w-full rounded-md border-gray-300 focus:border-secondary-dark focus:ring-secondary-dark"
                    @change="selectedTab = depictionSelection"
                >
                    <option :value="tab" v-for="tab in tabs" :key="tab">
                        {{ tab }}
                    </option>
                </select>
            </div>
            <div class="hidden sm:block">
                <nav class="flex space-x-2 pl-3" aria-label="Tabs">
                    <div
                        @click="selectedTab = tab"
                        v-for="tab in tabs"
                        :key="tab"
                        :class="[
                            selectedTab == tab ? 'bg-gray-500 text-white' : '',
                            'border hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium',
                        ]"
                    >
                        {{ tab }}
                    </div>
                    <div
                        @click="loadStructureEditor('structureEditor')"
                        v-if="!readonly"
                        class="hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium"
                    >
                        edit
                    </div>
                </nav>
            </div>
        </div>
        <div v-if="selectedTab == '2D'">
            <Depictor2D
                :CIP="CIP"
                :molecule="modelValue"
                :showDownload="showDownload"
                :identifier="identifier"
                :height="height"
                :width="width"
            ></Depictor2D>
        </div>
        <div v-if="selectedTab == '3D'">
            <Depictor3D
                :molecule="modelValue"
                :showDownload="showDownload"
                :identifier="identifier"
                :height="height"
                :width="width"
            ></Depictor3D>
        </div>
    </div>
    <div class="mb-2" v-else>
        <div
            id="structureEditor"
            class="w-full border rounded-md"
            style="height: 400px"
        />
        <br />
        <a
            @click="save"
            class="border hover:cursor-pointer text-gray-500 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium"
            >SAVE</a
        >
    </div>
</template>
<script>
import Depictor2D from "@/Shared/Depictor2D.vue";
import Depictor3D from "@/Shared/Depictor3D.vue";
import OCL from "openchemlib/full";

export default {
    components: {
        Depictor2D,
        Depictor3D,
    },
    props: {
        modelValue: String,
        width: {
            type: Number,
            default: 300,
        },
        height: {
            type: Number,
            default: 300,
        },
        readonly: {
            type: Boolean,
            default: true,
        },
        CIP: {
            type: Boolean,
            default: false,
        },
        showDownload: {
            type: Boolean,
            default: false,
        },
        identifier: String,
    },
    data() {
        return {
            selectedTab: "2D",
            depictionSelection: "2D",
            tabs: ["2D", "3D"],
            edit: false,
            editor: null,
        };
    },

    methods: {
        loadStructureEditor(id) {
            this.edit = true;
            this.$nextTick(() => {
                this.editor = OCL.StructureEditor.createSVGEditor(id, 1);
                if (this.modelValue) {
                    this.editor.setSmiles(this.modelValue);
                }
            });
        },
        save() {
            this.$emit("update:modelValue", this.editor.getSmiles());
            this.edit = false;
        },
    },
};
</script>
