<template>
    <div class="col-10-12 push-1-12 new-theme">
        <div class="header">
            DESIGNER MENU
            <a href="." class="button clear" style="float: right">Cancel</a>
        </div>
        <div class="col-1-2 left-container">
            <SharedForm
                :edit-page="true"
                :init-item-data="{
                    title: initName,
                    description: initDescription,
                    bucks: initBucks ? Number(initBucks) : null,
                    bits: initBits ? Number(initBits) : null,
                    offsale: initOffsale === 'true' ? true : false,
                    free: initBucks === '0' || initBits === '0',
                }"
                @saved="saveItem"
            />
        </div>
        <div class="col-1-2"></div>
    </div>
</template>

<script setup lang="ts">
import { axiosSendErrorToNotification } from "@/logic/notifications";
import axios from "axios";
import SharedForm from "./SharedForm.vue";
import { ItemData } from "./types";

defineProps<{
    initName: string;
    initDescription: string;
    initBucks?: string;
    initBits?: string;
    initOffsale: string;
}>();

function saveItem(item: ItemData) {
    axios
        .post(``, {
            title: item.title,
            description: item.description,
            bucks: item.bucks,
            bits: item.bits,
            offsale: item.offsale,
            free: item.free,
        })
        .then(() => {
            window.location.href = ".";
        })
        .catch(axiosSendErrorToNotification);
}
</script>
