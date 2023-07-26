<template>
    <div class="new-theme">
        <div class="header">Avatar Editor</div>
        <div class="col-1-3" style="position: relative">
            <Painter
                v-show="painterActive"
                :avatar-data="avatarData"
                @modify-colors="newModification"
            />
            <div class="preview-holder">
                <div
                    v-show="
                        avatarLoading ||
                        thumbnailStore
                            .getThumbnail(thumbnailData)
                            .includes('pending')
                    "
                    class="preview-img"
                    style="padding-bottom: calc(100% - 20px)"
                >
                    <div class="loader"></div>
                </div>
                <img
                    v-show="
                        !avatarLoading &&
                        !thumbnailStore
                            .getThumbnail(thumbnailData)
                            .includes('pending')
                    "
                    class="preview-img"
                    :src="thumbnailStore.getThumbnail(thumbnailData)"
                />

                <template v-if="avatarData.items?.length > 0">
                    <Clothing
                        :clothing="avatarData.raw_items?.clothing ?? []"
                        :items="avatarData.items"
                        @reorder-clothing="reorderClothing"
                    />
                </template>
                <!--
                <button class="blue threedee-button">3D</button>
                -->
            </div>
            <div class="avatar-buttons text-center">
                <div class="inline" @click="rebaseAvatar">
                    <button class="clear thin">
                        <SvgSprite
                            class="svg-icon-large svg-black"
                            square="24"
                            svg="user/customize/avatar_reset.svg"
                        />
                    </button>
                    <div class="button-hint">RESET</div>
                </div>
                <div class="inline" @click="undo">
                    <button class="blue thin">
                        <SvgSprite
                            class="svg-icon-large"
                            square="24"
                            svg="user/customize/avatar_arrow_undo.svg"
                        />
                    </button>
                    <div class="button-hint">UNDO</div>
                </div>
                <div class="inline" @click="redo">
                    <button class="blue thin">
                        <SvgSprite
                            class="svg-icon-large"
                            square="24"
                            svg="user/customize/avatar_arrow_redo.svg"
                        />
                    </button>
                    <div class="button-hint">REDO</div>
                </div>
                <div class="inline" @click="outfitActive = !outfitActive">
                    <button class="blue thin">
                        <SvgSprite
                            class="svg-icon-large"
                            square="24"
                            svg="user/customize/avatar_save.svg"
                        />
                    </button>
                    <div class="button-hint">SAVE</div>
                </div>
                <div class="inline" @click="painterActive = !painterActive">
                    <button class="yellow thin">
                        <SvgSprite
                            class="svg-icon-large"
                            square="24"
                            svg="user/customize/avatar_paint.svg"
                        />
                    </button>
                    <div class="button-hint">PAINT</div>
                </div>
            </div>
            <div
                v-if="outfitActive"
                style="margin-top: 10px; z-index: 11; position: relative"
            >
                <div class="col-7-12">
                    <input
                        v-model="outfitName"
                        class="width-100"
                        placeholder="Outfit Name"
                    />
                </div>
                <div class="col-5-12">
                    <button class="blue width-100" @click="saveOutfit">
                        SAVE OUTFIT
                    </button>
                </div>
            </div>
        </div>
        <div class="col-2-3">
            <Tabs :newTheme="true">
                <Tab name="Accessories" @selected="lazyLoad.accessories = true">
                    <InventoryTab
                        :categories="[
                            { value: 'hat', name: 'Hats' },
                            { value: 'face', name: 'Faces' },
                            { value: 'tool', name: 'Tools' },
                        ]"
                        :wearing="wearingIDs"
                        :allow-load="lazyLoad.accessories"
                        @toggle-item="toggleItem"
                    />
                </Tab>
                <Tab name="Clothing" @selected="lazyLoad.clothing = true">
                    <InventoryTab
                        :categories="[
                            { value: 'shirt', name: 'Shirts' },
                            { value: 'pants', name: 'Pants' },
                            { value: 'tshirt', name: 'T-Shirts' },
                        ]"
                        :wearing="wearingIDs"
                        :allow-load="lazyLoad.clothing"
                        @toggle-item="toggleItem"
                    />
                </Tab>
                <Tab name="Body" @selected="lazyLoad.body = true">
                    <InventoryTab
                        :categories="[{ value: 'head', name: 'Heads' }]"
                        :wearing="wearingIDs"
                        :allow-load="lazyLoad.body"
                        @toggle-item="toggleItem"
                    />
                </Tab>
                <Tab name="Outfits" @selected="lazyLoad.outfits = true">
                    <OutfitTab
                        ref="outfitTab"
                        :allow-load="lazyLoad.outfits"
                        @select-outfit="selectOutfit"
                    />
                </Tab>
            </Tabs>
            <div>
                <div class="header-3">Currently Wearing</div>
                <div class="text-center">
                    <div v-if="avatarData.items?.length == 0">No items</div>
                    <div
                        v-else
                        v-for="item in avatarData.items"
                        class="item-card pointer active"
                        @click="toggleItem(item.id, item.type_id)"
                    >
                        <img
                            :src="
                                thumbnailStore.getThumbnail({
                                    id: item.id,
                                    type: ThumbnailType.ItemFull,
                                })
                            "
                        />
                        <div class="item-title small-text ellipsis">
                            <a
                                @click.stop
                                target="_blank"
                                :href="`/shop/${item.id}`"
                            >
                                {{ item.name }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.threedee-button {
    position: absolute;
    right: 10px;
    bottom: 10px;
    font-size: 20px;
    padding: 7px 15px;
}
.avatar-buttons {
    position: relative;
    z-index: 11;
    div:not(:last-child) {
        button,
        .button-hint {
            margin-right: 9.9px;
        }
    }

    .button-hint {
        font-weight: 600;
        margin-top: 5px;
    }

    svg {
        height: 24px;
    }
}
.preview-holder {
    position: relative;
    margin-bottom: 10px;
    z-index: 11;
}
.preview-img {
    border-radius: 2px;
    border: 1px solid;
    padding: 20px;
    width: 100%;
    margin-bottom: -4px;

    @include themify() {
        border-color: themed("inputs", "blend-border");
        background: themed("media", "gradient");
    }
}
</style>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from "vue";
import axios from "axios";

import { axiosSendErrorToNotification } from "@/logic/notifications";
import {
    Modification,
    UndoState,
    useModifications,
} from "./modification_handler";
import { BH } from "@/logic/bh";

import Tabs from "../../global/tabs/Tabs.vue";
import Tab from "../../global/tabs/Tab.vue";
import InventoryTab from "./InventoryTab.vue";
import OutfitTab from "./OutfitTab.vue";
import Painter from "./Painter.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";
import Clothing from "./Clothing.vue";
import { SortableEvent } from "sortablejs";

const lazyLoad = reactive({
    accessories: false,
    clothing: false,
    body: false,
    outfits: false,
});

const thumbnailData = {
    // user will always exist to create this component
    // returning from this function if user doesnt exist creates typescript errors
    // idk how to work around that
    id: BH.user?.id ?? 1,
    type: ThumbnailType.AvatarFull,
};

const {
    newModification,
    updateWearing,
    redo,
    undo,
    avatarData,
    avatarLoading,
} = useModifications(thumbnailData);

const outfitName = ref<string>("");
const outfitTab = ref<InstanceType<typeof OutfitTab>>();

// TODO: use modal code to make it close when clicking out of the menu?
const painterActive = ref<boolean>(false);
const outfitActive = ref<boolean>(false);

function currentAvatarToInstruction() {
    let modifications: Modification[] = [];

    for (let part in avatarData.value.colors) {
        modifications.push({
            type: part,
            value: avatarData.value.colors[part],
        });
    }

    for (let id of wearingIDs.value) {
        modifications.push({
            type: "wear",
            value: id,
        });
    }

    return modifications;
}

function saveOutfit() {
    axios
        .post(BH.apiUrl(`v1/user/outfits/create`), {
            name: outfitName.value,
        })
        .then(() => {
            outfitTab.value?.crateAPI.refreshAPI();
            window.location.hash = "#outfits";
        })
        .catch(axiosSendErrorToNotification);
}

function selectOutfit(outfitId: number) {
    let modifications: UndoState = {
        undo: [],
        redo: [],
    };

    modifications.undo = currentAvatarToInstruction();
    modifications.undo.push({
        type: "rebase",
        value: true,
    });

    modifications.redo.push({
        type: "wearOutfit",
        value: outfitId,
    });

    newModification(modifications);
}

function rebaseAvatar() {
    let modifications: UndoState = {
        undo: [],
        redo: [],
    };

    modifications.undo = currentAvatarToInstruction();
    modifications.redo.push({
        type: "rebase",
        value: true,
    });

    newModification(modifications);
}

// TODO: having to manage all the types adds a lot of unnecessary work
// TODO: probably best to refactor the avatar object to simply just store an array of itemIds
// TODO: item data is already stored in another object anyway so the client/renderer doesnt need to know the types of what you are wearing
const typeIdToKey: { [key: number]: string } = {
    1: "hats",
    2: "face",
    3: "tool",
    4: "head",
    5: "figure",
    6: "tshirt",
    7: "shirt",
    8: "pants",
};

function toggleItem(itemId: number, typeId: number) {
    let modifications: UndoState = {
        undo: [],
        redo: [],
    };

    let isWearing = wearingIDs.value.includes(itemId);
    if (isWearing) {
        toggleModifications(modifications, "wear", itemId, "remove", itemId);
    } else {
        let replacementId = avatarData.value.raw_items[typeIdToKey[typeId]];
        if (typeof replacementId === "object") {
            // either replace the first element with 0, or the first element
            let index = Math.max(replacementId.indexOf(0), 0);
            replacementId = replacementId[index];

            if (replacementId == 0) {
                modifications = toggleModifications(
                    modifications,
                    "remove",
                    itemId,
                    "wear",
                    itemId
                );
            } else {
                modifications = toggleModifications(
                    modifications,
                    "remove",
                    itemId,
                    "wear",
                    replacementId
                );

                modifications = toggleModifications(
                    modifications,
                    "remove",
                    replacementId,
                    "wear",
                    itemId
                );
            }
        } else {
            if (replacementId == 0) {
                modifications = toggleModifications(
                    modifications,
                    "remove",
                    itemId,
                    "wear",
                    itemId
                );
            } else {
                modifications = toggleModifications(
                    modifications,
                    "wear",
                    replacementId,
                    "wear",
                    itemId
                );
            }
        }
    }

    newModification(modifications);
}

function toggleModifications(
    modifications: UndoState,
    undoType: string,
    undoId: number,
    redoType: string,
    redoId: number
): UndoState {
    modifications.undo.push({
        type: undoType,
        value: undoId,
    });

    modifications.redo.push({
        type: redoType,
        value: redoId,
    });

    return modifications;
}

function reorderClothing(arr: string[]) {
    let sorted = arr.map(Number);

    let modifications: UndoState = {
        undo: [],
        redo: [],
    };

    modifications.undo.push({
        type: "reorderClothing",
        value: avatarData.value.raw_items?.clothing ?? [],
    });

    modifications.redo.push({
        type: "reorderClothing",
        value: sorted,
    });

    newModification(modifications);
}

onMounted(async () => {
    updateWearing();
});

const wearingIDs = computed<number[]>((): number[] => {
    return avatarData.value.items?.map((item: any) => item.id) ?? [];
});
</script>
