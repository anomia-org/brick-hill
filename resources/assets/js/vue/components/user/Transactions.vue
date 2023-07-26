<template>
    <div>
        <select
            v-model="type"
            @change="typeChange"
            name="type"
            style="margin-bottom: 10px; height: 30px"
            class="select width-100 small-padding"
        >
            <option value="purchases">Purchases</option>
            <option value="sales">Sales</option>
        </select>
        <table style="width: 100%">
            <tr v-for="item in purchases" :key="item.id">
                <th class="col-2-12" style="text-align: left; float: none">
                    <span class="agray-text block" :title="item.created_at">
                        {{ filterDate(item.created_at) }}
                    </span>
                </th>
                <th
                    class="ellipsis col-4-12"
                    style="text-align: left; float: none"
                >
                    <a
                        class="agray-text ellipsis"
                        :href="`/user/${item.user.id}/`"
                    >
                        <img
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: item.user.id,
                                    type: ThumbnailType.AvatarFull,
                                })
                            "
                            style="width: 64px"
                        />
                        <div>{{ item.user.username }}</div>
                    </a>
                </th>
                <th class="col-4-12" style="text-align: left; float: none">
                    <a class="agray-text" :href="`/shop/${item.item.id}/`">
                        <img
                            style="height: 56px"
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: item.item.id,
                                    type: ThumbnailType.ItemFull,
                                })
                            "
                            :alt="item.item.name"
                        />
                        <div>{{ item.item.name }}</div>
                    </a>
                </th>
                <th class="col-2-12" style="text-align: left; float: none">
                    <span v-if="item.pay_id == 3" style="color: red">
                        GRANTED
                    </span>
                    <span v-else-if="item.pay_id == 4" style="color: red">
                        TRANSFERRED
                    </span>
                    <span v-else-if="item.pay_id == 5" style="color: red">
                        REDEEMED
                    </span>
                    <span v-else-if="item.price == 0" style="color: #6fb6db">
                        FREE
                    </span>
                    <span v-else-if="item.pay_id == 1" class="bits-text">
                        {{ item.price }}
                        <span class="bits-icon"></span>
                    </span>
                    <span v-else-if="item.pay_id == 0" class="bucks-text">
                        {{ item.price }}
                        <span class="bucks-icon"></span>
                    </span>
                </th>
            </tr>
        </table>
        <div v-if="transactionAPI.hasNextPage()" style="text-align: center">
            <button class="blue" @click.passive="transactionAPI.loadNextPage()">
                LOAD MORE
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { filterDate } from "@/filters/index";
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";
import { BH } from "@/logic/bh";
import ThumbnailType from "@/logic/data/thumbnails";
import { thumbnailStore } from "@/store/modules/thumbnails";
import { ref } from "vue";

const props = defineProps({
    user_id: {
        default: BH.user?.id,
        type: Number,
    },
});

const type = ref<string>("purchases");

const transactionAPI = new InfiniteScrollAPI<any>(
    `v1/user/economy/${props.user_id}/transactions/${type.value}`
);
const { currentData: purchases } = transactionAPI;

transactionAPI.loadNextPage();

function typeChange() {
    transactionAPI.setUrl(
        `v1/user/economy/${props.user_id}/transactions/${type.value}`
    );
}
</script>
