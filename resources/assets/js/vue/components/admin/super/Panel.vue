<template>
    <VerticalTabs :border="false">
        <Tab name="Website">
            <WebsiteTab />
        </Tab>
        <Tab name="Promocodes">
            <PromoCodesTab />
        </Tab>
        <Tab name="Permissions">
            <PermissionsTab />
        </Tab>
    </VerticalTabs>
</template>

<script setup lang="ts">
import Tab from "@/components/global/tabs/Tab.vue";
import VerticalTabs from "@/components/global/tabs/VerticalTabs.vue";
import WebsiteTab from "./WebsiteTab.vue";
import PromoCodesTab from "./PromoCodesTab.vue";
import PermissionsTab from "./PermissionsTab.vue";
</script>
