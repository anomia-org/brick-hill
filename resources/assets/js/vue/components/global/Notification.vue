<template>
    <div class="col-10-12 push-1-12 new-theme" style="position: absolute">
        <div class="notification-holder">
            <div
                class="alert transition"
                :class="[state.notification?.type || 'warning', stateClass]"
            >
                {{ state.notification?.msg }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { notificationStore } from "@/store/modules/notifications";
import { ref, watch } from "vue";

const state = notificationStore.getState();

const stateClass = ref<"closed" | "open">("closed");

let timeout: NodeJS.Timeout;

watch(
    () => state.notification,
    () => {
        stateClass.value = "open";
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            stateClass.value = "closed";
        }, 5000);
    }
);
</script>
