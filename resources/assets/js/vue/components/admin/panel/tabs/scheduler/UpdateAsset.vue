<template>
    <div>
        <div class="col-1-3">
            <input
                v-model="searchItem"
                @keypress.enter="searchItemSubmit"
                class="width-100"
                min="1"
                placeholder="Item ID"
                type="number"
            />
            <div
                class="text-center"
                style="margin-top: 10px"
                v-if="recentAssets.length == 0"
            >
                No recent assets
            </div>
            <div
                v-show="recentAssets.length > 0"
                class="old-theme trade-picker"
            >
                <div v-for="(asset, i) in recentAssets" :key="asset.id">
                    <div class="trade">
                        <div>
                            <div style="padding-top: 20px; margin-left: 10px">
                                ID: {{ asset.id }}
                            </div>
                            <a
                                class="trade-status dark-gray-text"
                                :href="asset.url"
                                target="_blank"
                            >
                                Asset URL
                            </a>
                        </div>
                    </div>
                    <hr v-if="i !== recentAssets.length - 1" />
                </div>
            </div>
        </div>
        <div v-if="!(typeof selectedItem.id === 'undefined')" class="card">
            <div class="top green">Update {{ selectedItem.name }}</div>
            <div class="content">
                <div class="small-text">
                    Inputting invalid data will break the item
                </div>
                <textarea
                    v-model="assetData"
                    class="width-100 block"
                    style="height: 200px; margin-bottom: 6px"
                ></textarea>

                <AreYouSure buttonClass="green" @accepted="submitData">
                    SAVE
                </AreYouSure>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AreYouSure from "@/components/global/AreYouSure.vue";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { ref } from "vue";

const searchItem = ref<number>();
const assetData = ref<string>("");
const selectedItem = ref<any>({});

const recentAssets = ref<any>([]);

load();

function load() {
    axios
        .get(`/v1/admin/shop/getRecentAssets`)
        .then(({ data }) => {
            recentAssets.value = data.data;
        })
        .catch(axiosSendErrorToNotification);
}

function searchItemSubmit() {
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

function selectItem(item: any) {
    axios
        .get(BH.apiUrl(`v2/assets/getPoly/1/${item.id}`), {
            withCredentials: false,
        })
        .then(({ data }) => {
            assetData.value = JSON.stringify(data, null, 4);
            selectedItem.value = item;
        })
        .catch(axiosSendErrorToNotification);
}

function submitData() {
    axios
        .post(`/v1/admin/shop/item/${selectedItem.value.id}/updateAsset`, {
            assetData: assetData.value,
        })
        .then(() => {
            location.reload();
        })
        .catch(axiosSendErrorToNotification);
}
</script>
