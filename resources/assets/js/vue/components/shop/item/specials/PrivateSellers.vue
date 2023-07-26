<template>
    <div>
        <div class="header-3">Private Sellers</div>
        <div class="col-1-1">
            <div class="col-2-3">
                <div>
                    This item has <b>{{ sellerListings }}</b>
                    {{ sellerListings == 1 ? "listing" : "listings" }}
                </div>
            </div>
            <div class="col-1-3">
                <a
                    v-if="serials.length > 0"
                    @click="modals.sell.active = true"
                    class="button width-100 blue"
                >
                    SELL ITEM
                </a>
            </div>
        </div>
        <div class="col-1-1">
            <hr />
            <div>
                <div class="center-text" v-if="sellers.length == 0">
                    There are no private sellers
                </div>
                <div v-for="(seller, i) in sellers" :key="i">
                    <div class="col-1-1 flex">
                        <div
                            class="col-2-12 mobile-col-2-12 mobile-pad"
                            style="padding-right: 5px"
                        >
                            <a :href="`/user/${seller.user.id}/`">
                                <img
                                    class="width-100"
                                    :src="
                                        thumbnailStore.getThumbnail({
                                            id: seller.user.id,
                                            type: ThumbnailType.AvatarFull,
                                        })
                                    "
                                />
                            </a>
                        </div>
                        <div
                            class="col-4-12 mobile-col-4-12 mobile-pad flex-center"
                            style="padding-left: 5px; padding-right: 15px"
                        >
                            <div class="ellipsis" style="margin-bottom: 5px">
                                <a :href="`/user/${seller.user.id}/`">
                                    {{ seller.user.username }}
                                </a>
                            </div>
                            <div class="light-text">
                                #{{ seller.serial }} of {{ stock }}
                            </div>
                        </div>
                        <div
                            class="col-6-12 mobile-col-6-12 mobile-pad flex-center"
                            style="direction: rtl"
                        >
                            <button
                                v-if="seller.user.id != BH.user?.id"
                                @click="openPrivateSellerModal(seller)"
                                class="bucks width-100"
                                :id="
                                    seller.crate_id == lowestSeller.crate_id
                                        ? 'lowestReseller'
                                        : ''
                                "
                            >
                                BUY FOR
                                <SvgSprite
                                    class="svg-icon-medium-text svg-icon-margin-left"
                                    square="20"
                                    svg="shop/currency/bucks_full.svg"
                                />
                                {{ seller.bucks }}
                            </button>
                            <button
                                v-else
                                class="width-100"
                                @click="takeOffsale(seller)"
                                style="background-color: #999"
                            >
                                TAKE OFFSALE
                            </button>
                        </div>
                    </div>
                    <div class="col-1-1"><hr /></div>
                </div>

                <div
                    v-if="resellersAPI.hasNextPage()"
                    style="text-align: center"
                >
                    <div
                        class="pointer bold"
                        @click="resellersAPI.loadNextPage"
                    >
                        LOAD MORE
                    </div>
                </div>
            </div>

            <Modal
                :title="`Sell ${name}`"
                v-show="modals.sell.active"
                @close="modals.sell.active = false"
            >
                <select class="width-100 mb-10" v-model="sellId">
                    <option
                        v-for="(serial, i) in serials"
                        :key="i"
                        :value="serial.id"
                    >
                        #{{ serial.serial.toLocaleString() }} of
                        {{ stock.toLocaleString() }}
                    </option>
                </select>
                <div class="width-100 mb-10">
                    <span style="color: #7f817f">Price (min 1)</span>
                    <input
                        type="number"
                        v-model="sellFor"
                        maxlength="15"
                        min="1"
                        max="2147483647"
                        style="width: 100%; box-sizing: border-box"
                    />
                </div>
                <div class="mb-10">
                    You'll get
                    <SvgSprite
                        class="svg-icon-medium-text svg-icon-margin"
                        square="16"
                        svg="shop/currency/bucks_full_color.svg"
                    />
                    <span>{{ receive }}</span>
                </div>
                <div class="width-100 mb-10">
                    Items priced at or below the highest buy request will sell
                    to the requester
                </div>
                <div class="modal-buttons">
                    <button
                        class="button bucks flat"
                        style="margin-right: 10px"
                        @click="sellSpecial"
                    >
                        Sell Now
                    </button>
                    <button
                        class="cancel-button modal-close"
                        @click="modals.sell.active = false"
                        type="button"
                    >
                        Cancel
                    </button>
                </div>
            </Modal>

            <Modal
                title="Buy Item"
                v-show="modals.purchase.active"
                @close="modals.purchase.active = false"
            >
                Are you sure you want to buy
                <b>{{ name }}</b>
                for
                <span class="bucks-icon" style="margin-left: 2px"></span>
                {{ modals.purchase.item.bucks }} from
                <b>{{ modals.purchase.item.user.username }}</b
                >?
                <div class="modal-buttons">
                    <button
                        class="bucks"
                        style="margin-right: 10px"
                        @click="purchaseSpecial"
                    >
                        Buy Now
                    </button>
                    <button
                        class="cancel-button modal-close"
                        @click="modals.purchase.active = false"
                    >
                        Cancel
                    </button>
                </div>
            </Modal>

            <LogInModal
                v-show="modals.log_in.active"
                @close="modals.log_in.active = false"
            ></LogInModal>

            <NotEnoughModal
                type="bucks"
                v-show="modals.not_enough.active"
                @close="modals.not_enough.active = false"
            ></NotEnoughModal>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from "axios";
