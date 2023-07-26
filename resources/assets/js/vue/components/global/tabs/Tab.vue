<template>
    <div class="tab-body" :style="{ display }" ref="body">
        <div v-show="isActive">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { inject, ref, onBeforeMount, watch } from "vue";
import { Tab } from "./tab_interface";

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    show: {
        type: Boolean,
        default: true,
    },
    href: {
        type: String,
    },
});

const emit = defineEmits<{
    (e: "selected"): void;
    (e: "selection-state", state: boolean): void;
}>();

const isActive = ref(false);
const display = ref("none");

const addTab = inject("addTab") as (tab: Tab) => void;
onBeforeMount(() => {
    addTab({
        name: props.name,
        show: props.show,
    });
});

const tabs = inject("tabsProvider") as {
    tabs: Tab[];
    activeTabIndex: number | null;
};
watch(
    tabs,
    (val) =>
        (isActive.value =
            props.name === val.tabs[val.activeTabIndex ?? 0]?.name)
);

const updateTab = inject("updateTab") as (data: Tab) => void;
watch(
    () => props.show,
    () => {
        updateTab({
            name: props.name,
            show: props.show,
        });
    }
);

watch(isActive, (val) => {
    emit("selection-state", val);
    if (val) {
        if (props.href) window.location.href = props.href;
        emit("selected");
        display.value = "inherit";
    } else display.value = "none";
});
</script>
