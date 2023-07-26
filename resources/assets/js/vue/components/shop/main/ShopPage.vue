<template>
    <div class="new-theme">
        <div class="header">
            {{ t("header") }}
        </div>
        <div class="col-1-1 mobile-col-1-1 mb-20">
            <div class="col-1-2 mobile-col-1-1">
                <input
                    type="text"
                    class="width-100 blend"
                    placeholder="Search"
                    v-model="search"
                    @input="debounceSearch"
                />
            </div>
            <div class="col-1-4 mobile-col-1-1">
                <select class="width-100 blend" v-model="sortBy">
                    <option value="updated">Recently Updated</option>
                    <option value="newest">Newest</option>
                    <option value="oldest">Oldest</option>
                    <option value="expensive">Most Expensive</option>
                    <option value="inexpensive">Least Expensive</option>
                </select>
            </div>
            <div class="col-1-8 mobile-col-1-2 mobile-pad">
                <a href="/shop/create" class="button width-100 blue">
                    {{ t("create") }}
                </a>
            </div>
            <div class="col-1-8 mobile-col-1-2 mobile-pad">
                <a href="/customize" class="button width-100 yellow">
                    {{ t("wardrobe") }}
                </a>
            </div>
        </div>
        <div class="col-1-1">
            <div class="col-1-5 border-right swap-padding">
                <div
                    class="header-3 light-text mb-20"
                    @click="showFilters = !showFilters"
                >
                    <SvgSprite
                        :class="{ rotated: !showFilters }"
                        class="svg-white svg-rotate mobile-only"
                        style="height: 20px"
                        width="12"
                        height="12"
                        svg="ui/dropdown_arrow.svg"
                    />
                    FILTER
                </div>
                <div
                    class="height-transition transition-mobile-only"
                    :class="{ open: showFilters }"
                >
                    <!-- TODO: designers -->
                    <div v-if="false" class="mb-20">
                        <input
                            type="checkbox"
                            v-model="onlyVerifiedDesigners"
                            id="verified-designers"
                        />
                        <label for="verified-designers">
                            {{ t("filters.verified_designers") }}
                        </label>
                    </div>
                    <div class="mb-20">
                        <input
                            type="checkbox"
                            v-model="showUnavailableItems"
                            id="unavailable-items"
                        />
                        <label for="unavailable-items">
                            {{ t("filters.unavailable_items") }}
                        </label>
                    </div>
                    <div class="mb-20">
                        <input
                            type="checkbox"
                            v-model="onlySpecialItems"
                            id="special-items"
                        />
                        <label for="special-items">
                            {{ t("filters.special_only") }}
                        </label>
                    </div>
                    <div class="mb-20">
                        <input
                            type="checkbox"
                            v-model="onlyEventItems"
                            id="event-items"
                        />
                        <label for="event-items">
                            {{ t("filters.event_only") }}
                        </label>
                    </div>
                    <div
                        class="mb-20"
                        style="padding-right: 20px; height: 80px"
                    >
                        <div class="mb-10">Price</div>
                        <div class="mb-10">
                            <InputRange
                                :min="0"
                                :max="BH.user?.bucks ?? 100"
                                v-model:min-input="minPriceInput"
                                v-model:max-input="maxPriceInput"
                            />
                        </div>
                        <div class="col-1-2 mobile-col-1-2 no-pad">
                            <input
                                type="number"
                                class="thin blend col-10-12 mobile-col-9-12"
                                style="margin-right: 0; font-size: 0.7rem"
                                placeholder="0 bucks"
                                v-model.number="minPriceInput"
                            />
                        </div>
                        <div class="col-1-2 mobile-col-1-2">
                            <input
                                type="number"
                                class="thin blend col-10-12 mobile-col-9-12"
                                style="
                                    float: right;
                                    margin-right: 0;
                                    font-size: 0.7rem;
                                "
                                placeholder="Any"
                                v-model.number="maxPriceInput"
                            />
                        </div>
                    </div>
                    <!-- TODO: implement tags -->
                    <div v-if="false" style="padding-right: 20px">
                        <div class="mb-20">
                            <div class="mb-10">
                                <SvgSprite
                                    class="svg-white svg-icon-medium-text"
                                    square="16"
                                    svg="shop/main/tag.svg"
                                />
                                Tags
                            </div>
                            <TagSelector
                                :max-tag-display="2"
                                v-model:selected-tags="selectedTags"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4-5 swap-padding">
                <Tabs :new-theme="true" :small-text="true">
                    <template v-slot:all>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/all.svg"
                        />
                        <span>{{ t("tabs.all") }}</span>
                    </template>
                    <template v-slot:accessories>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/accessories.svg"
                        />
                        <span>{{ t("tabs.accessories") }}</span>
                    </template>
                    <template v-slot:clothing>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/clothing.svg"
                        />
                        <span>{{ t("tabs.clothing") }}</span>
                    </template>
                    <template v-slot:bodyparts>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/body_parts.svg"
                        />
                        <span>{{ t("tabs.body_parts") }}</span>
                    </template>
                    <template v-slot:emotes>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/emotes.svg"
                        />
                        <span>{{ t("tabs.emotes") }}</span>
                    </template>
                    <template v-slot:outfits>
                        <SvgSprite
                            class="svg-white svg-icon-medium-text svg-icon-margin"
                            square="20"
                            svg="shop/types/outfits.svg"
                        />
                        <span>{{ t("tabs.outfits") }}</span>
                    </template>
                    <Tab
                        name="All"
                        @selection-state="(state: boolean) => lazyLoad.all = state"
                    >
                        <ShopTab
                            :selected="lazyLoad.all"
                            :categories="[]"
                            :additional-params="additionalParams"
                        />
                    </Tab>
                    <Tab
                        name="Accessories"
                        @selection-state="(state: boolean) => lazyLoad.accessories = state"
                    >
                        <ShopTab
                            :selected="lazyLoad.accessories"
                            :categories="[
                                { value: 'hat', name: 'Hats' },
                                { value: 'face', name: 'Faces' },
                                { value: 'tool', name: 'Tools' },
                            ]"
                            :additional-params="additionalParams"
                        />
                    </Tab>
                    <Tab
                        name="Clothing"
                        @selection-state="(state: boolean) => lazyLoad.clothing = state"
                    >
                        <ShopTab
                            :selected="lazyLoad.clothing"
                            :categories="[
                                { value: 'shirt', name: 'Shirts' },
                                { value: 'tshirt', name: 'T-Shirts' },
                                { value: 'pants', name: 'Pants' },
                            ]"
                            :additional-params="additionalParams"
                        />
                    </Tab>
                    <Tab
                        name="Body Parts"
                        @selection-state="(state: boolean) => lazyLoad.bodyParts = state"
                    >
                        <ShopTab
                            :selected="lazyLoad.bodyParts"
                            :categories="[{ value: 'head', name: 'Heads' }]"
                            :additional-params="additionalParams"
                        />
                    </Tab>
                    <!--
                    <Tab name="Emotes" @selected="lazyLoad.emotes = true"> </Tab>
                    <Tab name="Outfits" @selected="lazyLoad.outfits = true"> </Tab>
                    -->
                </Tabs>
            </div>
        </div>
    </div>
