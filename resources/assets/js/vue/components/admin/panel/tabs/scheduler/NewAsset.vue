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
                        <option v-for="type in types" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>

                    <div>
                        <span>Lossless:</span>
                        <input
                            v-model="uploadData.lossless"
                            type="checkbox"
                            name="offsale"
                            style="vertical-align: middle"
                        />
                        <div class="small-text">
                            DO NOT SELECT FOR UPLOADING TO ITEMS
                        </div>
                    </div>

                    <input
                        @click="attemptUpload"
                        class="button green"
                        type="submit"
                        value="UPLOAD"
                    />
                </div>
                <div class="col-1-3" style="text-align: center">
                    <div v-if="selectedType == 'mesh'">
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

                    <div v-if="selectedType == 'image'">
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
                        v-if="selectedType == 'image'"
                        class="button orange small unselectable no-cap"
                        for="upload-asset-img"
                        >Choose Texture</label
                    >
                    <input
                        v-if="selectedType == 'image'"
                        @change="imgEvent"
                        name="img"
                        style="display: none"
                        id="upload-asset-img"
                        accept="image/jpeg, image/png"
                        type="file"
                    />

                    <label
                        v-if="selectedType == 'mesh'"
                        class="button orange small unselectable no-cap"
                        for="upload-asset-mesh"
                        >Choose Mesh</label
                    >
                    <input
                        v-if="selectedType == 'mesh'"
                        @change="meshEvent"
                        name="mesh"
                        style="display: none"
                        id="upload-asset-mesh"
                        accept=".obj"
                        type="file"
                    />
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
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { notificationStore } from "@/store/modules/notifications";
import axios from "axios";
import { reactive, ref } from "vue";

const types = ["image", "mesh"];
const selectedType = ref<string>("image");
const uploadData = reactive({
    mesh: null as any,
    image: null as any,
    lossless: false,
});
const fileSrc = ref<string>();

function clearUploadedData() {
    uploadData.mesh = null;
    uploadData.image = null;
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
}

function imgEvent(e: Event) {
    if (!(e.target instanceof HTMLInputElement)) return;
    const files = e.target.files;
    if (!files || files.length == 0) return;

    uploadData.image = files.item(0) as File;
    let reader = new FileReader();
    reader.onload = (e) => (fileSrc.value = e.target?.result as string);
    reader.readAsDataURL(files.item(0) as Blob);
}

function attemptUpload() {
    // use formdata to allow for posting of file information
    let form = new FormData();
    form.append("type", selectedType.value);
    if (selectedType.value == "image") form.append("texture", uploadData.image);
    if (selectedType.value == "mesh") form.append("mesh", uploadData.mesh);
    form.append("lossless", uploadData.lossless.toString());
    axios
        .post(`/v1/admin/shop/uploadAsset`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(({ data }) => {
            notificationStore.setNotification(
                `Asset uploaded with id ${data.asset_id}`,
                "success"
            );
        })
        .catch(axiosSendErrorToNotification);
}
</script>
