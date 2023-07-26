<template>
    <div class="col-10-12 push-1-12 new-theme">
        <div class="header">
            DESIGNER MENU
            <a href="/shop" class="button clear" style="float: right">Cancel</a>
        </div>
        <div class="col-1-2 left-container">
            <SharedForm
                :edit-page="false"
                :external-valid="
                    templateError.length > 0 || templateSrc.length == 0
                "
                v-model="selectedType"
                @saved="uploadAsset"
            />
        </div>
        <div class="col-1-2" style="padding-left: 20px">
            <div class="center-text" style="margin: 0 30px">
                <img
                    style="margin-bottom: 20px"
                    class="preview-img"
                    :class="{ empty: !previewSrc }"
                    :src="previewSrc"
                />
                <div style="margin: 0 20px">
                    <div
                        class="col-2-3"
                        style="margin-top: 20px; position: relative"
                    >
                        <div class="mb-10">
                            <label
                                class="button yellow width-100 upload-file"
                                style="padding: 10px 18px"
                                for="upload-file-img"
                            >
                                <SvgSprite
                                    class="svg-icon"
                                    square="20"
                                    svg="shop/upload/template.svg"
                                />
                                Open Template
                            </label>
                            <input
                                @change="imgEvent"
                                name="img"
                                style="display: none"
                                id="upload-file-img"
                                accept="image/jpeg, image/png"
                                type="file"
                            />
                        </div>
                        <div>
                            <a
                                class="button clear width-100"
                                href="https://brkcdn.com/default/template_new.png"
                            >
                                Get Template
                            </a>
                        </div>
                        <div
                            style="
                                position: absolute;
                                margin-top: 5px;
                                margin-left: -8px;
                            "
                            v-show="templateError"
                        >
                            <SvgSprite
                                class="svg-icon svg-notif-icon"
                                square="16"
                                svg="notifications/error.svg"
                            />
                            <span>{{ templateError }}</span>
                        </div>
                    </div>
                    <div class="col-1-3 center-text">
                        <div class="checkerboard mb-10">
                            <img
                                style="width: 100%"
                                @load="imageLoad"
                                :src="templateSrc"
                            />
                        </div>
                        <div class="ellipsis">{{ itemTemplate?.name }}</div>
                    </div>
                </div>
            </div>
            <!--
            <div class="col-1-1" style="margin-top:20px;">
                <div class="unselectable pointer mb-20" @click="showAdvanced = !showAdvanced">
                    <SvgSprite
                        :class="{ rotated: !showAdvanced }"
                        class="svg-icon svg-white svg-rotate"
                        square="12"
                        style="margin-top: 3px;"
                        svg="ui/dropdown_arrow.svg"
                    />
                    Advanced:
                </div>
                <div :class="{ open: showAdvanced }" class="advanced-info small-text">
                    <div class="mb-20">
                        <SvgSprite
                            class="svg-icon"
                            square="16"
                            svg="notifications/warning.svg"
                        />
                        These are the body parts that are being used on your template.
                        We will make a guess based on your initial upload, but you can change these using the editor below.
                    </div>
                    <div class="col-1-3 mobile-col-1-3 center-text">
                        <div style="margin-bottom:-4px;">
                            <div 
                                :class="{ selected: advanced.leftArm }"
                                @click="advanced.leftArm = !advanced.leftArm"
                                class="avatar-part arm left"
                            />
                            <div 
                                :class="{ selected: advanced.torso }"
                                @click="advanced.torso = !advanced.torso"
                                class="avatar-part torso"
                            />
                            <div 
                                :class="{ selected: advanced.rightArm }"
                                @click="advanced.rightArm = !advanced.rightArm"
                                class="avatar-part arm right"
                            />
                        </div>
                        <div>
                            <div 
                                :class="{ selected: advanced.leftLeg }"
                                @click="advanced.leftLeg = !advanced.leftLeg"
                                class="avatar-part leg left"
                            />
                            <div 
                                :class="{ selected: advanced.rightLeg }"
                                @click="advanced.rightLeg = !advanced.rightLeg"
                                class="avatar-part leg right"
                            />
                        </div>
                    </div>
                    <div class="col-2-3 center-text avatar-checkboxes">
                        <div class="mb-20">
                            <input v-model="advanced.leftArm" type="checkbox" id="leftArm">
                            <label for="leftArm">Left Arm</label>
                            <input v-model="advanced.torso" type="checkbox" id="torso">
                            <label for="torso">Torso</label>
                            <input v-model="advanced.rightArm" type="checkbox" id="rightArm">
                            <label for="rightArm">Right Arm</label>
                        </div>
                        <div>
                            <input v-model="advanced.leftLeg" type="checkbox" id="leftLeg">
                            <label for="leftLeg">Left Leg</label>
                            <input v-model="advanced.rightLeg" type="checkbox" id="rightLeg">
                            <label for="rightLeg">Right Leg</label>
                        </div>
                    </div>
                </div>
            </div>
            -->
        </div>
    </div>
</template>

