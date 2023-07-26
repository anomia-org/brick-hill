<template>
    <div class="new-theme">
        <div class="header mb-10">{{ setName }}</div>
        <div class="col-1-1 play-content-box">
            <div class="col-1-2">
                <img class="game-thumbnail" :src="setThumbnail" />
            </div>
            <div class="col-1-2 play-content-box">
                <div class="col-1-2">
                    <div
                        class="col-1-1 mobile-col-1-1 mobile-text-center no-pad"
                    >
                        <div class="red-text bold mb-10 no-mobile">
                            {{ filterNumberCompact(set.playing || 0) }} playing
                            now
                        </div>
                        <button
                            v-if="setPlayable"
                            @click="playButton"
                            class="green mb-20-no-mobile play-button"
                        >
                            Play
                        </button>
                        <button
                            v-else
                            disabled
                            class="mb-20-no-mobile play-button"
                        >
                            Offline
                        </button>
                    </div>
                    <div class="mobile-col-1-1">
                        <div
                            class="col-1-2 mobile-col-1-2 split-box light-text"
                        >
                            <div>Genre</div>
                            <div>Visits</div>
                            <div>Created</div>
                            <div>Updated</div>
                        </div>
                        <div
                            v-if="setLoaded"
                            class="col-1-2 mobile-col-1-2 split-data"
                        >
                            <div>{{ setGenre }}</div>
                            <div>{{ filterNumber(set.visits) }}</div>
                            <div>{{ filterDate(set.created_at) }}</div>
                            <div>{{ filterDate(set.updated_at) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-1-2 no-mobile center-text">
                    <a v-if="setLoaded" :href="`/user/${set.creator.id}`">
                        <img
                            class="user-thumbnail"
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: set.creator.id,
                                    type: ThumbnailType.AvatarFull,
                                })
                            "
                        />
                        <span class="bold">{{ set.creator.username }}</span>
                    </a>
                </div>
            </div>
            <div class="col-1-2" style="padding: 0">
                <div class="col-1-2" style="padding-top: 5px">
                    <div class="unselectable" style="margin-bottom: 33px">
                        <div class="col-1-2 mobile-col-1-2">
                            <SvgSprite
                                v-if="hasRated && rating"
                                class="pointer svg-black svg-icon-large"
                                square="28"
                                svg="sets/thumbsup_full.svg"
                                @click="toggleRating(true)"
                            />
                            <SvgSprite
                                v-else
                                class="pointer svg-black svg-icon-large"
                                square="28"
                                svg="sets/thumbsup.svg"
                                @click="toggleRating(true)"
                            />
                            {{ filterNumberCompact(setUpvotes) }}
                        </div>
                        <div
                            class="col-1-2 mobile-col-1-2"
                            style="text-align: right"
                        >
                            <SvgSprite
                                v-if="hasRated && !rating"
                                class="pointer svg-black svg-icon-large"
                                square="28"
                                svg="sets/thumbsdown_full.svg"
                                @click="toggleRating(false)"
                            />
                            <SvgSprite
                                v-else
                                class="pointer svg-black svg-icon-large"
                                square="28"
                                svg="sets/thumbsdown.svg"
                                @click="toggleRating(false)"
                            />
                            {{ filterNumberCompact(setDownvotes) }}
                        </div>
                    </div>
                    <div class="ratings">
                        <div
                            class="red-ratings"
                            :style="{
                                width: `calc(${100 - ratingPercentage}% - 3px)`,
                                left: `calc(${ratingPercentage}% + 3px)`,
                            }"
                        ></div>
                        <div
                            class="green-ratings"
                            :style="{
                                width: `${ratingPercentage}%`,
                            }"
                        ></div>
                    </div>
                </div>
                <div class="col-1-2 more-data" style="margin-top: 10px">
                    <a
                        v-if="setLoaded"
                        class="mobile-only mobile-col-1-3 text-center ellipsis"
                        style="padding-top: 15px"
                        :href="`/user/${set.creator.id}`"
                    >
                        {{ set.creator.username }}
                    </a>
                    <div
                        class="col-1-2 mobile-col-1-3 center-text"
                        style="color: #fec200"
                    >
                        <favorite
                            :poly_id="setId"
                            :on_load_favorited="onLoadFavorited"
                            :on_load_favorites="onLoadFavorites"
                            :type="ModelRelation.Set"
                            :newTheme="true"
                        ></favorite>
                    </div>
                    <div class="col-1-2 mobile-col-1-3 center-text">
                        <div
                            class="pointer svg-black svg-icon-large"
                            style="padding-top: 4px"
                            id="set-dropdown"
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
            <hr />
        </div>
        <div
            v-if="setLoaded && (set.description || '').length > 0"
            class="col-1-1"
        >
            <div class="header-2 mb-10">Description</div>
            <p style="white-space: pre-line">{{ set.description }}</p>
        </div>

        <Tabs :newTheme="true">
            <Tab name="Comments">
                <Comments
                    :poly-id="setId"
                    :type="ModelRelation.Set"
                    :creator-id="set.creator?.id"
                ></Comments>
            </Tab>
            <!--
            <tab name="Store">
                <div class="smedium-text bold">Passes</div>
                <div class="col-1-2 pass-box">
                    <img src="https://brkcdn.com/v2/assets/af85e9b9-96f3-58f8-a815-4a090ec9e37b">
                    <div class="bold medium-text">VIP</div>
                    <div></div>
                    <button class="blue purchase"><span class="bucks-icon img-white"></span>100 Bucks</button>
                </div>
                <div class="col-1-2 pass-box">
                    <img src="https://brkcdn.com/v2/assets/af85e9b9-96f3-58f8-a815-4a090ec9e37b">
                    <div class="bold medium-text">VIP</div>
                    <div></div>
                    <button class="blue purchase"><span class="bucks-icon img-white"></span>100 Bucks</button>
                </div>
            </tab>
            -->
        </Tabs>

        <Dropdown class="dropdown" activator="set-dropdown">
            <ul v-if="setLoaded">
                <li v-if="BH.user?.id == set.creator.id">
                    <a :href="`/play/${setId}/edit`">Edit</a>
                </li>
                <li v-if="set.creator.id != BH.main_account_id">
                    <a :href="`/report/set/${setId}`">Report</a>
                </li>
                <li v-if="permissionStore.can('scrub sets')">
                    <a @click="scrubSetName">Scrub Name</a>
                </li>
                <li v-if="permissionStore.can('scrub sets')">
                    <a @click="scrubSetDesc">Scrub Description</a>
                </li>
                <li v-if="permissionStore.can('scrub sets')">
                    <a @click="toggleFeatured">Toggle Featured</a>
                </li>
                <li
                    v-if="
                        permissionStore.can('scrub sets') &&
                        set.thumbnail !== null &&
                        set.thumbnail.is_approved
                    "
                >
                    <a @click="scrubSetThumb">Scrub Thumbnail</a>
                </li>
            </ul>
        </Dropdown>
    </div>
