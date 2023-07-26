<template>
    <div>
        <div class="card">
            <div class="top green">Upload</div>
            <div class="content">
                <div class="col-1-3 agray-text very-bold">
                    <select
                        v-model="selectedType"
                        @change="clearUploadedData"
                        class="capitalize width-100 mb2"
                    >
                        <option
                            v-for="(val, type) in types"
                            :key="type"
                            :value="type"
                        >
                            {{ type }}
                        </option>
                    </select>

                    <div>Title:</div>
                    <input
                        v-model="uploadData.title"
                        class="input width-100 mb2"
                        type="text"
                        placeholder="My Item"
                        required
                    />

                    <div v-if="selectedType == 'head'" class="mb2">
                        <span>Head Texture:</span>
                        <input
                            v-model="headTexture"
                            type="checkbox"
                            name="offsale"
                            style="vertical-align: middle"
                        />
                    </div>

                    <input
                        @click="attemptUpload"
                        class="button green"
                        type="submit"
                        value="UPLOAD"
                    />
                </div>
                <div class="col-1-3" style="text-align: center">
                    <div v-if="types[selectedType].includes('mesh')">
                        <div
                            v-if="!uploadData.mesh"
                            class="agray-text very-bold"
                            style="margin-bottom: 5px"
                        >
                            No Mesh Chosen
                        </div>
                        <div
                            v-else
                            class="agray-text very-bold"
                            style="margin-bottom: 5px"
                        >
                            {{ uploadData.mesh.name }}
                        </div>
                    </div>

                    <div v-if="types[selectedType].includes('texture')">
                        <div
                            v-if="!uploadData.image"
                            class="agray-text very-bold"
                            style="margin-bottom: 5px"
                        >
                            No Texture Chosen
                        </div>
                        <div
                            v-else
                            class="agray-text very-bold"
                            style="margin-bottom: 5px"
                        >
                            {{ uploadData.image.name }}
                        </div>

                        <div class="file-img-box">
                            <img v-if="fileSrc" :src="fileSrc" />
                        </div>
                    </div>

                    <label
                        v-if="types[selectedType].includes('texture')"
                        class="button orange small unselectable no-cap"
                        for="upload-file-img"
                        >Choose Texture</label
                    >
                    <input
                        v-if="types[selectedType].includes('texture')"
                        @change="imgEvent"
                        name="img"
                        style="display: none"
                        id="upload-file-img"
                        accept="image/jpeg, image/png"
                        type="file"
                    />

                    <label
                        v-if="types[selectedType].includes('mesh')"
                        class="button orange small unselectable no-cap"
                        for="upload-file-mesh"
                        >Choose Mesh</label
                    >
                    <input
                        v-if="types[selectedType].includes('mesh')"
                        @change="meshEvent"
                        name="mesh"
                        style="display: none"
                        id="upload-file-mesh"
                        accept=".obj"
                        type="file"
                    />
                </div>
                <div class="col-1-3">
                    <div class="upload-preview-box">
                        <img :src="previewSrc" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.capitalize {
    text-transform: capitalize;
}
</style>

<script setup lang="ts">
// TODO: need a better filter for what files are wanted
// TODO: overall this shouldnt be an 'online' process and _should_ be something done through the client but that is a lot of dev time

import { axiosSendErrorToNotification } from "@/logic/notifications";
import axios from "axios";
import { reactive, ref, watch } from "vue";

const types: { [key: string]: string[] } = {
    hat: ["mesh", "texture"],
    tool: ["mesh", "texture"],
    face: ["texture"],
    head: ["mesh"],
};
const selectedType = ref<string>("hat");
const uploadData = reactive({
    title: "",
    image: null as any,
    mesh: null as any,
});
const headTexture = ref<boolean>(false);
const fileSrc = ref<string>("");
const previewSrc = ref<string>("");

function clearUploadedData() {
    uploadData.image = null;
    uploadData.mesh = null;
    fileSrc.value = "";
}

function meshEvent(e: Event) {
    if (
        !(e.target instanceof HTMLInputElement) ||
        !e.target.files ||
        e.target.files.length == 0
    )
        return;
    uploadData.mesh = e.target.files[0];
    attemptRender();
}

function imgEvent(e: Event) {
    if (!(e.target instanceof HTMLInputElement)) return;
    const files = e.target.files;
    if (!files || files.length == 0) return;

    uploadData.image = files.item(0) as File;
    let reader = new FileReader();
    reader.onload = (e) => (fileSrc.value = e.target?.result as string);
    reader.readAsDataURL(files.item(0) as Blob);
    attemptRender();
}

function attemptRender() {
    // check if data is filled out before requesting preview
    if (types[selectedType.value].includes("mesh") && uploadData.mesh === null)
        return;
    if (
        types[selectedType.value].includes("texture") &&
        uploadData.image === null
    )
        return;
    render();
}

function render() {
    let form = new FormData();
    form.append("type", selectedType.value);
    if (types[selectedType.value].includes("texture"))
        form.append("texture", uploadData.image);
    if (types[selectedType.value].includes("mesh"))
        form.append("mesh", uploadData.mesh);
    axios
        .post(`/api/shop/render/preview`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(({ data }) => {
            previewSrc.value = data;
        })
        .catch(axiosSendErrorToNotification);
}

function attemptUpload() {
    // use formdata to allow for posting of file information
    let form = new FormData();
    form.append("title", uploadData.title);
    form.append("type", selectedType.value);
    if (types[selectedType.value].includes("texture"))
        form.append("texture", uploadData.image);
    if (types[selectedType.value].includes("mesh"))
        form.append("mesh", uploadData.mesh);
    axios
        .post(`/v1/admin/shop/uploadItem`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(() => {
            location.reload();
        })
        .catch(axiosSendErrorToNotification);
}

watch(headTexture, (val) => {
    if (val) types.head = ["mesh", "texture"];
    else types.head = ["mesh"];
});
</script>
