<template>
    <div>
        <div v-if="setEditState.serverType == 'dedicated'" class="mb-20">
            <div class="mb-10">Maximum Players:</div>
            <select v-model="maxPlayers">
                <option v-for="n in 15" :key="n">{{ n }}</option>
            </select>
        </div>

        <div v-if="false" class="mb-20">
            <div class="mb-10">Who can join:</div>
            <input
                type="radio"
                value="everyone"
                id="everyone"
                v-model="whoCanJoin"
            />
            <label for="everyone">Everyone</label>
            <br />
            <input
                type="radio"
                value="friends"
                id="friends"
                v-model="whoCanJoin"
            />
            <label for="friends">Friends Only</label>
        </div>

        <button class="blue" @click="saveAccess">Save</button>
    </div>
</template>

<script setup lang="ts">
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import { setEditStore } from "@/store/modules/sets/edit";
import axios from "axios";
import { ref } from "vue";

const props = defineProps<{
    initMaxPlayers: number;
    initWhoCanJoin: string;
    setId: number;
}>();

const maxPlayers = ref<number>(props.initMaxPlayers || 1);
const whoCanJoin = ref<string>(props.initWhoCanJoin || "everyone");

function saveAccess() {
    axios
        .post(`/play/${props.setId}/playerAccess`, {
            max_players: maxPlayers.value,
            who_can_join: whoCanJoin.value,
        })
        .then(() => successToNotification("Player access saved successfully"))
        .catch(axiosSendErrorToNotification);
}

const setEditState = setEditStore.getState();
</script>