<style lang="scss" scoped>
.avatar-checkboxes {
    margin-top: 20px;

    label {
        margin-right: 10px;
    }
}
.avatar-part {
    cursor: pointer;
    box-sizing: border-box;
    background-color: transparent;
    display: inline-block;
    border: 2px solid;

    @include themify($themes) {
        border-color: themed("dark");
    }
}
.torso {
    width: calc(50% - 2px);
    padding-bottom: 50%;
    border-right-width: 1px;
    border-left-width: 1px;

    &.selected {
        background-color: #aab4b9;
        border: 2px solid #00000020;
    }
}
.arm {
    width: 25%;
    padding-bottom: 50%;

    &.selected {
        background-color: #fed800;
        border: 2px solid #00000020;
    }

    &.left {
        border-right-width: 1px;
        border-radius: 2px 0 0 2px;
    }

    &.right {
        border-left-width: 1px;
        border-radius: 0 2px 2px 0;
    }
}
.leg {
    width: 25%;
    padding-bottom: 50%;
    border-top: 0;

    &.selected {
        background-color: #f1f1f1;
        border-color: #00000020;

        &.left,
        &.right {
            border-color: #00000020;
        }
    }

    &.left {
        border-right-width: 1px;
        border-radius: 0 0 0 2px;
    }

    &.right {
        border-left-width: 1px;
        border-radius: 0 0 2px;
    }
}
.advanced-info {
    overflow: hidden;
    transition: max-height 200ms;
}
.advanced-info:not(.open) {
    max-height: 0;
}
.advanced-info.open {
    max-height: 300px;
}
.preview-img {
    border-radius: 2px;
    border: 1px solid;
    padding: 20px;
    min-width: 65%;

    &.empty {
        padding-bottom: calc(65% - 20px);
    }

    @include themify() {
        border-color: themed("inputs", "blend-border");
        background: themed("media", "gradient");
    }
}
.checkerboard {
    height: 0;
    width: 100%;
    padding-bottom: 100%;
    border-radius: 1px;
    border: 1px solid;
    background-color: #49494c;
    background-image: linear-gradient(45deg, #59595c 25%, transparent 25%),
        linear-gradient(-45deg, #59595c 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #59595c 75%),
        linear-gradient(-45deg, transparent 75%, #59595c 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;

    @include themify() {
        border-color: themed("inputs", "border");
    }
}
</style>

<script setup lang="ts">
import axios from "axios";
import { reactive, ref, watch } from "vue";
import { ItemData } from "./types";

import { axiosSendErrorToNotification } from "@/logic/notifications";
import SvgSprite from "@/components/global/SvgSprite.vue";
import SharedForm from "./SharedForm.vue";
import { BH } from "@/logic/bh";

interface HTMLInputEvent extends Event {
    target: HTMLInputElement & EventTarget;
}

const selectedType = ref<string>("shirt");
const advanced = reactive({
    leftArm: false,
    rightArm: false,
    torso: false,
    leftLeg: false,
    rightLeg: false,
});
const showAdvanced = ref<boolean>(false);

const itemTemplate = ref<File>({} as File);
const templateSrc = ref<string>("");
const templateError = ref<string>("");
const previewSrc = ref<string>("");

function imgEvent(e: Event) {
    const files = (e as HTMLInputEvent).target.files;
    if (!files || files.length == 0) return;

    itemTemplate.value = files.item(0) as File;
    let reader = new FileReader();
    reader.onload = (e) => (templateSrc.value = e.target?.result as string);
    reader.readAsDataURL(files.item(0) as Blob);
}

function imageLoad(e: Event) {
    let img: HTMLImageElement = e.currentTarget as HTMLImageElement;
    let height = img.naturalHeight;
    let width = img.naturalWidth;

    if (height != width) {
        templateError.value = "Template must be square";
        return;
    }
    if (height < 128) {
        templateError.value = "Template must be greater than 128x128";
        return;
    }
    if (height > 1024) {
        templateError.value = "Template must be smaller than 1024x1024";
        return;
    }
    templateError.value = "";

    renderPreview();
}

function renderPreview() {
    if (templateError.value || !itemTemplate.value.text) return;

    let form = new FormData();
    form.append("type", selectedType.value);
    form.append("texture", itemTemplate.value);
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

function uploadAsset(item: ItemData) {
    let form = new FormData();
    form.append("title", item.title);
    form.append("type", selectedType.value);
    form.append("file", itemTemplate.value);
    axios
        .post(`/shop/create/upload`, form, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        })
        .then(({ data }) => {
            finalizeUpload(item, data.success);
        })
        .catch(axiosSendErrorToNotification);
}

// upload asset request doesnt allow for bits, bucks, desc, etc, so just post that data through with update api
function finalizeUpload(item: ItemData, itemId: number) {
    axios
        .post(`/shop/${itemId}/edit`, {
            title: item.title,
            description: item.description,
            bucks: item.bucks,
            bits: item.bits,
            offsale: item.offsale,
            free: item.free,
        })
        .then(() => {
            window.location.href = `/shop/${itemId}`;
        })
        // doing it in two requests is bangla as the upload could go fine but this could fail for some reason
        // that would leave them on the upload page thinking their item failed to upload
        // as the information is likely easily recreatable anyway just imagine that this request always works
        // they will then probably realize it didnt save their stuff and they will cope but who cares
        // could give this information in one request but its easier to maintain one endpoint to change all of this data
        .catch(() => {
            window.location.href = `/shop/${itemId}`;
        });
}

watch(selectedType, () => {
    renderPreview();
});
</script>