import { computed, reactive, ref, watchEffect } from "vue";
import { BH } from "@/logic/bh";
import Modal from "@/components/global/Modal.vue";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";
import NotEnoughModal from "../modals/NotEnoughModal.vue";
import LogInModal from "../modals/LogInModal.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

type Serials = { id: number; serial: number }[];

const props = defineProps<{
    itemId: number;
    stock: number;
    name: string;
    initSerials: Serials;
    sellerListings: number;
}>();

const emit = defineEmits(["newLowestValue"]);

const resellersAPI = new InfiniteScrollAPI<any>(
    `v1/shop/${props.itemId}/resellers`,
    10
);
const { currentData: sellers } = resellersAPI;

resellersAPI.loadNextPage();

const lowestSeller = ref<any>();

watchEffect(() => {
    lowestSeller.value = sellers.value.find((seller: any) => {
        return seller.user.id != BH.user?.id;
    });
    if (typeof lowestSeller.value === "undefined") return;
    emit("newLowestValue", lowestSeller.value.bucks);
});

const modals = reactive({
    sell: {
        active: false,
    },
    purchase: {
        active: false,
        item: { user: {} } as any,
    },
    log_in: {
        active: false,
    },
    not_enough: {
        active: false,
    },
});

const serials = ref<Serials>(props.initSerials);

function takeOffsale(seller: any) {
    axios
        .post(`/shop/takeSpecialOffsale`, {
            crate_id: seller.crate_id,
        })
        .then(() => {
            sellers.value = sellers.value.filter(
                (val: any) => val.crate_id != seller.crate_id
            );
            serials.value.push({
                id: seller.crate_id,
                serial: seller.serial,
            });
            sellId.value = serials.value[0]?.id ?? undefined;
        })
        .catch(axiosSendErrorToNotification);
}

function purchaseSpecial() {
    axios
        .post(`/shop/purchaseSpecial`, {
            purchase_type: 0,
            item_id: props.itemId,
            crate_id: modals.purchase.item.crate_id,
            expected_price: modals.purchase.item.bucks,
            expected_seller: modals.purchase.item.user.id,
        })
        .then(() => {
            successToNotification(
                `${props.name} has been purchased for ${modals.purchase.item.bucks} bucks`
            );
            sellers.value = sellers.value.filter(
                (val: any) => val.crate_id != modals.purchase.item.crate_id
            );
            serials.value.push({
                id: modals.purchase.item.crate_id,
                serial: modals.purchase.item.serial,
            });
        })
        .catch(axiosSendErrorToNotification);
    modals.purchase.active = false;
}

const sellId = ref<number>(serials.value[0]?.id ?? undefined);
const sellFor = ref<number>();
const receive = computed(() => {
    if (typeof sellFor.value === "undefined" || !BH.user) return 0;
    let v = Math.min(sellFor.value, 2 ** 31 - 1);
    if (v > 0) sellFor.value = v;
    return !isNaN(sellFor.value)
        ? Math.round(sellFor.value * BH.user.taxRate).toLocaleString()
        : 0;
});

function sellSpecial() {
    axios
        .post(`/shop/sellSpecial`, {
            item_id: props.itemId,
            crate_id: sellId.value,
            bucks_amount: sellFor.value,
        })
        .then(() => {
            resellersAPI.refreshAPI();
            serials.value = serials.value.filter(
                (val: any) => val.id != sellId.value
            );
        })
        .catch(axiosSendErrorToNotification);
    modals.sell.active = false;
}

function openPrivateSellerModal(item: any) {
    if (!BH.user) return logInModal();
    if (item.bucks > BH.user.bucks) return notEnoughModal();
    modals.purchase.item = item;
    modals.purchase.active = true;
}

function logInModal() {
    modals.log_in.active = true;
}

function notEnoughModal() {
    modals.not_enough.active = true;
}
</script>
