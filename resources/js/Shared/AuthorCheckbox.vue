<template>
    <fieldset class="border rounded border-gray-200 overflow-auto">
        <legend class="sr-only">Notifications</legend>
        <div style="height: 40vh" class="divide-y divide-gray-200">
            <div
                v-for="item in items"
                :key="item.id"
                class="relative flex items-start p-4"
            >
                <div class="min-w-0 flex-1 text-sm">
                    <label for="items" class="font-medium text-gray-700"
                        >{{ item.given_name }} {{ item.family_name }}
                    </label>
                    <p id="items-description" class="text-gray-500">
                        {{ item.affiliation }}
                    </p>
                    <a
                        v-if="item.orcid_id"
                        :href="getOrcidLink(item.orcid_id)"
                        :target="getTarget(item.orcid_id)"
                        class="text-teal-500"
                        >ORCID ID - {{ item.orcid_id }}</a
                    >
                </div>
                <div class="ml-3 flex items-center h-5">
                    <input
                        id="items"
                        v-model="proxyChecked"
                        :value="item"
                        aria-describedby="items-description"
                        name="items"
                        type="checkbox"
                        class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded"
                    />
                </div>
            </div>
        </div>
    </fieldset>
</template>

<script>
export default {
    props: {
        items: [],
        checked: {
            type: [Array],
            default: [],
        },
        value: {
            default: null,
        },
    },
    emits: ["update:checked"],
    computed: {
        proxyChecked: {
            get() {
                return this.checked;
            },

            set(val) {
                this.$emit("update:checked", val);
            },
        },
    },
    methods: {
        getOrcidLink(orcidId) {
            var link = "#";
            if (orcidId) {
                link = "https://orcid.org/" + orcidId;
            }
            return link;
        },
        getTarget(id) {
            var target = null;
            if (id) {
                target = "_blank";
            }
            return target;
        },
    },
};
</script>
