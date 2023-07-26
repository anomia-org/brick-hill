<template>
    <div v-if="itemLoaded" class="new-theme">
        <div class="col-1-1 top-content mb-10">
            <div class="col-5-12">
                <div
                    class="item-img-content item-border"
                    :class="{
                        'special-border': isSpecial,
                        'owns-border': !isSpecial && owns,
                        'event-border': !isSpecial && item.event !== null,
                    }"
                >
                    <ItemTriangle
                        :item="item"
                        :is-large="true"
                        :owns="owns"
                    ></ItemTriangle>
                    <img
                        :src="
                            thumbnailStore.getThumbnail({
                                id: itemId,
                                type: ThumbnailType.ItemFull,
                            })
                        "
                        class="width-100"
                    />
                </div>
            </div>
            <div class="col-7-12 item-data-content">
                <div class="col-2-3 mobile-col-1-1">
                    <div
                        class="header-3"
                        style="word-break: break-word"
                        :style="{
                            'margin-bottom': `${
                                itemLoaded && item.timer ? 0 : 10
                            }px`,
                        }"
                    >
                        {{ itemName }}
                    </div>
                    <div
                        v-if="itemLoaded && item.timer"
                        class="col-1-1 red-text bold ellipsis"
                        style="margin-bottom: 5px"
                    >
                        <SvgSprite
                            class="svg-icon svg-icon-margin"
                            square="18"
                            svg="shop/main/timer.svg"
                        />
                        <Countdown
                            :countdown-to="item.timer_date"
                            :short-form="true"
                            :add-left="true"
                            @finished="timerFinished"
                        />
                    </div>
                    <div
                        v-if="
                            productId && itemLoaded && !soldOut && !item.offsale
                        "
                        class="col-1-1 mobile-col-1-1 no-pad"
                    >
                        <BuyButton
                            :item-name="item.name"
                            :product-id="productId"
                            :seller="item.creator.id"
                            :bits="item.bits"
                            :bucks="item.bucks"
                            :owns="owns"
                        ></BuyButton>
                    </div>
                    <div
                        v-if="
                            itemLoaded && typeof lowestReseller !== 'undefined'
                        "
                        class="col-1-1 no-pad flex mb-20-no-mobile"
                    >
                        <div
                            class="col-1-2 mobile-col-1-2 no-pad pr10"
                            style="margin-bottom: 0"
                        >
                            <button
                                class="bucks width-100"
                                @click="clickLowestResller"
                            >
                                <SvgSprite
                                    class="svg-icon-medium-text svg-icon-margin"
                                    square="20"
                                    svg="shop/currency/bucks_full.svg"
                                />
                                {{ lowestReseller }} bucks
                            </button>
                        </div>
                        <div
                            class="col-1-2 mobile-col-1-2 no-pad pl10 flex-center bold pointer"
                            @click="scrollLowestReseller"
                        >
                            SEE MORE
                        </div>
                    </div>
                    <div
                        class="col-1-1 red-text mb-10 bold"
                        style="margin-top: 5px"
                    >
                        <span
                            v-if="
                                itemLoaded &&
                                item.special_edition &&
                                item.stock_left
                            "
                        >
                            {{ item.stock_left }} of {{ item.stock }} remaining
                        </span>
                    </div>
                    <div style="margin-bottom: 20px">
                        <div
                            class="col-1-2 mobile-col-1-2 split-box light-text"
                        >
                            <div>Type</div>
                            <div>Sold</div>
                            <div>Created</div>
                            <div>Updated</div>
                        </div>
                        <div
                            v-if="itemLoaded"
                            class="col-1-2 mobile-col-1-2 split-data"
                        >
                            <div style="text-transform: capitalize">
                                {{ item.type.type }}
                            </div>
                            <div>{{ sold }}</div>
                            <div :title="filterDatetimeLong(item.created_at)">
                                {{ filterDate(item.created_at) }}
                            </div>
                            <div :title="filterDatetimeLong(item.updated_at)">
                                {{ filterDate(item.updated_at) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="itemLoaded" class="col-1-3 no-mobile text-center">
                    <a :href="`/user/${item.creator.id}`">
                        <img
                            class="avatar-img"
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: item.creator.id,
                                    type: ThumbnailType.AvatarFull,
                                })
                            "
                        />
                        <div>{{ item.creator.username }}</div>
                    </a>
                </div>
                <div class="col-1-1 mobile-col-1-1 center-text bottom-data">
                    <hr />
                    <div class="col-2-3 unselectable">
                        <template v-if="isOfficial">
                            <div
                                class="col-1-3 mobile-col-1-3 pointer ellipsis"
                                @click="toggleSeries"
                            >
                                <div>
                                    <SvgSprite
                                        v-if="seriesOpened"
                                        class="svg-icon-medium-text svg-white"
                                        square="20"
                                        svg="shop/main/hat_series_full.svg"
                                    />
                                    <SvgSprite
                                        v-else
                                        class="svg-icon-medium-text svg-white"
                                        square="20"
                                        svg="shop/main/hat_series.svg"
                                    />
                                </div>
                                SERIES
                            </div>
                            <div
                                class="col-1-3 mobile-col-1-3 pointer ellipsis"
                                @click="toggleVersions"
                            >
                                <div>
                                    <SvgSprite
                                        v-if="versionsOpened"
                                        class="svg-icon-medium-text svg-white"
                                        square="20"
                                        svg="shop/main/hat_versions_full.svg"
                                    />
                                    <SvgSprite
                                        v-else
                                        class="svg-icon-medium-text svg-white"
                                        square="20"
                                        svg="shop/main/hat_versions.svg"
                                    />
                                </div>
                                VERSIONS
                            </div>
                            <div
                                class="col-1-3 mobile-col-1-3 pointer ellipsis"
                            >
                                <Wishlist
                                    :poly_id="itemId"
                                    :type="ModelRelation.Item"
                                    :onLoadWishlisted="onLoadWishlisted"
                                />
                            </div>
                        </template>
                    </div>
                    <div class="col-1-3">
                        <div
                            v-if="itemLoaded"
                            class="mobile-only mobile-col-1-3 ellipsis"
                            style="padding-top: 7px"
                        >
                            <a :href="`/user/${item.creator.id}`">
                                {{ item.creator.username }}
                            </a>
                        </div>
                        <div
                            class="col-1-2 mobile-col-1-3"
                            style="color: #fec200; margin-top: 4px"
                        >
                            <Favorite
                                :poly_id="itemId"
                                :on_load_favorited="onLoadFavorited"
                                :on_load_favorites="onLoadFavorites"
                                :type="ModelRelation.Item"
                                :newTheme="true"
                            ></Favorite>
                        </div>
                        <div class="col-1-2 mobile-col-1-3">
                            <div
                                class="pointer svg-black svg-icon-large"
                                style="padding-top: 4px"
                                id="edit-dropdown"
                            >
                                <SvgSprite
                                    class="more-full-svg"
                                    square="28"
                                    svg="sets/more_full.svg"
                                />
                                <SvgSprite
                                    class="more-svg"
                                    square="28"
                                    svg="sets/more.svg"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="itemLoaded && item.tags.length > 0" class="mb-10">
            <Tag v-for="tag in item.tags" :tag="tag.name" :for-display="true" />
        </div>
        <div v-if="itemLoaded && item.event !== null" class="mb-10 blue-text">
            <SvgSprite
                class="svg-icon"
                square="30"
                svg="shop/main/event.svg"
                style="vertical-align: middle"
            />
            This item {{ itemEventState }} obtainable in the
            {{ item.event.name }} event
            <template
                v-if="
                    item.event.start_date !== null &&
                    item.event.end_date !== null
                "
            >
                running from
                <span :title="filterDatetimeLong(item.event.start_date)">{{
                    filterDate(item.event.start_date)
                }}</span>
                to
                <span :title="filterDatetimeLong(item.event.end_date)">{{
                    filterDate(item.event.end_date)
                }}</span>
            </template>
        </div>
        <div
            v-if="itemLoaded"
            class="col-1-1 slow height-transition"
            :class="{ open: versionsOpened }"
        >
            <Versions :item-id="item.id" :allow-load="lazyLoad.versions" />
        </div>
        <div
            v-if="itemLoaded"
            class="col-1-1 slow height-transition"
            :class="{ open: seriesOpened }"
        >
            <Series :item-id="item.id" :allow-load="lazyLoad.series" />
        </div>
        <div
            v-if="itemLoaded && item.description?.length > 0"
            style="margin-bottom: 50px"
        >
            <div>
                <div class="header-3">Description</div>
                <p style="white-space: pre-line">{{ item.description }}</p>
            </div>
        </div>
        <div v-if="itemLoaded && soldOut" class="col-1-1 mb-20">
            <div class="col-1-1 mb-20">
                <PriceChart :item-id="itemId"></PriceChart>
            </div>
            <div
                v-if="
                    typeof itemSpecialData !== 'undefined' &&
                    itemSpecialData?.last_calculated !== null
                "
                class="col-1-1 mb-20"
            >
                <SpecialData :data="itemSpecialData"></SpecialData>
            </div>
            <div class="col-1-2 mobile-col-1-1">
                <PrivateSellers
                    :item-id="itemId"
                    :stock="item.stock"
                    :name="item.name"
                    :init-serials="serials"
                    :seller-listings="itemSpecialData?.seller_listings ?? 0"
                    @new-lowest-value="newLowestReseller"
                ></PrivateSellers>
            </div>
            <div class="col-1-2 mobile-col-1-1">
                <BuyOrders
                    :item-id="itemId"
                    :has-buy-request="hasBuyRequest"
                    :active-requests="itemSpecialData?.active_buy_requests ?? 0"
                ></BuyOrders>
            </div>
        </div>
        <div>
            <Tabs :new-theme="true">
                <Tab
                    name="Comments"
                    @selection-state="(state: boolean) => lazyLoad.comments = state"
                >
                    <Comments
                        :poly-id="itemId"
                        :creator-id="item.creator?.id"
                        :type="ModelRelation.Item"
                        :allow-load="lazyLoad.comments"
                    />
                </Tab>
                <Tab name="Related Items" @selected="lazyLoad.related = true">
                    <RelatedItems
                        :item-id="itemId"
                        :allow-load="lazyLoad.related"
                    />
                </Tab>
                <Tab name="Owners" @selected="lazyLoad.owners = true">
                    <Owners
                        :item-id="itemId"
                        :allow-load="lazyLoad.owners"
                        :show-trade="isSpecial"
                    />
                </Tab>
            </Tabs>
        </div>

        <ShopDropdown
            v-if="itemLoaded"
            :item-id="item.id"
            :creator-id="item.creator.id"
            :can-update-item="permissionStore.can(updateItemPermission)"
            :can-update-user="permissionStore.can(updateUserPermission)"
        ></ShopDropdown>
    </div>
