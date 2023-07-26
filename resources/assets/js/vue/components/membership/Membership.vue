<template>
    <div class="col-10-12 push-1-12" v-if="Object.keys(prices).length > 0">
        <div class="alert warning" v-if="saleEndsAt > new Date()">
            Sale ends in
            <Countdown :countdown-to="saleEndsAt" :reload="true"></Countdown>
        </div>
        <PurchaseForm
            v-if="inPurchaseForm"
            :productName="selectedProductName"
            :productId="selectedProductId"
        />
        <div v-else>
            <div class="card">
                <div class="top orange">Memberships</div>
                <div class="content">
                    <div class="center-text small-text">
                        With Membership you get a higher daily allowance and
                        more freedom when trading, buying, and selling items.
                        You're also entered into monthly giveaways and
                        experience bonuses depending on your package. First-time
                        buyers also get a <b>100 buck</b> signing bonus!
                    </div>
                    <hr />
                    <div class="membership-holder" style="margin-bottom: 25px">
                        <div class="membership-header ace">
                            <span
                                class="membership-icon ace-icon absolute"
                            ></span>
                            <div class="membership-text white-text">
                                <div class="smedium-text bold">
                                    ACE MEMBERSHIP
                                </div>
                                <div class="membership-perks smaller-text">
                                    <div>
                                        Receive <b>20 daily bucks</b>, make up
                                        to <b>10 clans</b>, and create up to
                                        <b>10 sets</b>.
                                    </div>
                                    <div>
                                        Create <b>20 buy requests</b>, and have
                                        a <b>5% lower tax</b>.
                                    </div>
                                    <div>
                                        <b>100 items</b> in trades,
                                        <b>unlimited specials</b> onsale.
                                    </div>
                                    <div>
                                        Entered once into
                                        <b>monthly giveaway</b>.
                                    </div>
                                </div>
                            </div>
                            <img
                                class="membership-bkg hide-on-mobile"
                                src="https://brkcdn.com/images/membership/acebkg.png"
                            />
                            <img
                                class="membership-items hide-on-mobile"
                                src="https://brkcdn.com/images/membership/aceitems.png"
                            />
                        </div>
                        <div class="membership-buttons">
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.ace[1].id,
                                        1,
                                        'Ace'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    ace: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <span v-if="prices.ace[1].sale">
                                    <div class="membership-length">
                                        ${{ prices.ace[1].price.toFixed(2) }}
                                        for first month
                                    </div>
                                    <span
                                        class="bold medium-text"
                                        style="
                                            text-decoration: line-through;
                                            text-decoration-color: #fe292b;
                                        "
                                        >${{
                                            prices.ace[1].sale.toFixed(2)
                                        }}/month</span
                                    >
                                </span>
                                <span v-else>
                                    <span class="bold medium-text">$5.99</span>
                                    <span class="membership-month">/month</span>
                                </span>
                            </button>
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.ace[6].id,
                                        6,
                                        'Ace'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    ace: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <div class="membership-length">6 MONTHS</div>
                                <span class="bold medium-text">$32.99</span>
                                <div
                                    :class="{
                                        gray: alreadyHasMembership,
                                        ace: !alreadyHasMembership,
                                    }"
                                    class="membership-average small-text"
                                >
                                    Avg. monthly of $5.49
                                </div>
                            </button>
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.ace[12].id,
                                        12,
                                        'Ace'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    ace: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <div class="membership-length">12 MONTHS</div>
                                <span class="bold medium-text">$57.99</span>
                                <div
                                    :class="{
                                        gray: alreadyHasMembership,
                                        ace: !alreadyHasMembership,
                                    }"
                                    class="membership-average small-text"
                                >
                                    Avg. monthly of $4.83
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="membership-holder">
                        <div class="membership-header royal">
                            <span
                                class="membership-icon royal-icon absolute"
                            ></span>
                            <div class="membership-text white-text">
                                <div class="smedium-text bold">
                                    ROYAL MEMBERSHIP
                                </div>
                                <div class="membership-perks smaller-text">
                                    <div>
                                        Receive <b>70 daily bucks</b>, make up
                                        to <b>20 clans</b>, and create up to
                                        <b>20 sets</b>.
                                    </div>
                                    <div>
                                        Create <b>50 buy requests</b>, and have
                                        a <b>10% lower tax</b>.
                                    </div>
                                    <div>
                                        <b>100 items</b> in trades,
                                        <b>unlimited specials</b> onsale.
                                    </div>
                                    <div>
                                        Entered twice into
                                        <b>monthly giveaway</b>.
                                    </div>
                                </div>
                            </div>
                            <img
                                class="membership-bkg hide-on-mobile"
                                src="https://brkcdn.com/images/membership/royalbkg.png"
                            />
                            <img
                                class="membership-items hide-on-mobile"
                                src="https://brkcdn.com/images/membership/royalitems.png"
                            />
                        </div>
                        <div class="membership-buttons">
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.royal[1].id,
                                        1,
                                        'Royal'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    royal: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <span v-if="prices.royal[1].sale">
                                    <div class="membership-length">
                                        ${{ prices.royal[1].price.toFixed(2) }}
                                        for first month
                                    </div>
                                    <span
                                        class="bold medium-text"
                                        style="
                                            text-decoration: line-through;
                                            text-decoration-color: #fe292b;
                                        "
                                        >${{
                                            prices.royal[1].sale.toFixed(2)
                                        }}/month</span
                                    >
                                </span>
                                <span v-else>
                                    <span class="bold medium-text">$12.99</span>
                                    <span class="membership-month">/month</span>
                                </span>
                            </button>
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.royal[6].id,
                                        6,
                                        'Royal'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    royal: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <div class="membership-length">6 MONTHS</div>
                                <span class="bold medium-text">$68.99</span>
                                <div
                                    :class="{
                                        gray: alreadyHasMembership,
                                        royal: !alreadyHasMembership,
                                    }"
                                    class="membership-average small-text"
                                >
                                    Avg. monthly of $11.50
                                </div>
                            </button>
                            <button
                                @click="
                                    purchaseFormMembership(
                                        prices.royal[12].id,
                                        12,
                                        'Royal'
                                    )
                                "
                                :class="{
                                    gray: alreadyHasMembership,
                                    royal: !alreadyHasMembership,
                                }"
                                class="membership-button white-text plain flat"
                            >
                                <div class="membership-length">12 MONTHS</div>
                                <span class="bold medium-text">$126.99</span>
                                <div
                                    :class="{
                                        gray: alreadyHasMembership,
                                        royal: !alreadyHasMembership,
                                    }"
                                    class="membership-average small-text"
                                >
                                    Avg. monthly of $10.58
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="center-text">
                        <a href="/membership/giveaway">
                            <button
                                class="lottery-button white-text plain flat"
                            >
                                <div class="smedium-text">VIEW GIVEAWAY</div>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="top green">Bucks</div>
                <div class="content membership-buttons">
                    <div
                        v-if="
                            typeof bucksItem !== 'undefined' &&
                            Object.keys(bucksItem).length > 0
                        "
                        class="new-theme"
                        style="position: relative"
                    >
                        <div class="bucks-absolute white-text">
                            <div class="header mb-10" style="font-weight: 700">
                                LIMITED VIRTUAL ITEM!
                            </div>
                            <div class="mb-10">
                                Stock up on virtual currency for your avatar!
                            </div>
                            <div style="max-width: 400px">
                                <a :href="`/shop/${bucksItem.id}`">
                                    Spend
                                    <b
                                        >${{
                                            (bucksItem.price / 100)
                                                .toFixed(2)
                                                .replace(".00", "")
                                        }}</b
                                    >
                                    or more on bucks before
                                    <b>{{ bucksItem.ends_at }}</b> to receive
                                    this exclusive item!</a
                                >
                            </div>
                        </div>
                        <div class="no-mobile limited-item">
                            <img :src="bucksItem.image" />
                        </div>
                        <div class="bucks-grad"></div>
                    </div>
                    <hr />
                    <button
                        v-for="(data, i) in prices.bucks"
                        :key="i"
                        @click="purchaseFormBucks(data.id, i)"
                        style="margin-bottom: 5px"
                        class="membership-button bucks white-text plain flat"
                    >
                        <div class="bucks-amount medium-text bold">
                            {{ i }} BUCKS
                        </div>
                        <span
                            v-if="data.sale"
                            style="
                                transform: translateY(23%);
                                color: #e20000;
                                text-decoration: line-through;
                                font-size: 13px;
                            "
                            >${{ data.sale.toFixed(2) }}</span
                        >
                        <span
                            v-if="data.sale"
                            style="transform: translateY(110%)"
                            class="bucks-price"
                            >${{ data.price.toFixed(2) }}</span
                        >
                        <span v-else class="bucks-price"
                            >${{ data.price.toFixed(2) }}</span
                        >
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.limited-item {
    position: absolute;
    top: -10px;
    right: 32px;

    img {
        height: 156px;
    }
}
.bucks-grad {
    height: 146px;
    background: url("@/../../images/membership/bucks_bg.png"),
        linear-gradient(90deg, #00842c 0%, #00bc44 100%);
}
.bucks-absolute {
    text-align: left;
    position: absolute;
    top: 10px;
    left: 10px;
}
@media handheld, only screen and (min-width: 767px) {
    .bucks-grad {
        margin: 0 22px;
    }
    .bucks-absolute {
        left: 32px;
    }
}
@media handheld, only screen and (max-width: 767px) {
    .bucks-absolute .header {
        font-size: 1em;
    }
}
</style>

<script setup lang="ts">
import { notificationStore } from "@/store/modules/notifications";
import axios from "axios";
import { ref } from "vue";
import { BH } from "@/logic/bh";
import PurchaseForm from "./PurchaseForm.vue";
import Countdown from "@/components/global/Countdown.vue";

const props = defineProps<{
    alreadyHasMembership: boolean;
}>();

loadProducts();
if (props.alreadyHasMembership) {
    notificationStore.setNotification(
        "You can only purchase one membership at a time. You can cancel your current membership in the settings.",
        "error"
    );
}

const prices = ref<any>({});

const bucksItem = ref<{
    id: number;
    image: string;
    ends_at: string;
    price: number;
}>();

const saleEndsAt = ref<Date>(new Date());

function loadProducts() {
    axios.get(BH.apiUrl(`v1/products/all`)).then(({ data }) => {
        saleEndsAt.value = new Date(data.sale_ends_at);

        bucksItem.value = data.bucks;

        let kData: any = {};
        for (let product of data.products) {
            if (
                data.sale > 0 &&
                !product.name.includes("Annually") &&
                !product.name.includes("Biannually")
            ) {
                product.sale = product.price_in_cents / 100;
                product.price_in_cents =
                    product.price_in_cents -
                    product.price_in_cents / (1 / data.sale);
            }
            product.price = product.price_in_cents / 100;
            kData[product.name] = product;
        }
        prices.value = {
            ace: {
                1: kData["Ace Monthly"],
                6: kData["Ace Biannually"],
                12: kData["Ace Annually"],
            },
            royal: {
                1: kData["Royal Monthly"],
                6: kData["Royal Biannually"],
                12: kData["Royal Annually"],
            },
            bucks: {
                500: kData["500 Bucks"],
                1000: kData["1000 Bucks"],
                2000: kData["2000 Bucks"],
                5000: kData["5000 Bucks"],
                10000: kData["10000 Bucks"],
            },
        };
    });
}

const selectedProductName = ref<string>("");
const selectedProductId = ref<number>(0);
const inPurchaseForm = ref<boolean>(false);

function purchaseFormBucks(productId: number, amountOfBucks: number) {
    selectedProductName.value = `${amountOfBucks} Bucks`;
    selectedProductId.value = productId;
    inPurchaseForm.value = true;
}

function purchaseFormMembership(
    productId: number,
    length: number,
    type: string
) {
    if (props.alreadyHasMembership) return;
    let lengthFormatted = "";
    switch (length) {
        case 1:
            lengthFormatted = "Monthly";
            break;
        case 6:
            lengthFormatted = "Biannually";
            break;
        case 12:
            lengthFormatted = "Annually";
            break;
    }
    selectedProductName.value = `${type} ${lengthFormatted}`;
    selectedProductId.value = productId;
    inPurchaseForm.value = true;
}
</script>
