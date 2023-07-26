<template>
    <Dropdown class="dropdown" style="top: 0; right: 2px">
        <ul>
            <li v-if="BH.user?.id != for_user">
                <a :href="`/report/user/${for_user}`">Report</a>
            </li>
            <li
                v-if="
                    can_view &&
                    permissionStore.canAny(
                        'view user economy',
                        'view linked accounts',
                        'view emails',
                        'view purchases'
                    )
                "
            >
                <a :href="`/user/${for_user}/audit`">Audit</a>
            </li>
            <li v-if="can_update && permissionStore.can('scrub users')">
                <a @click="scrubUsername">Scrub Username</a>
            </li>
            <li v-if="can_update && permissionStore.can('scrub users')">
                <a @click="scrubDescription">Scrub Description</a>
            </li>
            <li v-if="can_update && permissionStore.can('scrub users')">
                <a @click="refreshAvatar">Refresh Avatar</a>
            </li>
            <li v-if="can_update && permissionStore.can('ban')">
                <a :href="`/user/${for_user}/ban`">Ban</a>
            </li>
            <li v-if="can_update && permissionStore.can('superban')">
                <a :href="`/user/${for_user}/superban`">Super Ban</a>
            </li>
        </ul>
    </Dropdown>
</template>

<script setup lang="ts">
import { permissionStore } from "@/store/modules/permission";
import { BH } from "@/logic/bh";
import axios from "axios";
import Dropdown from "../global/Dropdown.vue";

const props = defineProps<{
    for_user: number;
    can_view?: boolean;
    can_update?: boolean;
}>();

permissionStore.loadCan(
    "scrub users",
    "ban",
    "view user economy",
    "view linked accounts",
    "view emails",
    "view purchases",
    "superban"
);

function scrubUsername() {
    axios
        .post(`/user/${props.for_user}/scrubName`)
        .then((res) => location.reload());
}

function scrubDescription() {
    axios
        .post(`/user/${props.for_user}/scrubDesc`)
        .then((res) => location.reload());
}

function refreshAvatar() {
    axios
        .post(`/user/${props.for_user}/renderAvatar`)
        .then((res) => location.reload());
}
</script>
