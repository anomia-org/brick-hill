<template>
    <div class="new-theme">
        <div class="header mb-10">GAMES</div>
        <div class="col-1-6 border-right light-text" style="padding-right: 0">
            <div class="sorts mobile-col-1-2" style="padding-right: 0">
                <div class="sorts-title">Sort By</div>
                <div class="sorts-option" v-for="sort in sorts" :key="sort">
                    <input
                        class="no-input-display"
                        type="radio"
                        :id="sort"
                        v-model="selectedSort"
                        :value="sort"
                    />
                    <label :for="sort">{{ sort }}</label>
                </div>
            </div>
            <div class="sorts mobile-col-1-2" style="padding-right: 0">
                <div class="sorts-title">Genre</div>
                <div class="sorts-option" v-for="genre in genres" :key="genre">
                    <input
                        type="checkbox"
                        :id="genre"
                        v-model="selectedGenres"
                        :value="genre"
                    />
                    <label :for="genre">{{ genre }}</label>
                </div>
            </div>
        </div>
        <div class="col-10-12" style="padding-left: 20px">
            <div class="play-searches">
                <div class="col-1-2 mobile-col-1-2">
                    <input
                        class="thin blend"
                        v-model="search"
                        @input="debounceSearch"
                        type="text"
                        placeholder="Search"
                        id="search-bar"
                    />
                </div>
                <div class="col-1-2 mobile-col-1-2 play-selects">
                    <select
                        v-model="direction"
                        class="blend"
                        style="margin-right: 20px"
                    >
                        <option value="desc">Descending</option>
                        <option value="asc">Ascending</option>
                    </select>
                    <!--
                    Aggregated sorts by day/week/month too complicated for BH at this time
                    <select style="margin-right:20px;">
                        <option>Today</option>
                    </select>
                    -->
                    <a
                        href="/play/create"
                        class="button blue no-mobile no-overflow"
                    >
                        Create
                    </a>
                </div>
            </div>
            <div class="play-sets">
                <div
                    class="col-1-1"
                    style="padding-right: 0"
                    v-for="(column, i) in setColumns"
                    :key="i"
                >
                    <div
                        class="col-1-4 mobile-col-1-2"
                        v-for="set in column"
                        :key="set.id"
                    >
                        <a :href="`/play/${set.id}`">
                            <img style="width: 100%" :src="set.thumbnail" />
                            <div
                                style="height: 24px"
                                class="smedium-text bold ellipsis"
                            >
                                {{ set.name }}
                            </div>
                            <div class="smaller-text gray-text ellipsis">
                                By {{ set.creator.username }}
                            </div>
                            <div class="small-text red-text bold">
                                {{ filterNumberCompact(set.playing) }} playing
                            </div>
                        </a>
                    </div>
                </div>
                <div
                    class="col-1-1 text-center"
                    v-if="
                        selectedSort == 'featured' && !setListAPI.hasNextPage()
                    "
                >
                    <button class="clear" @click="swapToPopular">
                        View More
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.play-sets .col-1-4 {
    padding-right: 20px;
    margin-bottom: 25px;
}
.sorts {
    text-transform: uppercase;
    margin-bottom: 50px;

    .sorts-title {
        font-weight: 700;
        font-size: 20px;
    }

    .sorts-option {
        font-weight: 600;
        padding: 7px;
    }
}

input:checked + label {
    @include themify($themes) {
        color: themed("dark");
    }
}

.play-searches {
    margin-bottom: 55px;
}

.play-selects {
    text-align: right;
    padding-right: 20px;
}
</style>

<script setup lang="ts">
import { filterNumberCompact } from "@/filters/index";
import { computed, onMounted, ref, watch } from "vue";
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import { BH } from "@/logic/bh";
import createDebounce from "@/logic/debounce";
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";

const direction = ref<string>("desc");
const sorts = ref<string[]>(["featured", "most popular"]);
const selectedSort = ref<string>("featured");
const genres = ref<string[]>([
    "adventure",
    "roleplay",
    "action",
    "simulation",
    "showcase",
    "minigame",
    "platformer",
]);
const selectedGenres = ref<string[]>([]);
const search = ref<string>("");

if (BH.user) sorts.value.push("recently played");

const setListAPI = new InfiniteScrollAPI<any>(`v1/sets/list`, 20, "", true);
const { currentData: sets } = setListAPI;

refreshData();

let clearScroll: () => void;

onMounted(() => {
    ({ clearScroll } = hasInfiniteScroll(() => {
        setListAPI.loadNextPage();
    }));
});

const debounce = createDebounce();

function debounceSearch() {
    debounce(() => {
        refreshData();
    }, 300);
}

function refreshData() {
    let params = [
        { key: "sort", value: selectedSort.value },
        { key: "direction", value: direction.value },
        { key: "search", value: search.value },
    ];
    for (let type of selectedGenres.value) {
        if (type == "all") continue;
        params.push({ key: "types[]", value: type });
    }
    setListAPI.setParams(params);
}

const setColumns = computed(() => {
    let cols: any[][] = [[]];
    for (let set of sets.value) {
        if (cols[cols.length - 1].length == 4) cols.push([]);

        cols[cols.length - 1].push(set);
    }

    return cols;
});

function swapToPopular() {
    selectedSort.value = "most popular";
    document
        .getElementById("search-bar")
        ?.scrollIntoView({ behavior: "smooth", block: "start" });
}

watch(
    selectedGenres,
    () => {
        refreshData();
    },
    { deep: true }
);

watch(direction, () => {
    refreshData();
});

watch(selectedSort, (newSort) => {
    if (newSort == "recently played") {
        setListAPI.setUrl("v1/sets/list/recent", true);
    } else {
        setListAPI.setUrl("v1/sets/list", true);
    }
    if (clearScroll) clearScroll();
    refreshData();
});
</script>
