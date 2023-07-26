<template>
    <div>
        <div class="col-1-3">
            <select
                v-model="paginator.type"
                @change="changeType"
                class="width-100"
            >
                <option value="pending">Pending Approval</option>
                <option value="upcoming">Upcoming</option>
                <option value="past">Past</option>
            </select>
            <div
                v-show="paginator.data.length > 0"
                class="old-theme trade-picker"
                ref="schedules"
            >
                <div v-for="(schedule, i) in paginator.data" :key="schedule.id">
                    <div
                        class="trade"
                        @click="selectedSchedule = schedule"
                        :class="{
                            selected: selectedSchedule.id == schedule.id,
                        }"
                    >
                        <div>
                            <img
                                class="trade-user-thumbnail"
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: schedule.item.id,
                                        type: ThumbnailType.ItemFull,
                                    })
                                "
                            />
                            <div class="ellipsis" style="padding-top: 20px">
                                {{ schedule.item.name }}
                            </div>
                            <div class="trade-date dark-gray-text">
                                {{ schedule.human_time }}
                            </div>
                            <div class="trade-status dark-gray-text">
                                {{ scheduleStatus(schedule) }}
                            </div>
                        </div>
                    </div>
                    <hr v-if="i !== paginator.data.length - 1" />
                </div>
            </div>
        </div>
        <ViewSchedule
            :schedule="selectedSchedule"
            @statusChanged="changeScheduleStatus"
        />
    </div>
</template>

<script setup lang="ts">
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import axios from "axios";
import { onMounted, reactive, ref } from "vue";
import ViewSchedule from "./ViewSchedule.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const schedules = ref<HTMLDivElement>();

onMounted(() => {
    hasInfiniteScroll(load, schedules.value);
});

const paginator = reactive({
    type: "pending",
    cursor: "",
    data: [] as any,
});
const loading = ref<boolean>(false);
const selectedSchedule = ref<any>({});

async function load() {
    if (paginator.cursor === null) return;
    loading.value = true;
    await axios
        .get(
            `/v1/admin/shop/scheduled/${paginator.type}?limit=25&cursor=${paginator.cursor}`
        )
        .then(({ data }) => {
            paginator.data.push(...data.data);
            paginator.cursor = data.next_cursor;
            if (
                paginator.data.length > 0 &&
                typeof selectedSchedule.value.id === "undefined"
            )
                selectedSchedule.value = paginator.data[0];
        })
        .catch(axiosSendErrorToNotification);
    loading.value = false;
}
function changeType() {
    paginator.cursor = "";
    paginator.data = [];

    load();
}

function scheduleStatus(schedule: any) {
    if (schedule.approver === null) return "Pending Approval";
    if (schedule.is_approved) return "Approved";
    if (schedule.approver !== null && !schedule.is_approved) return "Denied";
    return;
}

function changeScheduleStatus({
    schedule_id,
    status,
}: {
    schedule_id: number;
    status: boolean;
}) {
    let schedule = paginator.data.find((s: any) => s.id == schedule_id);
    schedule.is_approved = status;
    schedule.approver = {
        username: BH.user?.username,
    };

    selectedSchedule.is_approved = status;
}
</script>
