<template>
    <div class="card">
        <div class="top red">Reports</div>
        <div class="content">
            <table v-for="(report, i) in reports" :key="i" style="width: 100%">
                <tr>
                    <td style="width: 10%">
                        <a :href="report.reportable.url" target="_blank">
                            <img
                                v-if="report.reportable.image !== null"
                                :src="report.reportable.image"
                                style="width: 100%"
                            />
                        </a>
                    </td>
                    <td style="width: 70%">
                        <a :href="report.reportable.url" target="_blank">
                            <div style="text-transform: capitalize">
                                {{ report.type }}
                            </div>
                            <div class="bold" style="color: #999">
                                Reason: {{ report.report_reason.reason }}
                            </div>
                        </a>
                        <div class="bold" style="color: #999">
                            User says: "{{ report.note }}"
                        </div>
                        <a :href="report.reportable.url" target="_blank">
                            <div>
                                {{
                                    report.reportable.content.length > 250
                                        ? report.reportable.content.slice(
                                              0,
                                              250
                                          ) + '..."'
                                        : report.reportable.content
                                }}
                            </div>
                        </a>
                    </td>
                    <td style="width: 20%; text-align: right">
                        <a
                            v-if="bannableTypes.includes(report.type)"
                            :href="`/user/${report.reportable.author}/ban/${report.type}/${report.reportable.id}`"
                            target="_blank"
                        >
                            <button class="button small red">Ban</button>
                        </a>
                        <p>
                            <button
                                class="button small orange"
                                @click="sendReport(report)"
                            >
                                Ignore
                            </button>
                        </p>
                        <a :href="`/user/${report.sender.id}`" target="_blank"
                            ><button class="button small gray">
                                Reporter
                            </button></a
                        >
                    </td>
                </tr>
                <br />
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { axiosSendErrorToNotification } from "@/logic/notifications";
import axios from "axios";
import { ref } from "vue";

const reports = ref<any>([]);
const bannableTypes = [
    "forumthread",
    "forumpost",
    "item",
    "clan",
    "comment",
    "message",
];

load();

function load() {
    axios.get(`/api/admin/reports`).then(({ data }) => {
        reports.value = data.data;
    });
}

function sendReport(report: any) {
    axios
        .post(`/admin/report`, {
            report_id: report.id,
        })
        .then(({ data }) => {
            load();
        })
        .catch(axiosSendErrorToNotification);
}
</script>
