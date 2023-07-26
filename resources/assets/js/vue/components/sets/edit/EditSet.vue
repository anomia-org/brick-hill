<template>
    <div class="new-theme">
        <div class="header">
            CONFIGURE SET
            <a
                :href="`/play/${setId}`"
                class="button clear"
                style="float: right"
            >
                Cancel
            </a>
        </div>

        <VerticalTabs :new-theme="true" :border="true">
            <Tab name="Main Info">
                <MainInfo
                    :init-set-name="setName"
                    :init-set-genre="setGenre"
                    :init-set-description="setDescription"
                />
            </Tab>
            <Tab name="Add Thumbnail">
                <AddThumbnail
                    :init-set-thumbnail="setThumbnail"
                    :set-id="setId"
                />
            </Tab>
            <Tab :show="trimmedDedicated === '1'" name="Set History">
                <SetHistory :setId="setId" />
            </Tab>
            <Tab name="Hosting">
                <Hosting
                    :canUseDedicated="trimmedDedicated"
                    :setId="setId"
                    :crashReport="crashReport"
                />
            </Tab>
            <Tab
                :show="
                    trimmedDedicated === '1' &&
                    setEditState.serverType == 'dedicated'
                "
                name="Player Access"
            >
                <PlayerAccess
                    :initMaxPlayers="maxPlayers"
                    :initWhoCanJoin="whoCanJoin"
                    :setId="setId"
                />
            </Tab>
        </VerticalTabs>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import Tab from "@/components/global/tabs/Tab.vue";
import VerticalTabs from "@/components/global/tabs/VerticalTabs.vue";

import MainInfo from "./tabs/MainInfo.vue";
import AddThumbnail from "./tabs/AddThumbnail.vue";
import PlayerAccess from "./tabs/PlayerAccess.vue";
import SetHistory from "./tabs/SetHistory.vue";
import Hosting from "./tabs/Hosting.vue";
import { setEditStore } from "@/store/modules/sets/edit";

const props = defineProps<{
    // TODO: why did i make every single value a prop? did apis not exist when i made this like 3 months ago
    setName: string;
    setGenre: string;
    setDescription: string;
    setThumbnail: string;
    setId: number;
    initServerType: string;
    maxPlayers: number;
    whoCanJoin: string;
    crashReport: string;
    canUseDedicated: string;
}>();

const trimmedDedicated = computed(() => props.canUseDedicated.trim());

setEditStore.setServerType(props.initServerType);

const setEditState = setEditStore.getState();
</script>
