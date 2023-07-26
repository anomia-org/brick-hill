<template>
    <div>
        <div class="col-1-3">
            <input
                v-model="searchItem"
                @keypress.enter="searchForItem"
                class="width-100"
                min="1"
                placeholder="Item ID"
                type="number"
            />
            <div
                class="text-center"
                style="margin-top: 10px"
                v-if="paginator.data.length == 0"
            >
                No items pending
            </div>
            <div
                v-show="paginator.data.length > 0"
                class="old-theme trade-picker"
            >
                <div v-for="(item, i) in paginator.data" :key="item.id">
                    <div
                        class="trade"
                        @click="selectItem(item)"
                        :class="{ selected: selectedItem == item }"
                    >
                        <div>
                            <img
                                class="trade-user-thumbnail"
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: item.id,
                                        type: ThumbnailType.ItemFull,
                                    })
                                "
                            />
                            <div style="padding-top: 20px">{{ item.name }}</div>
                        </div>
                    </div>
                    <hr v-if="i !== paginator.data.length - 1" />
                </div>
            </div>
        </div>
        <div v-if="!(typeof selectedItem.id === 'undefined')" class="card">
            <div class="top green">Update {{ originalItem.name }}</div>
            <div class="content">
                <div>Title:</div>
                <input
                    v-model="selectedItem.name"
                    class="mb2"
                    type="text"
                    name="title"
                    placeholder="My Item"
                    required
                />
                <div>Description:</div>
                <textarea
                    v-model="selectedItem.description"
                    name="description"
                    class="width-100 block mb2"
                    style="height: 80px"
                    placeholder="Brand new design!"
                ></textarea>

                <div class="mb2">
                    <span>Item Type:</span>
                    <select
                        v-model="selectedItem.type_id"
                        class="capitalize mb2"
                    >
                        <option value="1">hat</option>
                        <option value="2">face</option>
                        <option value="3">tool</option>
                        <option value="4">head</option>
                    </select>
                </div>

                <div class="mb2">
                    <span>Series ID:</span>
                    <input
                        v-model="selectedItem.series_id"
                        type="number"
                        name="series_id"
                        style="vertical-align: middle"
                    />
                </div>

                <div class="mb2">
                    <span>Event ID:</span>
                    <input
                        v-model="selectedItem.event.id"
                        type="number"
                        name="event_id"
                        style="vertical-align: middle"
                    />
                </div>

                <div v-if="!originalItem.special" class="mb3">
                    <span>Free:</span>
                    <input
                        v-model="virtual.free"
                        @change="virtual.offsale = false"
                        type="checkbox"
                        name="free"
                        style="vertical-align: middle"
                    />
                </div>

                <div v-if="!virtual.free">
                    <div class="mb3">
                        <span>Offsale:</span>
                        <input
                            v-model="virtual.offsale"
                            type="checkbox"
                            name="offsale"
                            style="vertical-align: middle"
                        />
                    </div>

                    <div
                        v-if="!virtual.offsale && !originalItem.special"
                        class="block mb2"
                    >
                        <div>Price:</div>
                        <span
                            class="bucks-icon"
                            style="vertical-align: middle; padding-right: 0"
                        ></span>
                        <input
                            v-model="selectedItem.bucks"
                            name="bucks"
                            min="1"
                            placeholder="0 bucks"
                            type="number"
                            style="width: 100px; margin-right: 5px"
                        />
                        <span
                            class="bits-icon"
                            style="vertical-align: middle; padding-right: 0"
                        ></span>
                        <input
                            v-model="selectedItem.bits"
                            name="bits"
                            min="1"
                            placeholder="0 bits"
                            type="number"
                            style="width: 100px"
                        />
                    </div>

                    <div v-if="originalItem.is_public" class="mb2">
                        <span>Special:</span>
                        <input
                            v-model="selectedItem.special"
                            :disabled="
                                originalItem.special > 0 ||
                                originalItem.special_edition > 0
                            "
                            type="checkbox"
                            name="offsale"
                            style="vertical-align: middle"
                        />
                    </div>

                    <div v-if="!originalItem.is_public" class="mb2">
                        <div class="mb2">
                            <span>Special Edition:</span>
                            <input
                                v-model="selectedItem.special_edition"
                                :disabled="originalItem.special_edition > 0"
                                type="checkbox"
                                name="offsale"
                                style="vertical-align: middle"
                            />
                        </div>
                        <div v-if="selectedItem.special_edition">
                            <div>Stock:</div>
                            <input
                                v-model="selectedItem.stock"
                                :disabled="originalItem.special_edition > 0"
                                name="stock"
                                min="1"
                                placeholder="0"
                                type="number"
                                style="width: 100px; margin-right: 5px"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="!selectedItem.special">
                    <div class="mb2">
                        <span>Timer:</span>
                        <input
                            v-model="selectedItem.timer"
                            :disabled="!!originalItem.special"
                            type="checkbox"
                            name="offsale"
                            style="vertical-align: middle"
                        />
                    </div>

                    <div v-if="selectedItem.timer" class="mb2">
                        <div>Based on UTC timezone</div>
                        <input
                            v-model="selectedItem.timer_date"
                            :min="new Date().toISOString().slice(0, -8)"
                            type="datetime-local"
                            style="vertical-align: middle"
                        />
                    </div>
                </div>

                <div class="mb2">
                    <span>Schedule for:</span>
                    <div>Based on UTC timezone</div>
                    <input
                        v-model="virtual.scheduled_for"
                        :min="new Date().toISOString().slice(0, -8)"
                        type="datetime-local"
                        style="vertical-align: middle"
                    />
                </div>

                <div class="mb2">
                    <span>Hide Update:</span>
                    <input
                        v-model="virtual.hide_update"
                        type="checkbox"
                        name="hide_update"
                        style="vertical-align: middle"
                    />
                </div>

                <input
                    class="button green upload-submit"
                    type="submit"
                    @click.prevent="submitData"
                    value="SAVE"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { reactive, ref } from "vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const searchItem = ref<string>();