</template>

<style scoped>
div.pr10 {
    padding-right: 10px;
}
div.pl10 {
    padding-left: 10px;
}
</style>

<style lang="scss">
.alternative-container {
    transition: all 1s;
}
.top-content {
    display: flex;
}
.item-img-content {
    position: relative;
    border: 1px solid;
    border-radius: 2px;
    padding: 10px;
    width: 100%;
    @include themify() {
        border-color: themed("inputs", "blend-border");
        background: themed("media", "gradient");
    }
}
.item-data-content {
    display: flex;
    flex-wrap: wrap;
    flex: 1;
}
.bottom-data {
    margin-top: auto;

    padding-bottom: 10px;
    border-bottom: 2px solid;
    @include themify($themes) {
        border-color: themed("inputs", "blend-border");
    }
}
#edit-dropdown {
    &:not(:hover) .more-full-svg {
        display: none;
    }

    &:hover .more-svg {
        display: none;
    }
}
@media handheld, only screen and (max-width: 767px) {
    .top-content {
        display: block;
    }
}
.split-box div {
    font-weight: 700;
    padding: 7px;
    text-transform: uppercase;
}
.split-data div {
    text-align: right;
    padding: 7px;
}
.avatar-img {
    width: 100%;
}
</style>

<script setup lang="ts">
import axios from "axios";
import { computed, reactive, ref } from "vue";
import { BH } from "@/logic/bh";
import { filterDate, filterDatetimeLong } from "@/filters/index";
import { thumbnailStore } from "@/store/modules/thumbnails";
import { permissionStore } from "@/store/modules/permission";
import { notificationStore } from "@/store/modules/notifications";
import ThumbnailType from "@/logic/data/thumbnails";
import ModelRelation from "@/logic/data/relations";
import Tabs from "@/components/global/tabs/Tabs.vue";
import Tab from "@/components/global/tabs/Tab.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import Countdown from "@/components/global/Countdown.vue";
import Comments from "@/components/polymorphic/Comments.vue";
import Favorite from "@/components/polymorphic/Favorite.vue";
import Owners from "./tabs/Owners.vue";
import RelatedItems from "./tabs/RelatedItems.vue";
import BuyButton from "./BuyButton.vue";
import PriceChart from "./specials/PriceChart.vue";
import PrivateSellers from "./specials/PrivateSellers.vue";
import BuyOrders from "./specials/BuyOrders.vue";
import SpecialData from "./specials/SpecialData.vue";
import ItemTriangle from "../shared/ItemTriangle.vue";
import ShopDropdown from "./ShopDropdown.vue";
import Tag from "../shared/Tag.vue";
import Wishlist from "@/components/polymorphic/Wishlist.vue";
import Versions from "./tabs/Versions.vue";
import Series from "./tabs/Series.vue";

