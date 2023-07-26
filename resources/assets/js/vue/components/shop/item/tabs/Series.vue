<template></template>

<style lang="scss"></style>

<script setup lang="ts">
import { toRef, watch } from "vue";
import CursorableAPI from "@/logic/apis/CursorableAPI";

const props = defineProps<{
    itemId: number;
    allowLoad: boolean;
}>();

const versionsAPI = new CursorableAPI<ItemVersion>(
    `v1/shop/${props.itemId}/versions`,
    10
);
const { currentData: itemOwners } = versionsAPI;

const allowLoad = toRef(props, "allowLoad");

watch(allowLoad, () => versionsAPI.loadPage(1));
</script>
