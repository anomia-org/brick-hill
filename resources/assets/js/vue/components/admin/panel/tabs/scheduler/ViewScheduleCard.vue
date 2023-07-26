<template>
    <div class="content" style="word-break: break-all">
        <a class="bold" :href="`/shop/${item.id}`" target="_blank">View Item</a>
        <div>
            <span class="bold">Title: </span>
            {{ item.name }}
        </div>
        <div>
            <span class="bold">Description: </span>
            {{ item.description }}
        </div>
        <div>
            <span class="bold">Item Type: </span>
            {{ item.type?.type ?? "None" }}
        </div>
        <div>
            <span class="bold">Series ID: </span>
            {{ item.series_id ?? "None" }}
        </div>
        <div>
            <span class="bold">Event: </span>
            {{ item.event?.name ?? "None" }}
        </div>
        <div>
            <span class="bold">Price: </span>
            <span v-if="item.bits == 0 && item.bucks == 0" class="free-text">
                FREE
            </span>
            <span v-if="item.bits == null && item.bucks == null">
                Offsale
            </span>
            <span v-if="item.bucks > 0" class="bucks-text">
                <span
                    class="bucks-icon"
                    style="vertical-align: middle; padding-right: 0"
                ></span>
                {{ item.bucks }}
            </span>
            <span v-if="item.bits > 0" class="bits-text">
                <span
                    class="bits-icon"
                    style="vertical-align: middle; padding-right: 0"
                ></span>
                {{ item.bits }}
            </span>
        </div>
        <div>
            <span class="bold">Special: </span>
            {{ item.special ? "Yes" : "No" }}
        </div>
        <div>
            <span class="bold">Special Edition: </span>
            {{ item.special_edition ? "Yes" : "No" }}
        </div>
        <div>
            <span class="bold">Special Stock: </span>
            {{ item.stock }}
        </div>
        <div>
            <span class="bold">Timer: </span>
            <span v-if="!item.timer">None</span>
            <span v-else>
                {{ filterDatetime(item.timer_date) }}
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { filterDatetime } from "@/filters/index";

defineProps<{
    item: any;
}>();
</script>