</template>

<i18n locale="en" lang="json">
{
    "header": "Avatar Store",
    "create": "Create",
    "wardrobe": "Wardrobe",
    "filters": {
        "verified_designers": "Verified designers only",
        "unavailable_items": "Show unavailable items",
        "special_only": "Special items only",
        "event_only": "Event items only"
    },
    "tabs": {
        "all": "All",
        "accessories": "Accessories",
        "clothing": "Clothing",
        "body_parts": "Body Parts",
        "emotes": "Emotes",
        "outfits": "Outfits"
    }
}
</i18n>

<style lang="scss"></style>

<script setup lang="ts">
import { reactive, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { BH } from "@/logic/bh";
import createDebounce from "@/logic/debounce";
import Tabs from "components/global/tabs/Tabs.vue";
import Tab from "components/global/tabs/Tab.vue";
import InputRange from "./InputRange.vue";
import TagSelector from "../shared/TagSelector.vue";
import ShopTab from "./ShopTab.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";

const { t } = useI18n();

const showFilters = ref<boolean>(false);

const onlyVerifiedDesigners = ref<boolean>(false);
const showUnavailableItems = ref<boolean>(true);
const onlySpecialItems = ref<boolean>(false);
const onlyEventItems = ref<boolean>(false);

const minPriceInput = ref<number>();
const maxPriceInput = ref<number>();
const debouncedMinPrice = ref<number>();
const debouncedMaxPrice = ref<number>();

const sortBy = ref<string>("updated");
const selectedTags = ref<string[]>([]);
const search = ref<string>("");
const debouncedSelectedTags = ref<string[]>();
const debouncedSearch = ref<string>("");

const searchDebounce = createDebounce();
const selectedTagsDebounce = createDebounce();
const minPriceDebounce = createDebounce();
const maxPriceDebounce = createDebounce();

function debounceSearch() {
    searchDebounce(() => {
        debouncedSearch.value = search.value;
    }, 300);
}

function debounceSelectedTags() {
    selectedTagsDebounce(() => {
        debouncedSelectedTags.value = [...selectedTags.value];
    }, 500);
}

function debounceMinPrice() {
    minPriceDebounce(() => {
        debouncedMinPrice.value = minPriceInput.value;
    }, 300);
}

function debounceMaxPrice() {
    maxPriceDebounce(() => {
        debouncedMaxPrice.value = maxPriceInput.value;
    }, 300);
}

watch(selectedTags, debounceSelectedTags, { deep: true });
watch(minPriceInput, debounceMinPrice);
watch(maxPriceInput, debounceMaxPrice);

const lazyLoad = reactive({
    all: false,
    accessories: false,
    clothing: false,
    bodyParts: false,
    emotes: false,
    outfits: false,
});

const additionalParamsArray = [
    { key: "search", value: debouncedSearch },
    { key: "verified_designers_only", value: onlyVerifiedDesigners },
    { key: "show_unavailable", value: showUnavailableItems },
    { key: "special_only", value: onlySpecialItems },
    { key: "event_only", value: onlyEventItems },
    { key: "min_bucks_price", value: minPriceInput },
    { key: "max_bucks_price", value: maxPriceInput },
    { key: "tags[]", value: debouncedSelectedTags },
    { key: "sort", value: sortBy },
];

const additionalParams = ref<{ key: string; value: any }[]>([]);

watch(
    [
        sortBy,
        debouncedSearch,
        debouncedSelectedTags,
        onlyVerifiedDesigners,
        showUnavailableItems,
        onlySpecialItems,
        onlyEventItems,
        debouncedMinPrice,
        debouncedMaxPrice,
    ],
    () => {
        let params = [];
        for (let additionalParam of additionalParamsArray) {
            if (typeof additionalParam.value.value !== "undefined") {
                if (Array.isArray(additionalParam.value.value)) {
                    for (let val of additionalParam.value.value) {
                        params.push({
                            key: additionalParam.key,
                            value: val,
                        });
                    }
                } else {
                    params.push({
                        key: additionalParam.key,
                        value: additionalParam.value.value,
                    });
                }
            }
        }

        additionalParams.value = params;
    },
    { immediate: true }
);
</script>
