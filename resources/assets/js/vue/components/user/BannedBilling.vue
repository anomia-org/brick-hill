<template>
    <div>
        <button
            v-if="hasActiveMembership"
            @click="cancelActiveMembership"
            class="blue"
        >
            Cancel Active Membership
        </button>
        <button @click="enterBillingPortal" class="blue">Billing Portal</button>
    </div>
</template>

<script setup lang="ts">
import { BH } from "@/logic/bh";
import { notificationStore } from "@/store/modules/notifications";
import axios from "axios";

defineProps<{
    hasActiveMembership: boolean;
}>();

function cancelActiveMembership() {
    axios
        .post(BH.apiUrl("v1/billing/cancelSubscription"))
        .then(({ data }) => {
            if (data.error) {
                notificationStore.setNotification(data.error, "error");
            } else {
                location.reload();
            }
        })
        .catch((error) => {
            if (error.response.data.error) {
                notificationStore.setNotification(
                    error.response.data.error,
                    "error"
                );
            }
        });
}

function enterBillingPortal() {
    axios.post(BH.apiUrl("v1/billing/portal")).then(({ data }) => {
        if (data.url) location.href = data.url;
    });
}
</script>
