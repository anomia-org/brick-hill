<template>
    <div class="trade-list border-right new-theme">
        <select
            v-model="selectedType"
            @change="changeType"
            class="blend"
            style="width: calc(100% - 10px)"
        >
            <option value="accepted" v-if="more_types">Accepted</option>
            <option value="declined" v-if="more_types">Declined</option>
            <option value="inbound">Inbound</option>
            <option value="outbound">Outbound</option>
            <option value="history">History</option>
            <!-- 
                we want these to be on top if admin panel as accepted are most useful, but inbound first on normal user page as that is most useful
                this is eziest solution
            -->
            <option value="accepted" v-if="!more_types">Accepted</option>
            <option value="declined" v-if="!more_types">Declined</option>
        </select>
        <div
            class="text-center"
            style="margin-top: 10px"
            v-if="trades.length == 0"
        >
            No trades available
        </div>
        <div v-show="trades.length > 0" class="trade-picker" ref="tradePicker">
            <div
                v-for="trade in trades"
                :key="trade.id"
                class="trade flex"
                @click="loadTradeInfo(trade.id)"
                :class="{ selected: attempted_select == trade.id }"
            >
                <component
                    :is="listElementType"
                    :href="open_tab ? `/trades/${trade.id}` : false"
                    :target="open_tab ? `_blank` : false"
                    class="flex trade-inner"
                >
                    <div class="flex full-width">
                        <img
                            class="trade-user-thumbnail"
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: trade.user.id,
                                    type: ThumbnailType.AvatarFull,
                                })
                            "
                        />

                        <div class="full-width flex flex-column">
                            <div
                                class="flex flex-wrap space-between full-width"
                            >
                                <p class="username bold ellipsis">
                                    {{ trade.user.username }}
                                </p>
                                <p class="light-text bold trade-time">
                                    {{ filterTimeAgo(trade.updated_at) }}
                                </p>
                            </div>
                            <div
                                class="flex flex-items-center trade-info mb-5"
                                v-if="trade.sender_avg + trade.receiver_avg > 0"
                            >
                                <div class="svg-container">
                                    <SVGSprite
                                        class="svg-black small-h-margin-r"
                                        square="16"
                                        svg="shop/currency/bucks_full.svg"
                                    />
                                </div>
                                <p class="light-text bold" style="">
                                    {{ trade.sender_avg }}
                                </p>
                                <div class="svg-container">
                                    <SVGSprite
                                        class="svg-white small-h-margin"
                                        square="16"
                                        svg="user/trade/double_arrow.svg"
                                    />
                                </div>
                                <div class="svg-container">
                                    <SVGSprite
                                        class="svg-black small-h-margin-r"
                                        square="16"
                                        svg="shop/currency/bucks_full.svg"
                                    />
                                </div>
                                <p class="light-text bold">
                                    {{ trade.receiver_avg }}
                                </p>
                            </div>
                            <div v-if="!trade.is_pending">
                                <p class="no-margin bold small-text">
                                    {{ tradeStatus(trade) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </component>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import { filterTimeAgo } from "@/filters/index";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import SVGSprite from "@/components/global/SvgSprite.vue";

const props = defineProps<{
    user: string;
    open_tab?: boolean;
    more_types?: boolean;
}>();

const emit = defineEmits(["newTrade", "typeChanged"]);

const selectedType = ref<string>(props.more_types ? "accepted" : "inbound");
const tradePicker = ref<HTMLDivElement>();

onMounted(() => {
    hasInfiniteScroll(loadTrades, tradePicker.value);
});

const cursor = ref<string | null>("");
const loading = ref<boolean>(false);

const attempted_select = ref<number>(0);
const url_select = ref<number>(0);
const trades = ref<any>([]);

let path = window.location.pathname.split("/");
let attempted_trade = Number(path[2]);
if (!isNaN(attempted_trade) && attempted_trade > 0 && !props.open_tab) {
    url_select.value = attempted_trade;
    loadTradeInfo(attempted_trade);
}

async function loadTrades() {
    if (cursor.value === null) return;
    loading.value = true;
    await axios
        .get(
            BH.apiUrl(
                `v1/user/trades/${props.user}/${selectedType.value}?limit=25&cursor=${cursor.value}`
            )
        )
        .then(({ data }) => {
            if (
                data.data.length > 0 &&
                trades.value.length == 0 &&
                !url_select.value &&
                !props.open_tab
            )
                loadTradeInfo(data.data[0].id);
            trades.value.push(...data.data);
            cursor.value = data.next_cursor;
        });
    loading.value = false;
}

function loadTradeInfo(tradeId: number) {
    attempted_select.value = tradeId;
    if (props.open_tab) {
        window.open(`/trades/${tradeId}`, "_blank");
        return;
    }

    if (url_select.value != tradeId) removeUrlSelect();
    axios.get(BH.apiUrl(`v1/user/trades/${tradeId}`)).then(({ data }) => {
        if (data.data.id == attempted_select.value) emit("newTrade", data.data);
    });
}

function removeUrlSelect() {
    window.history.pushState("trades", "Trades", "/trades");
    url_select.value = 0;
}

function tradeStatus(trade: any) {
    if (trade.is_pending) return "PENDING";
    if (trade.is_accepted) return "ACCEPTED";
    if (trade.has_errored) return "ERRORED";
    if (trade.is_cancelled) return "CANCELLED";
    return "DECLINED";
}

function changeType() {
    emit("typeChanged");
    cursor.value = "";
    trades.value = [];
    loadTrades();
}

const listElementType = computed(() => {
    if (props.open_tab) return "a";
    return "div";
});
</script>
