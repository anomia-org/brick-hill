<template>
    <VerticalTabs
        v-if="permissionStore.can('manage shop')"
        ref="Tabs"
        @loaded="tabsLoaded"
    >
        <Tab name="Scheduler">
            <Scheduler />
        </Tab>
        <Tab name="New Item">
            <NewItem />
        </Tab>
        <Tab name="Update Item">
            <UpdateItem />
        </Tab>
        <Tab
            :show="permissionStore.can('approve item schedule')"
            name="New Asset"
        >
            <NewAsset />
        </Tab>
        <Tab
            :show="permissionStore.can('approve item schedule')"
            name="Update Asset"
        >
            <UpdateAsset />
        </Tab>
        <Tab :show="permissionStore.can('manage events')" name="Events">
            <Events />
        </Tab>
    </VerticalTabs>
</template>

<script setup lang="ts">
import Tab from "@/components/global/tabs/Tab.vue";
import VerticalTabs from "@/components/global/tabs/VerticalTabs.vue";

import NewItem from "./NewItem.vue";
import NewAsset from "./NewAsset.vue";
import Scheduler from "./Scheduler.vue";
import UpdateItem from "./UpdateItem.vue";
import UpdateAsset from "./UpdateAsset.vue";
import { permissionStore } from "@/store/modules/permission";
import Events from "./Events.vue";

permissionStore.loadCan(
    "manage shop",
    "approve item schedule",
    "manage events"
);

function tabsLoaded() {
    let queryEdit = new URLSearchParams(window.location.search).get("editId");
    if (queryEdit) window.location.hash = "Update Item";
}
</script>
