<template>
    <div :class="{ 'new-theme': newTheme }">
        <div :class="{ 'col-2-12': !newTheme, 'col-3-12': newTheme }">
            <ul
                :class="{
                    'crate-types': !newTheme,
                    'vertical-tabs': newTheme,
                    'border-right': border,
                }"
            >
                <li
                    v-for="(tab, i) in tabs.filter((tab) => tab.show)"
                    :key="i"
                    :class="{ active: tab.isActive }"
                    @click="selectTab(tab.name)"
                >
                    {{ tab.name }}
                </li>
            </ul>
        </div>
        <div
            :class="{ 'col-10-12': !newTheme, 'col-9-12': newTheme }"
            v-if="tabsLoaded"
        >
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { useTabs } from "./use_tabs";

defineProps({
    tabsLoaded: {
        type: Boolean,
        default: true,
    },
    newTheme: Boolean,
    border: Boolean,
});

const emit = defineEmits(["loaded"]);

const { tabs, selectTab } = useTabs(emit);
</script>
