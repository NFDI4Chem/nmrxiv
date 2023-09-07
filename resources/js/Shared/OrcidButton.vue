<template>
    <button :type="type">
        <slot></slot>
        <img
            alt="ORCID logo"
            src="https://orcid.org/assets/vectors/orcid.logo.icon.svg"
            width="20"
            height="20"
        />
    </button>
    <span
        class="bg-gray-900 text-center text-white px-2 py-1 shadow-lg rounded-md tooltiptextbottom"
        >Click to find ORCID ID</span
    >
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: "button",
        },
    },
};
</script>
