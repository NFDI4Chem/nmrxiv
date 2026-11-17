<template>
    <div ref="host" class="nmrium-host"></div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, toRaw, watch } from 'vue';
import { createRoot } from 'react-dom/client';
import { createElement } from 'react';
import { NMRium } from 'nmrium';
import '@blueprintjs/core/lib/css/blueprint.css';
import '@blueprintjs/icons/lib/css/blueprint-icons.css';
import '@blueprintjs/select/lib/css/blueprint-select.css';

const props = defineProps({
    prepared: { type: Object, default: null },
});

const emit = defineEmits(['error']);

const host = ref(null);
let root = null;

function renderNmrium(prepared) {
    if (!root) {
        return;
    }

    // Unwrap any Vue Proxy — private fields on FileCollection / core break
    // when accessed through a reactive proxy.
    const raw = prepared ? toRaw(prepared) : null;

    if (!raw?.state || !raw?.aggregator) {
        root.render(
            createElement(
                'div',
                {
                    style: {
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        height: '100%',
                        color: '#6b7280',
                        fontSize: '14px',
                    },
                },
                'No spectra to display',
            ),
        );

        return;
    }

    try {
        root.render(
            createElement(NMRium, {
                state: toRaw(raw.state),
                aggregator: toRaw(raw.aggregator),
                core: toRaw(raw.core),
                emptyText: 'Loading spectra from BagIt archive…',
                workspace: 'default',
            }),
        );
    } catch (error) {
        emit('error', error);
    }
}

onMounted(() => {
    if (!host.value) {
        return;
    }

    root = createRoot(host.value);
    renderNmrium(props.prepared);
});

watch(
    () => props.prepared,
    (prepared) => {
        renderNmrium(prepared);
    },
);

onBeforeUnmount(() => {
    if (root) {
        root.unmount();
        root = null;
    }
});
</script>