const props = defineProps<{
    itemId: number;
    itemName: string;
    productId?: number;
    owns: boolean;
    sold: string;

    hasBuyRequest: number;

    isOfficial: boolean;

    serials: { id: number; serial: number }[];

    onLoadFavorites: string;
    onLoadFavorited?: boolean;

    onLoadWishlisted?: boolean;
}>();

let updateItemPermission = {
    permission: props.isOfficial ? "updateOfficial" : "update",
    id: props.itemId,
    relation: ModelRelation.Item,
};
let updateUserPermission = {
    permission: "update",
    id: Number(),
    relation: ModelRelation.User,
};

const itemLoaded = ref<boolean>(false);
const item = ref<Item>({} as Item);
const itemSpecialData = ref<SpecialData>();
const lazyLoad = reactive({
    comments: true,
    related: false,
    owners: false,

    versions: false,
    series: false,
});

const seriesOpened = ref<boolean>(false);
const versionsOpened = ref<boolean>(false);

function toggleVersions() {
    seriesOpened.value = false;
    //versionsOpened.value = !versionsOpened.value;

    //lazyLoad.versions = true;
}

function toggleSeries() {
    versionsOpened.value = false;
    //seriesOpened.value = !seriesOpened.value;

    //lazyLoad.series = true;
}

