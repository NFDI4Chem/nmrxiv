<template>
  <fieldset class="border border-gray-200 overflow-auto">
    <legend class="sr-only">Notifications</legend>
    <div style="height: 40vh" class="divide-y divide-gray-200 ">
    <div v-for="item in items" :key="item.id" class="relative flex items-start p-4 ">
        <div class="min-w-0 flex-1 text-sm">
          <label for="items" class="font-medium text-gray-700">{{ item.given_name }} {{ item.family_name}} </label>
          <p id="items-description" class="text-gray-500">{{ item.affiliation }}</p>
          <a :href="item.orcid_id" v-if="item.orcid_id" class="text-teal-500">ORCID ID - {{item.orcid_id}}</a>
        </div>
        <div class="ml-3 flex items-center h-5">
          <input :value="item" v-model="proxyChecked" id="items" aria-describedby="items-description" name="items" type="checkbox" class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded" />
        </div>
    </div>
    </div>
  </fieldset>
</template>

<script>
export default {
 emits: ['update:checked'],

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
}

</script>