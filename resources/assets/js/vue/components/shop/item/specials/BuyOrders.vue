<template>
    <div>
        <div class="header-3">Buy Requests</div>
        <div class="col-1-1">
            <div class="col-7-12">
                This item has
                <b>
                    {{ numOfRequests }}
                </b>
                buy {{ t("request", numOfRequests) }}
            </div>
            <div class="col-5-12">
                <a
                    v-if="BH.user"
                    @click="modals.make_request.active = true"
                    class="button width-100 blue"
                >
                    PLACE ORDER
                </a>
            </div>
        </div>
        <div class="col-1-1">
            <hr />
            <div
                v-if="requests.length == 0"
                style="width: 100%; text-align: center"
            >
                <span>There are no current requests for this item</span>
            </div>
            <table
                v-if="requests.length > 0"
                style="width: 100%; text-align: center"
            >
                <tr class="no-top-border">
                    <th
                        class="center"
                        style="width: 50%; font-weight: 600; font-size: 1.1em"
                    >
                        PRICE
                    </th>
                    <th
                        class="center"
                        style="width: 50%; font-weight: 600; font-size: 1.1em"
                    >
                        COUNT
                    </th>
                </tr>
                <tr v-for="(count, i) in requests" :key="i">
                    <td>
                        <SvgSprite
                            class="svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/currency/bucks_full_color.svg"
                        />
                        <span class="bucks-text">{{ count.bucks }}</span>
                        <span v-if="i == 4"> or less</span>
                    </td>
                    <td>{{ count.count }}</td>
                </tr>
            </table>
            <hr />
            <div v-if="buyRequestPrice > 0" class="mb-20">
                You have an active buy request for
                <SvgSprite
                    class="svg-icon-medium-text svg-icon-margin svg-icon-margin-left"
                    square="20"
                    svg="shop/currency/bucks_full_color.svg"
                />
                <span class="bucks-text">{{ buyRequestPrice }}</span>
            </div>
            <div v-else class="mb-20">
                You do not currently have a buy request for this item.
            </div>
            <div v-if="activeRequests > 0">
                <a href="/currency#buyrequests">
                    You have {{ activeRequests }} active buy
                    {{ t("request", activeRequests) }}, click here to manage
                    them
                </a>
            </div>
        </div>

        <Modal
            title="Create Buy Request"
            v-show="modals.make_request.active"
            @close="modals.make_request.active = false"
        >
            <div class="mb-10">
                If the item you are creating the request on ever drops to the
                price you are asking for you will instantly purchase the item.
            </div>
            <div class="width-100">
                <div class="light-text">You'll pay (min 1)</div>
                <input
                    type="number"
                    v-model="submitPrice"
                    maxlength="15"
                    min="1"
                    max="2147483647"
                    style="width: 100%; box-sizing: border-box"
                    name="bucks_amount"
                />
            </div>

            <div class="modal-buttons">
                <button
                    class="button bucks flat"
                    style="margin-right: 10px"
                    @click="submitOrder"
                >
                    Submit
                </button>
                <button
                    class="cancel-button modal-close"
                    @click="modals.make_request.active = false"
                >
                    Cancel
                </button>
            </div>
        </Modal>
    </div>
</template>

<i18n lang="json" locale="en">
{
    "request": "request | requests"
}
</i18n>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import axios from "axios";
import { reactive, ref } from "vue";
import { BH } from "@/logic/bh";
import Modal from "@/components/global/Modal.vue";
import { computed } from "@vue/reactivity";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import SvgSprite from "@/components/global/SvgSprite.vue";

const { t } = useI18n();

const props = defineProps<{
    itemId: number;
    activeRequests: number;
    hasBuyRequest: number;
}>();

load();

const requests = ref<any>([]);
const modals = reactive({
    make_request: {
        active: false,
    },
});

const buyRequestPrice = ref<number>(props.hasBuyRequest);

const numOfRequests = computed<number>(() => {
    return requests.value.reduce((acc: number, req: any) => acc + req.count, 0);
});

const submitPrice = ref<number>();

async function submitOrder() {
    if (!submitPrice.value) return;
    await axios
        .post(`/shop/buyRequest`, {
            item_id: props.itemId,
            bucks_amount: submitPrice.value,
        })
        .then(() => {
            if (!submitPrice.value) return;
            buyRequestPrice.value = submitPrice.value;
            load();
        })
        .catch(axiosSendErrorToNotification);

    modals.make_request.active = false;
}

function load() {
    axios.get(BH.apiUrl(`v1/shop/${props.itemId}/orders`)).then(({ data }) => {
        requests.value = data;
    });
}
</script>
