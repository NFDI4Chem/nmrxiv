<template>
    <div v-if="visibleStatuses.length > 0" class="flex flex-wrap gap-2 ml-4">
        <span
            v-for="status in visibleStatuses"
            :key="status.type"
            :class="[
                'inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium capitalize',
                getStatusClasses(status.type),
            ]"
        >
            {{ status.label }}
        </span>
    </div>
</template>

<script>
export default {
    name: "ProjectStatusBadge",

    props: {
        /**
         * Project object containing status information
         */
        project: {
            type: Object,
            required: true,
            validator: (project) => {
                return project && typeof project === "object";
            },
        },
    },

    computed: {
        /**
         * Get visible statuses based on project.status
         */
        visibleStatuses() {
            if (!this.project.status) {
                return [];
            }

            return [
                {
                    type: this.project.status,
                    label: this.project.status,
                },
            ];
        },
    },

    methods: {
        /**
         * Get Tailwind CSS classes for different status types
         * Following consistent color patterns based on status values
         */
        getStatusClasses(statusType) {
            const colorMap = {
                // Critical states - Red theme
                deleted:
                    "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",

                // Warning states - Yellow theme
                embargo:
                    "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",

                draft: "bg-yellow-100 text-yellow-800 dark:bg-blue-900 dark:text-yellow-200",

                // Neutral states - Gray theme
                archived:
                    "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200",

                // Success states - Green theme
                published:
                    "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
                complete:
                    "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",

                // Processing states - Blue theme
                processing:
                    "bg-blue-100 text-blue-800 dark:bg-green-900 dark:text-blue-200",
            };

            return (
                colorMap[statusType] ||
                "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
            );
        },

        /**
         * Get readable status description for accessibility
         */
        getStatusDescription(statusType) {
            return `Project status: ${statusType}`;
        },
    },
};
</script>

<style scoped>
/* Add any custom styles if needed */
</style>