loadItem();

function timerFinished() {
    item.value.offsale = true;
    item.value.timer = false;
}

const isSpecial = computed<boolean>(() => {
    return !!(item.value.special || item.value.special_edition);
});

const soldOut = computed<boolean>(() => {
    return (
        itemLoaded &&
        (item.value.special || (isSpecial.value && item.value.stock_left == 0))
    );
});

const itemEventState = computed<"will be" | "is" | "was" | null>(() => {
    if (!itemLoaded || item.value.event === null) return null;

    let now = new Date();
    let startDate = new Date(item.value.event.start_date);
    let endDate = new Date(item.value.event.end_date);
    let hasStartTime = item.value.event.start_date !== null;
    let hasEndTime = item.value.event.end_date !== null;

    if (!hasStartTime) return "will be";

    if (startDate < now && (endDate > now || !hasEndTime)) return "is";

    if (startDate > now) return "will be";

    if (endDate < now) return "was";

    return null;
});

const lowestReseller = ref<number>();
function newLowestReseller(price: number) {
    lowestReseller.value = price;
}

function clickLowestResller() {
    document.getElementById("lowestReseller")?.click();
}

function scrollLowestReseller() {
    document
        .getElementById("lowestReseller")
        ?.scrollIntoView({ behavior: "smooth", block: "center" });
}

function loadItem() {
    axios
        .get<{ data: Item }>(BH.apiUrl(`v1/shop/${props.itemId}`))
        .then(({ data }) => {
            item.value = data.data;
            itemLoaded.value = true;

            // should redirect if its not public and they arent admin so no need to redirect here
            if (!item.value.is_public) {
                notificationStore.setNotification(
                    "This item is not public and only viewable by admins.",
                    "warning"
                );
            }

            updateUserPermission.id = item.value.creator.id;
            permissionStore.loadCan(
                updateItemPermission,
                updateUserPermission,
                "scrub items",
                "accept items"
            );

            if (item.value.special || item.value.special_edition)
                loadSpecialData();
        });
}

function loadSpecialData() {
    axios
        .get<{ data: SpecialData }>(
            BH.apiUrl(`v1/shop/${props.itemId}/specialData`)
        )
        .then(({ data }) => {
            itemSpecialData.value = data.data;
        });
}
</script>
