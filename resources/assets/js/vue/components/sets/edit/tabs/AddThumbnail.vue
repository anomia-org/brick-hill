<template>
    <div>
        <div class="col-1-2">
            <img
                class="mb-16"
                style="width: 100%"
                :src="thumbnailSrc !== '' ? thumbnailSrc : initSetThumbnail"
            />
        </div>

        <div class="col-1-1">
            <input
                class="mb-16"
                @change="imgEvent"
                style="width: 100%"
                accept="image/jpeg, image/png"
                type="file"
            />
        </div>

        <div class="col-2-3">
            <div class="mb-16">
                Make sure your chosen thumbnail includes content from your game
                and is not misleading
            </div>
            <div class="mb-16">
                Set thumbnails must be 768 x 512. If no thumbnail is chosen,
                your set will display a default image
            </div>
        </div>

        <div class="col-1-1">
            <button
                class="blue"
                :disabled="thumbnailImage === null"
                @click="submit"
            >
                Upload
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { ref } from "vue";

const props = defineProps<{
    initSetThumbnail: string;
    setId: Number;
}>();

const thumbnailSrc = ref<string>("");
const thumbnailImage = ref<File>({} as File);

function imgEvent(e: Event) {
    if (!(e.target instanceof HTMLInputElement)) return;
    const files = e.target.files;
    if (!files || files.length == 0) return;

    thumbnailImage.value = files.item(0) as File;
    let reader = new FileReader();
    reader.onload = (e) => (thumbnailSrc.value = e.target?.result as string);
    reader.readAsDataURL(files.item(0) as Blob);
}

function submit() {
    let form = new FormData();
    form.append("file", thumbnailImage.value);
    axios
        .post(`/play/${props.setId}/thumbnail`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(() => successToNotification("Thumbnail saved successfully"))
        .catch(axiosSendErrorToNotification);
}
</script>
