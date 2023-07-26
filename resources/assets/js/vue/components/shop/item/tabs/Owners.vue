<template>
    <table v-if="itemOwners.length > 0" class="col-1-1 mb-20 border">
        <tr>
            <th>USER</th>
            <th>SERIAL</th>
            <th>LAST ONLINE</th>
            <th>OWNED SINCE</th>
        </tr>
        <tr v-for="owner in itemOwners" :key="owner.id">
            <td class="flex-container">
                <div class="flex flex-left" style="padding-right: 10px">
                    <img
                        style="width: 64px"
                        :src="
                            thumbnailStore.getThumbnail({
                                id: owner.user.id,
                                type: ThumbnailType.AvatarFull,
                            })
                        "
                    />
                </div>
                <div
                    class="col-10-12 flex flex-center"
                    style="padding-right: 10px"
                >
                    <a :href="`/user/${owner.user.id}`">
                        {{ owner.user.username }}
                    </a>
                </div>
                <div v-if="showTrade" class="flex flex-center flex-right">
                    <a
                        class="button blue thin"
                        :href="`/trade/create/${owner.user.id}`"
                        target="_blank"
                    >
                        TRADE
                    </a>
                </div>
            </td>
            <td>#{{ owner.serial }}</td>
            <td>{{ filterDatetime(owner.user.last_online) }}</td>
            <td>{{ filterDatetime(owner.updated_at) }}</td>
        </tr>
    </table>
    <div v-else class="text-center">Nobody owns this item</div>
    <PageSelector :api="crateAPI" />
</template>

<style lang="scss"></style>

<script setup lang="ts">
import { toRef, watch } from "vue";
import CursorableAPI from "@/logic/apis/CursorableAPI";
import PageSelector from "@/components/global/PageSelector.vue";
import { filterDatetime } from "@/filters/index";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const props = defineProps<{
    itemId: number;
    allowLoad: boolean;
    showTrade: boolean;
}>();

const crateAPI = new CursorableAPI<ItemCrate>(
    `v1/shop/${props.itemId}/owners`,
    50
);
const { currentData: itemOwners } = crateAPI;

const allowLoad = toRef(props, "allowLoad");

watch(allowLoad, () => crateAPI.loadPage(1));
</script>
