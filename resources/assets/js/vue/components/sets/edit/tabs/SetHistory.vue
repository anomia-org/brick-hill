<template>
    <div class="col-2-3">
        <div class="mb-12">
            Your set history archives versions of your set that you have
            published.
        </div>

        <div v-if="archives.length > 0">
            <table class="col-2-3 mb-20 striped">
                <tr>
                    <th>ID</th>
                    <th>Date Published</th>
                </tr>
                <tr
                    v-for="archive in archives"
                    :key="archive.id"
                    :class="{ selected: archive.active }"
                    style="cursor: pointer"
                    @click="setActive(archive)"
                >
                    <td>{{ archive.id }}</td>
                    <td>{{ filterDatetime(archive.created_at) }}</td>
                </tr>
            </table>

            <div class="col-1-1">
                <button
                    class="blue"
                    v-if="setEditState.serverType == 'dedicated'"
                    @click="setAsset"
                >
                    Revert
                </button>
            </div>
        </div>
        <div v-else>No uploaded files to select from</div>
    </div>
</template>

<script setup lang="ts">
import { filterDatetime } from "@/filters/index";
import { ref } from "vue";
import axios from "axios";
import { BH } from "@/logic/bh";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import { setEditStore } from "@/store/modules/sets/edit";

const props = defineProps<{
    setId: number;
}>();

const archives = ref<any>([]);
const setEditState = setEditStore.getState();

loadArchives();

function loadArchives() {
    axios
        .get(BH.apiUrl(`v1/sets/${props.setId}/getAssets`))
        .then(({ data }) => {
            archives.value = data.data;
        });
}

function setActive(archive: any) {
    archives.value.map((ar: any) => {
        ar.active = 0;
    });
    archive.active = 1;
}

function setAsset() {
    let archive = archives.value.find((ar: any) => ar.active);
    axios
        .post(`/play/${props.setId}/setActive`, {
            id: archive.id,
        })
        .then(() => successToNotification("Set saved successfully"))
        .catch(axiosSendErrorToNotification);
}
</script>
