<template>
    <Tabs>
        <Tab :show="permissionStore.can('accept items')" name="Items">
            <div v-if="Object.keys(approvalItems).length > 0">
                <table
                    v-for="(item, i) in approvalItems.data"
                    :key="i"
                    style="width: 100%"
                >
                    <tr>
                        <td style="width: 30%; word-break: break-word">
                            <a :href="`/shop/${item.id}`" target="_blank">
                                <p>{{ item.name }}</p>
                            </a>
                        </td>
                        <td style="width: 30%">
                            <a :href="`/shop/${item.id}`" target="_blank">
                                <img
                                    style="width: 100%"
                                    :src="
                                        typeof assetData[item.asset.uuid] !==
                                        'undefined'
                                            ? BH.apiUrl(
                                                  `v2/assets/get/${
                                                      assetData[item.asset.uuid]
                                                          .assetId
                                                  }`
                                              )
                                            : ''
                                    "
                                />
                            </a>
                        </td>
                        <td style="width: 30%">
                            <a :href="`/shop/${item.id}`" target="_blank">
                                <img
                                    style="width: 100%"
                                    :src="
                                        thumbnailStore.getThumbnail({
                                            id: item.id,
                                            type: ThumbnailType.ItemFull,
                                            admin: true,
                                        })
                                    "
                                />
                            </a>
                        </td>
                        <td style="width: 10%; text-align: right">
                            <button
                                class="button small green"
                                :disabled="
                                    typeof assetData[item.asset.uuid] ===
                                    'undefined'
                                "
                                @click="pendingItem(item, true)"
                            >
                                Accept
                            </button>
                            <p>
                                <button
                                    class="button small orange"
                                    @click="pendingItem(item, false)"
                                >
                                    Decline
                                </button>
                            </p>
                            <a
                                :href="`/user/${item.creator_id}/ban/item/${item.id}`"
                            >
                                <button
                                    class="button small red"
                                    :disabled="
                                        typeof assetData[item.asset.uuid] ===
                                        'undefined'
                                    "
                                    @click="pendingItem(item, false)"
                                >
                                    Ban
                                </button>
                            </a>
                        </td>
                    </tr>
                    <br />
                </table>
            </div>
        </Tab>
        <Tab :show="permissionStore.can('accept items')" name="Other">
            <div v-if="Object.keys(approvalAssets).length > 0">
                <table
                    v-for="(asset, i) in approvalAssets.data"
                    :key="i"
                    style="width: 100%"
                >
                    <tr>
                        <td style="width: 30%">
                            <a
                                :href="`/user/${asset.creator_id}`"
                                target="_blank"
                            >
                                <img style="width: 100%" :src="asset.url" />
                            </a>
                        </td>
                        <td style="width: 20%; text-align: right">
                            <button
                                class="button small green"
                                @click="pendingAsset(asset, true)"
                            >
                                Accept
                            </button>
                            <p>
                                <button
                                    class="button small orange"
                                    @click="pendingAsset(asset, false)"
                                >
                                    Decline
                                </button>
                            </p>
                            <a :href="`/user/${asset.creator_id}/ban/`">
                                <button
                                    class="button small red"
                                    @click="pendingAsset(asset, false)"
                                >
                                    Ban
                                </button>
                            </a>
                        </td>
                    </tr>
                    <br />
                </table>
            </div>
        </Tab>
    </Tabs>
</template>

<script setup lang="ts">
import Tab from "@/components/global/tabs/Tab.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { permissionStore } from "@/store/modules/permission";
import { BH } from "@/logic/bh";
import axios from "axios";
import { ref } from "vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const approvalItems = ref<any>({});
const approvalAssets = ref<any>({});
const assetData = ref<any>({});

loadItems();
loadAssets();

type Asset = {
    id: number;
    uuid: string;
};

function loadItems() {
    axios.get(`/v1/admin/approval/items`).then(({ data }) => {
        approvalItems.value = data;
        for (let item of data.data) {
            getAssetFromTemplate(item.asset);
        }
    });
}

function loadAssets() {
    axios.get(`/v1/admin/approval/assets`).then(({ data }) => {
        approvalAssets.value = data;
    });
}

async function getAssetFromTemplate(asset: Asset) {
    await axios
        .get(`${BH.storage_domain}/v3/assets/${asset.uuid}`, {
            withCredentials: false,
        })
        .then(({ data }) => {
            assetData.value[asset.uuid] = {
                rawFile: data,
                assetId: data[0].texture.replace("asset://", ""),
            };
        })
        .catch(() => {});
}

function pendingItem(item: any, approved: boolean) {
    axios
        .post(
            `/v1/admin/approve/item/${item.id}/${
                assetData.value[item.asset.uuid].assetId
            }`,
            {
                toggle: approved,
            }
        )
        .then(loadItems)
        .catch(axiosSendErrorToNotification);
}

function pendingAsset(asset: any, approved: boolean) {
    axios
        .post(`/v1/admin/approve/asset/${asset.id}`, {
            toggle: approved,
        })
        .then(loadAssets)
        .catch(axiosSendErrorToNotification);
}
</script>