const selectedItem = ref<any>({});
const originalItem = ref<any>({});
const paginator = reactive({
    cursor: "",
    data: [] as any,
});
const virtual = reactive({
    free: false,
    offsale: false,
    scheduled_for: "",
    hide_update: false,
});

let queryEdit = new URLSearchParams(window.location.search).get("editId");
if (queryEdit !== null) {
    searchItem.value = queryEdit;
    searchForItem();
}
load();

function searchForItem() {
    axios
        .get(BH.apiUrl(`v1/shop/${searchItem.value}`))
        .then(({ data }) => {
            if (data.data.creator.id != BH.main_account_id)
                return Promise.reject("You can only edit Brick Hill items");
            selectItem(data.data);
            return;
        })
        .catch(axiosSendErrorToNotification);
}

function load() {
    axios
        .get(`/v1/admin/shop/unscheduled`)
        .then(({ data }) => {
            paginator.data = data.data;
            if (
                paginator.data.length > 0 &&
                new URLSearchParams(window.location.search).get("editId") ===
                    null
            )
                selectItem(paginator.data[0]);
        })
        .catch(axiosSendErrorToNotification);
}

function selectItem(item: any) {
    selectedItem.value = item;
    if (selectedItem.value.event === null) {
        selectedItem.value.event = { id: null };
    }
    selectedItem.value.type_id = selectedItem.value.type.id;
    // dereference object so it doesnt copy over
    originalItem.value = JSON.parse(JSON.stringify(item));
    virtual.free =
        (selectedItem.value.bits === 0 || selectedItem.value.bucks === 0) &&
        !selectedItem.value.special;
    virtual.offsale =
        selectedItem.value.bits === null && selectedItem.value.bucks === null;
    virtual.scheduled_for = new Date(new Date().getTime() + 60 * 1000)
        .toISOString()
        .slice(0, -8);
}

function submitData() {
    axios
        .post(`/v1/admin/shop/scheduleItem`, {
            item: selectedItem.value,
            virtual: virtual,
        })
        .then(() => {
            location.reload();
        })
        .catch(axiosSendErrorToNotification);
}
</script>
