<template>
    <div v-if="typeof schedule.id !== 'undefined'" class="col-2-3">
        <div class="col-1-2">
            <div class="card">
                <div class="top green">Original</div>
                <ViewScheduleCard :item="schedule.item" />
            </div>
        </div>
        <div class="col-1-2">
            <div class="card">
                <div class="top green">New</div>
                <ViewScheduleCard :item="schedule" />
            </div>
        </div>
        <div class="col-1-1">
            <div class="card">
                <div class="top green">Confirm</div>
                <div class="content">
                    <div class="mb2">
                        <template v-if="schedule.hide_update">
                            This schedule
                            <span class="red-text">WILL NOT</span> update the
                            items timestamps
                        </template>
                        <template v-else>
                            This schedule
                            <span class="red-text">WILL</span> update the items
                            timestamps
                        </template>
                    </div>
                    <div class="mb2">
                        <span class="bold">Scheduled For: </span>
                        <span>
                            {{ filterDatetime(schedule.scheduled_for) }}
                        </span>
                    </div>
                    <div v-if="schedule.approver !== null" class="mb2">
                        <span class="bold">Last status change: </span>
                        <a
                            :href="`/user/${schedule.approver.id}`"
                            target="_blank"
                        >
                            {{ schedule.approver.username }}
                        </a>
                    </div>
                    <div
                        v-if="
                            new Date(schedule.scheduled_for) <= new Date() &&
                            schedule.is_approved
                        "
                    >
                        This schedule has already been approved and gone through
                    </div>
                    <div v-else>
                        <div
                            v-if="
                                schedule.approver !== null &&
                                !schedule.is_approved
                            "
                        >
                            This schedule has already been denied and cannot be
                            overwritten
                        </div>
                        <div v-else>
                            <div
                                v-if="
                                    new Date(schedule.scheduled_for) <=
                                    new Date()
                                "
                                class="mb2"
                            >
                                This schedule's date is already past. It will go
                                through within a minute when approved.
                            </div>
                            <div
                                v-if="
                                    permissionStore.can('approve item schedule')
                                "
                            >
                                <button
                                    v-if="!schedule.is_approved"
                                    @click="changeScheduleApproval(schedule, 1)"
                                    class="green"
                                >
                                    Approve
                                </button>
                                <button
                                    @click="changeScheduleApproval(schedule, 0)"
                                    class="red"
                                >
                                    Deny
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { filterDatetime } from "@/filters/index";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { permissionStore } from "@/store/modules/permission";
import axios from "axios";
import ViewScheduleCard from "./ViewScheduleCard.vue";

defineProps<{
    schedule: any;
}>();

const emit = defineEmits(["statusChanged"]);

function changeScheduleApproval(schedule: any, type: number) {
    axios
        .post(`/v1/admin/shop/schedule/${schedule.id}/toggle`, {
            toggle: type,
        })
        .then(() => {
            emit("statusChanged", {
                schedule_id: schedule.id,
                status: type,
            });
        })
        .catch(axiosSendErrorToNotification);
}
</script>
