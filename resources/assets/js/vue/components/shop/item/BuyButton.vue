<template>
    <div>
        <div
            v-if="isSellingFree"
            class="col-1-2 mobile-col-1-2 no-pad"
            :class="{
                pr10: isSellingBits || isSellingBucks,
            }"
        >
            <AreYouSure
                buttonClass="free width-100"
                modalTitle="Buy Item"
                modalButtonClass="free"
                modalButtonText="Buy Now"
                :intercepted="modals.buy.free.intercepted"
                @intercept="otherModals('free')"
                @accepted="purchaseItem('free')"
            >
                <template v-slot:button>FREE</template>
                <template v-slot:modal>
                    Are you sure you want to buy
                    <b>{{ itemName }}</b>
                    for <span class="free-text">free</span>?
                </template>
            </AreYouSure>
        </div>

        <div
            v-if="isSellingBucks"
            class="col-1-2 mobile-col-1-2 no-pad"
            :class="{
                pr10: isSellingBits && !isSellingFree,
                pl10: isSellingFree,
            }"
        >
            <AreYouSure
                buttonClass="bucks width-100"
                modalTitle="Buy Item"
                modalButtonClass="bucks"
                modalButtonText="Buy Now"
                :intercepted="modals.buy.bucks.intercepted"
                @intercept="otherModals('bucks')"
                @accepted="purchaseItem('bucks')"
            >
                <template v-slot:button>
                    <SvgSprite
                        class="svg-icon-medium-text svg-icon-margin"
                        square="20"
                        svg="shop/currency/bucks_full.svg"
                    />
                    {{ bucks }}
                    {{ t("buck", bucks ?? 0) }}
                </template>
                <template v-slot:modal>
                    Are you sure you want to buy
                    <b>{{ itemName }}</b>
                    for
                    <SvgSprite
                        class="svg-icon-medium-text svg-icon-margin"
                        square="20"
                        svg="shop/currency/bucks_full_color.svg"
                    />
                    <span class="bucks-text">{{ bucks }}</span>
                    ?
                </template>
            </AreYouSure>
        </div>

        <div
            v-if="isSellingBits"
            class="col-1-2 mobile-col-1-2 no-pad"
            :class="{
                pl10: isSellingBucks || isSellingFree,
            }"
        >
            <AreYouSure
                buttonClass="bits width-100"
                modalTitle="Buy Item"
                modalButtonClass="bits"
                modalButtonText="Buy Now"
                :intercepted="modals.buy.bits.intercepted"
                @intercept="otherModals('bits')"
                @accepted="purchaseItem('bits')"
            >
                <template v-slot:button>
                    <SvgSprite
                        class="svg-icon-medium-text svg-icon-margin"
                        square="20"
                        svg="shop/currency/bits_full.svg"
                    />
                    {{ bits }}
                    {{ t("bit", bits ?? 0) }}
                </template>
                <template v-slot:modal>
                    Are you sure you want to buy
                    <b>{{ itemName }}</b>
                    for
                    <SvgSprite
                        class="svg-icon-medium-text svg-icon-margin"
                        square="20"
                        svg="shop/currency/bits_full_color.svg"
                    />
                    <span class="bits-text">{{ bits }}</span>
                    ?
                </template>
            </AreYouSure>
        </div>

        <LogInModal
            v-show="modals.log_in.active"
            @close="modals.log_in.active = false"
        ></LogInModal>

        <NotEnoughModal
            :type="modals.not_enough.type"
            v-show="modals.not_enough.active"
            @close="modals.not_enough.active = false"
        ></NotEnoughModal>

        <Modal
            title="You already own this item"
            v-show="modals.already_owned.active"
            @close="modals.already_owned.active = false"
        >
            You can't purchase an item you already own
            <div class="modal-buttons">
                <button
                    class="cancel-button modal-close"
                    @click="modals.already_owned.active = false"
                >
                    Cancel
                </button>
            </div>
        </Modal>
    </div>
</template>

<i18n lang="json" locale="en">
{
    "buck": "buck | bucks",
    "bit": "bit | bits"
}
</i18n>

<style scoped>
div.pr10 {
    padding-right: 10px;
}
div.pl10 {
    padding-left: 10px;
}
</style>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import axios from "axios";
import { computed, reactive } from "vue";
import { BH } from "@/logic/bh";
import AreYouSure from "../../global/AreYouSure.vue";
import Modal from "../../global/Modal.vue";
import LogInModal from "./modals/LogInModal.vue";
import NotEnoughModal from "./modals/NotEnoughModal.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

const { t } = useI18n();

const props = defineProps<{
    itemName: string;
    productId: number;
    seller: number;
    bits: number | null;
    bucks: number | null;
    owns: boolean;
}>();

const modals = reactive({
    buy: {
        free: {
            intercepted: false,
        },
        bucks: {
            intercepted: false,
        },
        bits: {
            intercepted: false,
        },
    },
    log_in: {
        active: false,
    },
    already_owned: {
        active: false,
    },
    not_enough: {
        active: false,
        type: "bits" as PurchaseTypes,
    },
});

type PurchaseTypes = "free" | "bits" | "bucks";

const isSellingBucks = computed<boolean>(() => {
    return props.bucks !== null && props.bucks > 0;
});

const isSellingBits = computed<boolean>(() => {
    return props.bits !== null && props.bits > 0;
});

const isSellingFree = computed<boolean>(() => {
    return props.bucks == 0 || props.bits == 0;
});

function purchaseItem(type: PurchaseTypes) {
    let info = getPricingInfo(type);
    axios
        .post("/shop/purchase", {
            product_id: props.productId,
            purchase_type: info.type,
            expected_price: info.price,
            expected_seller: props.seller,
        })
        .then(({ data }) => {
            location.reload();
        });
}

function getPricingInfo(type: PurchaseTypes): {
    type: number;
    price: number | null;
} {
    switch (type) {
        case "free":
            return { type: 2, price: 0 };
        case "bits":
            return { type: 1, price: props.bits };
        case "bucks":
            return { type: 0, price: props.bucks };
    }
}

function otherModals(type: PurchaseTypes) {
    if (!BH.user) {
        modals.buy[type].intercepted = true;
        return logInModal();
    }
    if (props.owns) {
        modals.buy[type].intercepted = true;
        return alreadyOwnedModal();
    }
    if (type !== "free" && (props[type] ?? -1) > BH.user[type]) {
        modals.buy[type].intercepted = true;
        return notEnoughModal(type);
    }
}

function logInModal() {
    modals.log_in.active = true;
}

function notEnoughModal(type: PurchaseTypes) {
    modals.not_enough.active = true;
    modals.not_enough.type = type;
}

function alreadyOwnedModal() {
    modals.already_owned.active = true;
}
</script>
