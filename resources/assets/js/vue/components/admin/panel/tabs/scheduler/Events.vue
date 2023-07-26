<template>
    <div>
        <div>
            <div class="col-1-1">
                <div class="card">
                    <div class="top green">Create Event</div>
                    <div class="content">
                        <div class="mb2">
                            <span>Event Name:</span>
                            <input
                                v-model="newEventName"
                                type="text"
                                placeholder="Event Name"
                            />
                        </div>

                        <div class="mb2">
                            <span>Start time:</span>
                            <div>
                                Based on UTC timezone. Leave empty for unknown
                                time
                            </div>
                            <input
                                v-model="newEventStartDate"
                                type="datetime-local"
                                style="vertical-align: middle"
                            />
                        </div>

                        <div class="mb2">
                            <span>End time:</span>
                            <div>
                                Based on UTC timezone. Leave empty for unknown
                                time
                            </div>
                            <input
                                v-model="newEventEndDate"
                                type="datetime-local"
                                style="vertical-align: middle"
                            />
                        </div>

                        <button class="green" @click="createEvent">
                            SUBMIT
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1-1">
            <div class="col-1-3">
                <div class="old-theme trade-picker" ref="eventList">
                    <div v-for="event in events">
                        <div
                            class="trade"
                            style="height: auto"
                            @click="selectEvent(event)"
                            :class="{ selected: selectedEvent === event }"
                        >
                            <div>
                                <div
                                    style="
                                        padding-top: 10px;
                                        padding-left: 10px;
                                        padding-bottom: 10px;
                                    "
                                >
                                    <div>
                                        <span class="gray-text">ID: </span>
                                        {{ event.id }}
                                    </div>
                                    <div>
                                        <span class="gray-text">Name: </span>
                                        {{ event.name }}
                                    </div>
                                    <div>
                                        <span class="gray-text">
                                            Start Date:
                                        </span>
                                        <template v-if="event.start_date">
                                            {{
                                                filterDatetime(event.start_date)
                                            }}
                                        </template>
                                        <template v-else>None</template>
                                    </div>
                                    <div>
                                        <span class="gray-text">
                                            End Date:
                                        </span>
                                        <template v-if="event.end_date">
                                            {{ filterDatetime(event.end_date) }}
                                        </template>
                                        <template v-else>None</template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-2-3">
                <div v-if="selectedEvent" class="card">
                    <div class="top green">Modify Event</div>
                    <div class="content">
                        <div class="mb2">
                            <span>Event Name:</span>
                            <input
                                v-model="modifiedEvent.name"
                                type="text"
                                placeholder="Event Name"
                            />
                        </div>

                        <div class="mb2">
                            <span>Start time:</span>
                            <div>
                                Based on UTC timezone. Leave empty for unknown
                                time
                            </div>
                            <input
                                v-model="modifiedEvent.start_date"
                                type="datetime-local"
                                style="vertical-align: middle"
                            />
                        </div>

                        <div class="mb2">
                            <span>End time:</span>
                            <div>
                                Based on UTC timezone. Leave empty for unknown
                                time
                            </div>
                            <input
                                v-model="modifiedEvent.end_date"
                                type="datetime-local"
                                style="vertical-align: middle"
                            />
                        </div>

                        <button class="green" @click="modifyEvent">
                            SUBMIT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { filterDatetime } from "@/filters";
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { onMounted, reactive, ref } from "vue";

type EventData = {
    id: number;
    name: string;
    start_date: string | null;
    end_date: string | null;
};

const eventListAPI = new InfiniteScrollAPI<EventData>(
    `/v1/admin/shop/events`,
    20,
    "",
    false
);
const { currentData: events } = eventListAPI;

const eventList = ref<HTMLDivElement>();

const selectedEvent = ref<EventData>();
const modifiedEvent = reactive<EventData>({
    id: 1,
    name: "",
    start_date: "",
    end_date: "",
});

onMounted(() => {
    hasInfiniteScroll(() => eventListAPI.loadNextPage(), eventList.value);
});

function selectEvent(event: EventData) {
    selectedEvent.value = event;
    modifiedEvent.name = event.name;
    if (event.start_date) {
        modifiedEvent.start_date = new Date(event.start_date)
            .toISOString()
            .slice(0, -8);
    } else {
        modifiedEvent.start_date = "";
    }
    if (event.end_date) {
        modifiedEvent.end_date = new Date(event.end_date)
            .toISOString()
            .slice(0, -8);
    } else {
        modifiedEvent.end_date = "";
    }
}

const newEventName = ref<string>();
const newEventStartDate = ref<string>();
const newEventEndDate = ref<string>();

function createEvent() {
    axios
        .post(`/v1/admin/shop/createEvent`, {
            name: newEventName.value,
            start_date: newEventStartDate.value,
            end_date: newEventEndDate.value,
        })
        .then(() => {
            eventListAPI.refreshAPI();
            successToNotification("Event created");
        })
        .catch(axiosSendErrorToNotification);
}

function modifyEvent() {
    if (!selectedEvent.value) return;
    axios
        .post(`/v1/admin/shop/modifyEvent/${selectedEvent.value.id}`, {
            name: modifiedEvent.name,
            start_date: modifiedEvent.start_date,
            end_date: modifiedEvent.end_date,
        })
        .then(() => {
            eventListAPI.refreshAPI();
            selectedEvent.value = undefined;
            successToNotification("Event modified");
        })
        .catch(axiosSendErrorToNotification);
}
</script>
