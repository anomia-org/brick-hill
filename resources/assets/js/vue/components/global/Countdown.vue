<template>
    <span :title="rawTime">
        {{ timeFormatted }}
    </span>
</template>

<i18n lang="json" locale="en">
{
    "second": "second | seconds",
    "minute": "minute | minutes",
    "hour": "hour | hours",
    "day": "day | days"
}
</i18n>

<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps<{
    countdownTo: string | number | Date;
    reload?: string | boolean;
    shortForm?: boolean;
    addLeft?: boolean;
}>();

const emit = defineEmits(["finished"]);

let date = ref<Date>(new Date(props.countdownTo));

onMounted(() => {
    setInterval(() => {
        // any other way to recompute computed variables?
        date.value = new Date(props.countdownTo);
    }, 1000);
});

const rawTime = computed<string>(() => {
    let time = date.value.getTime() - new Date().getTime();

    let days = Math.floor(time / (1000 * 60 * 60 * 24));
    let hours = Math.floor((time % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    let minutes = Math.floor((time % (1000 * 60 * 60)) / (1000 * 60));
    let seconds = Math.floor((time % (1000 * 60)) / 1000);

    if (seconds < 0) {
        emit("finished");

        if (props.reload) location.reload();

        return "0 seconds";
    }

    // my brain is too big
    let timeString = ``;
    if (days >= 1) {
        timeString += `${Math.floor(days)} `;
        timeString += `${t("day", days)}, `;
    }
    if (hours >= 1) {
        timeString += `${hours} `;
        timeString += `${t("hour", hours)}, `;
    }
    if (minutes >= 1) {
        timeString += `${minutes} `;
        timeString += `${t("minute", minutes)}, `;
    }

    timeString += `${seconds} `;
    timeString += `${t("second", seconds)}`;

    return timeString;
});

const timeFormatted = computed(() => {
    let timeString = rawTime.value;
    if (props.shortForm) {
        timeString = timeString.split(",")[0];
    }

    if (props.addLeft) {
        timeString += ` left`;
    }

    return timeString;
});
</script>
