<template>
    <input
        class="width-100 thin blend"
        v-model="search"
        @input="debounceSearch"
        placeholder="Search"
    />
    <div style="padding-top: 10px" class="text-center">
        <div v-if="outfits.length == 0">No outfits</div>
        <div
            v-for="outfit in outfits"
            :key="outfit.id"
            class="item-card pointer"
            @click="emit('selectOutfit', outfit.id)"
        >
            <Dropdown style="position: absolute; right: 5px; top: 5px">
                <ul>
                    <li>
                        <div @click="renameOutfitModal(outfit.id)">Rename</div>
                    </li>
                    <li>
                        <div @click="updateOutfitModal(outfit.id)">Update</div>
                    </li>
                    <li>
                        <div @click="deleteOutfitModal(outfit.id)">Delete</div>
                    </li>
                </ul>
            </Dropdown>
            <img
                :src="
                    thumbnailStore.getThumbnail({
                        id: outfit.id,
                        type: ThumbnailType.OutfitFull,
                    })
                "
            />
            <div class="item-title small-text ellipsis">{{ outfit.name }}</div>
        </div>
        <div class="unselectable" style="margin: 10px 0">
            <SvgSprite
                v-show="pageNumber > 1"
                class="svg-icon-small svg-white pointer"
                square="12"
                svg="ui/arrow_left.svg"
                @click="crateAPI.loadPage(pageNumber - 1)"
            />
            <span style="padding: 0 20px">Page {{ pageNumber }}</span>
            <SvgSprite
                v-show="crateAPI.hasNextPage()"
                class="svg-icon-small svg-white pointer"
                square="12"
                svg="ui/arrow_right.svg"
                @click="crateAPI.loadPage(pageNumber + 1)"
            />
        </div>
    </div>

    <Teleport to="body">
        <Modal
            title="Rename Outfit"
            v-show="renameModal"
            @close="renameModal = false"
        >
            <input
                class="width-100"
                placeholder="Outfit Name"
                v-model="renameValue"
            />
            <div class="modal-buttons">
                <button
                    class="green"
                    style="margin-right: 10px"
                    @click="renameOutfit"
                >
                    Save
                </button>
                <button class="cancel-button" @click="renameModal = false">
                    Cancel
                </button>
            </div>
        </Modal>

        <Modal
            title="Update Outfit"
            v-show="updateModal"
            @close="updateModal = false"
        >
            Are you sure you want to update this outfit? This will replace the
            outfit with your current avatar.
            <div class="modal-buttons">
                <button
                    class="green"
                    style="margin-right: 10px"
                    @click="updateOutfit"
                >
                    Update Outfit
                </button>
                <button class="cancel-button" @click="updateModal = false">
                    Cancel
                </button>
            </div>
        </Modal>

        <Modal
            title="Delete Outfit"
            v-show="deleteModal"
            @close="deleteModal = false"
        >
            Are you sure you want to delete this outfit? This cannot be undone.
            <div class="modal-buttons">
                <button
                    class="red"
                    style="margin-right: 10px"
                    @click="deleteOutfit"
                >
                    Delete Outfit
                </button>
                <button class="cancel-button" @click="deleteModal = false">
                    Cancel
                </button>
            </div>
        </Modal>
    </Teleport>
</template>

<style lang="scss">
.item-card {
    height: 128px;
    width: 128px;
}
</style>

<script setup lang="ts">
import createDebounce from "@/logic/debounce";
import CursorableAPI from "@/logic/apis/CursorableAPI";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import { BH } from "@/logic/bh";

import { ref, toRef, watchEffect } from "vue";
import axios from "axios";

import Dropdown from "@/components/global/Dropdown.vue";
import Modal from "@/components/global/Modal.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const debounce = createDebounce();

interface Outfit {
    name: string;
    id: number;
    hash: string;
}

const crateAPI = new CursorableAPI<Outfit>(`v2/user/outfits`);
const { currentData: outfits, pageNumber } = crateAPI;

const props = defineProps<{
    allowLoad: boolean;
}>();

defineExpose({
    crateAPI,
});

const emit = defineEmits(["selectOutfit"]);

const selectedOutfit = ref<number>(0);
const renameModal = ref<boolean>(false);
const renameValue = ref<string>("");
const updateModal = ref<boolean>(false);
const deleteModal = ref<boolean>(false);

function renameOutfitModal(id: number) {
    selectedOutfit.value = id;
    renameValue.value = "";
    renameModal.value = true;
}

function updateOutfitModal(id: number) {
    selectedOutfit.value = id;
    updateModal.value = true;
}

function deleteOutfitModal(id: number) {
    selectedOutfit.value = id;
    deleteModal.value = true;
}

async function renameOutfit() {
    await axios
        .post(BH.apiUrl(`v1/user/outfits/${selectedOutfit.value}/rename`), {
            name: renameValue.value,
        })
        .then(() => {
            crateAPI.refreshAPI();
            successToNotification("Outfit renamed");
        })
        .catch(axiosSendErrorToNotification);

    renameModal.value = false;
}

async function updateOutfit() {
    await axios
        .post(BH.apiUrl(`v1/user/outfits/${selectedOutfit.value}/change`))
        .then(() => {
            crateAPI.refreshAPI();
            successToNotification("Outfit updated");
        })
        .catch(axiosSendErrorToNotification);

    updateModal.value = false;
}

async function deleteOutfit() {
    await axios
        .post(BH.apiUrl(`v1/user/outfits/${selectedOutfit.value}/delete`))
        .then(() => {
            crateAPI.refreshAPI();
            successToNotification("Outfit deleted");
        })
        .catch(axiosSendErrorToNotification);

    deleteModal.value = false;
}

const search = ref<string>("");
const debouncedSearch = ref<string>("");

function debounceSearch() {
    debounce(() => {
        debouncedSearch.value = search.value;
    }, 300);
}

const allowLoad = toRef(props, "allowLoad");

watchEffect(() => {
    if (!allowLoad.value) return;

    let params = [];
    params.push({ key: "search", value: debouncedSearch.value });
    crateAPI.setParams(params);
});
</script>
