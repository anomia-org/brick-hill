<template>
    <div :class="{ tabs: !newTheme, 'new-tabs': newTheme }">
        <div
            class="no-pad"
            v-for="(tab, i) in tabs"
            :key="i"
            :class="`col-1-${tabs.length}`"
            @click="selectTab(tab.name)"
        >
            <div
                class="tab"
                :class="{
                    active: tab.isActive,
                    'small-tab-text': smallText,
                    'last-tab': i == tabs.length - 1,
                }"
            >
                <slot :name="tab.name.replace(' ', '').toLowerCase()"></slot>
                <span
                    v-if="
                        typeof $slots[
                            tab.name.replace(' ', '').toLowerCase()
                        ] === 'undefined'
                    "
                    >{{ tab.name }}</span
                >
            </div>
        </div>
        <div class="tab-holder" v-show="tabsLoaded">
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
    smallText: Boolean,
});

const emit = defineEmits(["loaded"]);

const { tabs, selectTab } = useTabs(emit);
</script>