</template>

<style lang="scss" scoped>
#set-dropdown {
    &:not(:hover) .more-full-svg {
        display: none;
    }

    &:hover .more-svg {
        display: none;
    }
}
.pass-box {
    border-radius: 5px;
    border: 2px solid;
    margin: 5px;
    padding: 10px;
    height: 160px;
    position: relative;

    @include themify($themes) {
        border-color: themed("inputs", "blend-border");
    }

    img {
        width: 140px;
        float: left;
        margin-right: 10px;
    }

    .purchase {
        position: absolute;
        right: 10px;
        bottom: 10px;

        span {
            padding-right: 24px;
        }
    }
}
@media only screen and (min-width: 767px) {
    .pass-box {
        width: calc(50% - 10px);
    }
}
@media handheld, only screen and (max-width: 767px) {
    .more-data {
        margin-bottom: 35px;
        div {
            padding-top: 10px;
        }
    }
}
.ratings {
    position: relative;

    div {
        position: absolute;
        height: 5px;
        border-radius: 1px;
    }

    .red-ratings {
        background-color: #dc0f18;
    }

    .green-ratings {
        background-color: #3ade55;
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
.play-content-box {
    padding-bottom: 8px;
    border-bottom: 2px solid;
    min-height: 211px;

    @include themify($themes) {
        border-color: themed("inputs", "blend-border");
    }
}
.play-button {
    font-size: 2em;
    width: 100%;
    padding: 12.5px;
}
.game-thumbnail {
    width: 100%;
}
.user-thumbnail {
    width: 100%;
    padding: 8px;
}
</style>

<script setup lang="ts">
import Comments from "../../polymorphic/Comments.vue";
import Favorite from "../../polymorphic/Favorite.vue";
import Dropdown from "../../global/Dropdown.vue";
import Tabs from "../../global/tabs/Tabs.vue";
import Tab from "../../global/tabs/Tab.vue";

import { filterDate, filterNumber, filterNumberCompact } from "@/filters/index";
import { permissionStore } from "@/store/modules/permission";
import ModelRelation from "@/logic/data/relations";
import { BH } from "@/logic/bh";
import axios from "axios";
import { computed, ref } from "vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import { notificationStore } from "@/store/modules/notifications";

const props = defineProps<{
    setId: number;
    setName: string;
    setThumbnail: string;

    setIp: string;
    setPort: string;
    setPlayable: string;

    onLoadFavorites: string;
    onLoadFavorited?: boolean;

    onLoadRated?: boolean;
    onLoadRating?: string;
    onLoadDownRatings: string;
    onLoadUpRatings: string;
}>();

permissionStore.loadCan("scrub sets");

loadSetData();

const setUpvotes = ref<number>(Number(props.onLoadUpRatings) ?? 0);
const setDownvotes = ref<number>(Number(props.onLoadDownRatings) ?? 0);
const hasRated = ref<boolean>(props.onLoadRated ?? false);
const rating = ref<boolean>(props.onLoadRating == "1");

if (
    props.onLoadRated &&
    props.onLoadUpRatings == "0" &&
    props.onLoadDownRatings == "0"
) {
    if (rating.value) {
        setUpvotes.value++;
    } else {
        setDownvotes.value++;
    }
}

const setLoaded = ref<boolean>(false);
const set = ref<any>({});

function loadSetData() {
    axios.get(BH.apiUrl(`v1/sets/${props.setId}`)).then(({ data }) => {
        setLoaded.value = true;
        set.value = data.data;
    });
}

function toggleRating(status: boolean) {
    if (!BH.user) return;

    // their vote hasnt been counted -- no need to change previous rating
    if (!hasRated.value) {
        if (status) {
            setUpvotes.value++;
        } else {
            setDownvotes.value++;
        }
    } else if (rating.value != status) {
        // they have rated -- we need to change their previous status to -1
        if (rating.value) {
            setUpvotes.value--;
        } else {
            setDownvotes.value--;
        }

        if (status) {
            setUpvotes.value++;
        } else {
            setDownvotes.value++;
        }
    } else {
        // their rating is the same, they want to unselect a vote so remove previous count
        if (status) {
            setUpvotes.value--;
        } else {
            setDownvotes.value--;
        }
    }

    if (hasRated.value && rating.value == status) {
        hasRated.value = false;
    } else {
        hasRated.value = true;
        rating.value = status;
    }

    axios.post(`/play/${props.setId}/rate`, {
        rating: rating.value,
        disabled: !hasRated.value,
    });
}

function playButton() {
    axios
        .get(BH.apiUrl(`v1/auth/generateToken?set=${props.setId}`))
        .then(({ data }) => {
            let ip = atob(props.setIp.split("").reverse().join(""));
            window.location.href = `brickhill.legacy://client/${data.token}/${ip}/${props.setPort}`;
        });
}

function scrubSetName() {
    axios
        .post(`/v1/admin/sets/${props.setId}/scrubName`)
        .then((req) => location.reload());
}

function scrubSetDesc() {
    axios
        .post(`/v1/admin/sets/${props.setId}/scrubDesc`)
        .then((req) => location.reload());
}

function toggleFeatured() {
    axios
        .post(`/v1/admin/sets/${props.setId}/toggleFeatured`, {
            toggle: !set.value.is_featured,
        })
        .then(() => {
            set.value.is_featured = !set.value.is_featured;
            if (set.value.is_featured) {
                notificationStore.setNotification(
                    "Set is now featured",
                    "success"
                );
            } else {
                notificationStore.setNotification(
                    "Set is now not featured",
                    "success"
                );
            }
        });
}

function scrubSetThumb() {
    axios
        .post(`/v1/admin/approve/asset/${set.value.thumbnail.id}`, {
            toggle: 0,
        })
        .then((req) => location.reload());
}

const setGenre = computed(() => {
    return set.value.genre?.type || "None";
});

const ratingPercentage = computed(() => {
    // account for cases where the division will be wacky
    if (setUpvotes.value == 0 && setDownvotes.value != 0) return 0;
    if (setUpvotes.value == 0) return 100;
    if (setDownvotes.value == 0) return 100;

    return Math.min(
        Math.floor(
            (setUpvotes.value / (setDownvotes.value + setUpvotes.value)) * 100
        ),
        100
    );
});
</script>
