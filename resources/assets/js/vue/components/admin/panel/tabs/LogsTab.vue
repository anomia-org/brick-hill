<template>
    <div class="new-theme">
        <div class="header">Admin Logs</div>
        <div class="col-1-1 no-pad mb-20">
            <input
                type="number"
                class="width-100"
                placeholder="User ID"
                v-model="selectedUser"
                @input="debounceUser"
            />
        </div>
        <div class="mb-20">
            <table
                class="col-1-1 border"
                style="border: none; table-layout: fixed"
            >
                <tr
                    class="no-top-border"
                    v-for="log in currentLogs"
                    :key="log.id"
                >
                    <td
                        class="col-1-12 ellipsis"
                        style="text-align: left; float: none"
                    >
                        <a
                            :href="`/user/${log.user.id}`"
                            target="_blank"
                            class="ellipsis"
                        >
                            {{ log.user.username }}
                        </a>
                    </td>
                    <td
                        class="col-1-12 ellipsis"
                        style="text-align: left; float: none"
                        :title="log.action"
                    >
                        <span class="ellipsis">
                            {{ log.action.split("@")[1] }}
                        </span>
                    </td>
                    <td class="col-4-12" style="text-align: left; float: none">
                        <p
                            style="
                                white-space: pre;
                                max-height: 250px;
                                overflow-y: scroll;
                            "
                            v-html="
                                syntaxHighlight(
                                    JSON.stringify(
                                        JSON.parse(log.data),
                                        null,
                                        4
                                    )
                                )
                            "
                        ></p>
                    </td>
                    <td class="col-1-12" style="text-align: left; float: none">
                        <div :title="log.created_at">
                            {{ filterDatetime(log.created_at) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-1-1 center-text" style="margin-bottom: 5px">
            <button
                class="blue small"
                style="margin-right: 5px"
                :disabled="pageNumber == 1"
                @click="logsAPI.loadPage(pageNumber - 1)"
            >
                <i class="fas fa-chevron-left"></i>
            </button>
            <span>{{ pageNumber }}</span>
            <button
                class="blue small"
                style="margin-left: 5px"
                :disabled="!logsAPI.hasNextPage()"
                @click="logsAPI.loadPage(pageNumber + 1)"
            >
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import CursorableAPI from "@/logic/apis/CursorableAPI";
import createDebounce from "@/logic/debounce";
import { filterDatetime } from "@/filters/index";
import { ref, watch } from "vue";

type Log = {
    id: number;
    user: {
        id: number;
        username: string;
    };
    action: string;
    data: string;
    created_at: string;
};

const logsAPI = new CursorableAPI<Log>(`/v1/admin/logs`, 50, "", false);
const { currentData: currentLogs, pageNumber } = logsAPI;

const selectedUser = ref<string>("");
const debouncedUser = ref<string>("");

const userDebounce = createDebounce();

function debounceUser() {
    userDebounce(() => {
        debouncedUser.value = selectedUser.value;
    }, 300);
}

watch(
    debouncedUser,
    (val) => {
        logsAPI.setParams([{ key: "user_id", value: val }]);
    },
    { immediate: true }
);

function syntaxHighlight(json: string) {
    json = json
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
    return json.replace(
        /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
        function (match) {
            var cls = "#f3b161";
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = "#ff9595";
                } else {
                    cls = "#95c395";
                }
            } else if (/true|false/.test(match)) {
                cls = "#8585db";
            } else if (/null/.test(match)) {
                cls = "#8585db";
            }
            return '<span style="color:' + cls + '">' + match + "</span>";
        }
    );
}
</script>
