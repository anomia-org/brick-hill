<template>
    <div class="painter">
        <div class="painter-container">
            <div class="col-5-12 paint-container">
                <div class="bold">Paint Avatar</div>
                <div class="center-text">
                    <div style="padding: 20px">
                        <div style="margin-bottom: -4px">
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('head'),
                                }"
                                @click="toggleBodyPart($event, 'head')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.head}`,
                                }"
                                class="avatar-part head"
                            />
                        </div>
                        <div style="margin-bottom: -4px">
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('left_arm'),
                                }"
                                @click="toggleBodyPart($event, 'left_arm')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.left_arm}`,
                                }"
                                class="avatar-part arm left"
                            />
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('torso'),
                                }"
                                @click="toggleBodyPart($event, 'torso')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.torso}`,
                                }"
                                class="avatar-part torso"
                            />
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('right_arm'),
                                }"
                                @click="toggleBodyPart($event, 'right_arm')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.right_arm}`,
                                }"
                                class="avatar-part arm right"
                            />
                        </div>
                        <div>
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('left_leg'),
                                }"
                                @click="toggleBodyPart($event, 'left_leg')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.left_leg}`,
                                }"
                                class="avatar-part leg left"
                            />
                            <div
                                :class="{
                                    selected:
                                        selectedBodyParts.includes('right_leg'),
                                }"
                                @click="toggleBodyPart($event, 'right_leg')"
                                :style="{
                                    'background-color': `#${avatarData.colors?.right_leg}`,
                                }"
                                class="avatar-part leg right"
                            />
                        </div>
                    </div>
                    <div class="bold pointer" @click="selectAllBodyParts">
                        SELECT ALL
                    </div>
                </div>
            </div>
            <div class="col-7-12 text-center" style="padding-left: 20px">
                <div class="color-container">
                    <div
                        v-for="tone in skinTones"
                        class="color-box"
                        :class="{
                            selected: wearingColors.includes(
                                tone.toLowerCase()
                            ),
                        }"
                        :style="{ 'background-color': `#${tone}` }"
                        @click="modifySelectedColors(tone)"
                    />
                </div>
                <div class="color-selector">
                    <div class="col-1-2">
                        <select
                            class="width-100"
                            style="font-size: 14px"
                            v-model="selectedPalette"
                        >
                            <option
                                v-for="(colors, palette) in colorPalettes"
                                :value="palette"
                            >
                                {{ palette }}
                            </option>
                        </select>
                    </div>
                    <div
                        class="col-1-2"
                        style="position: relative; text-align: left"
                    >
                        <input
                            v-model="color"
                            type="color"
                            style="margin-right: 0"
                            class="avatar-color-picker"
                            @change="modifySelectedColors(color)"
                        />
                        <input
                            v-model="color"
                            class="avatar-color-picker-hex"
                            @keyup.enter="modifySelectedColors(color)"
                        />
                    </div>
                </div>
                <div
                    class="color-container"
                    style="padding: 0 28px; margin-bottom: 5px"
                >
                    <div
                        v-for="tone in blackToWhite"
                        class="color-box"
                        :class="{
                            selected: wearingColors.includes(
                                tone.toLowerCase()
                            ),
                        }"
                        :style="{ 'background-color': `#${tone}` }"
                        @click="modifySelectedColors(tone)"
                    />
                </div>
                <div
                    class="color-container"
                    style="padding: 0 36px; text-align: left"
                >
                    <div
                        v-for="tone in colorPalettes[selectedPalette]"
                        class="color-box"
                        :class="{
                            selected: wearingColors.includes(
                                tone.toLowerCase()
                            ),
                        }"
                        :style="{ 'background-color': `#${tone}` }"
                        @click="modifySelectedColors(tone)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.avatar-color-picker-hex {
    height: 38px;
    margin-left: 0;
    position: absolute;
    top: 0;
    width: 85%;
    border-radius: 0 2px 2px 0;
    border-left: 0;
    padding: 0 8px;
}
.new-theme .painter-container .avatar-color-picker {
    width: 38px;
    height: 38px;
    border: 1px solid #fff;
    border-radius: 2px 0 0 2px;
}
.color-selector {
    padding: 10px;

    @media only screen and (min-width: 767px) {
        margin-bottom: 35px;
    }
}
.color-container {
    white-space: pre-wrap;
}
.color-box {
    cursor: pointer;
    display: inline-block;
    border-radius: 2px;
    padding: 15px;
    margin: 0 2px;

    &.selected {
        outline: 2px solid;
        position: relative;
        z-index: 2;

        @include themify() {
            outline-color: themed("blue");
        }
    }
}
.avatar-part {
    cursor: pointer;
    box-sizing: border-box;
    background-color: transparent;
    display: inline-block;
    border: 2px solid #00000040;

    &:not(.head) {
        border-radius: 1px;
    }

    &.selected {
        outline: 2px solid;
        position: relative;
        z-index: 2;

        &:not(.head) {
            border-radius: 2px;
        }

        @include themify() {
            outline-color: themed("blue");
        }
    }
}
.head {
    background-color: #fff;
    width: 30%;
    padding-bottom: 30%;
    margin: 1px;
    border-radius: 25%;
}
.torso {
    background-color: #fff;
    width: 50%;
    padding-bottom: 50%;
    margin: 1px;
}
.arm {
    background-color: #7b8183;
    width: 25%;
    padding-bottom: 50%;
    margin: 1px;
}
.leg {
    background-color: #7b8183;
    width: 25%;
    padding-bottom: 50%;
    margin-top: 1px;

    &.left {
        margin-left: 5px;
        width: calc(25% - 2px);
        margin-right: 1px;
    }

    &.right {
        margin-left: 1px;
    }
}
.painter {
    position: absolute;
    top: -10px;
    left: -10px;
    padding-left: 100%;
    width: 275%;
    min-height: calc(100% + 20px);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    border-radius: 2px;
    z-index: 10;

    @include themify() {
        background-color: themed("table", "darker");
    }

    .paint-container {
        @media only screen and (min-width: 767px) {
            border-right: 1px solid #000;
        }
    }

    @media handheld, only screen and (max-width: 767px) {
        width: calc(100% + 15px);
        padding-left: 0;
        height: auto;
        padding-top: calc(100% + 50px);
    }

    @media handheld, only screen and (max-width: 400px) {
        padding-top: calc(120% + 50px);
    }

    @media handheld, only screen and (max-width: 240px) {
        padding-top: calc(150% + 50px);
    }

    .painter-container {
        white-space: nowrap;
        padding: 20px;
    }
}
</style>

<script setup lang="ts">
import { computed, ref } from "vue";
import { skinTones, blackToWhite, colorPalettes } from "./editor_colors";
import { UndoState } from "./modification_handler";

const props = defineProps<{
    avatarData: any;
}>();

const emit = defineEmits(["modifyColors"]);

const selectedPalette = ref<string>("Kahuna");
const color = ref<string>("#FF0000");
const selectedBodyParts = ref<string[]>([]);

const allBodyParts = [
    "head",
    "left_arm",
    "torso",
    "right_arm",
    "left_leg",
    "right_leg",
];

function selectAllBodyParts() {
    // if they press select all when having all selected unselect all
    if (selectedBodyParts.value.length == allBodyParts.length) {
        selectedBodyParts.value = [];
        return;
    }

    // somehow i triggered a bug when making this that made this function only select the left leg
    // couldnt reproduce it
    // the mysteries of js

    selectedBodyParts.value = allBodyParts;
}

function toggleBodyPart(e: Event, part: string) {
    let alreadySelected = selectedBodyParts.value.includes(part);
    let shift = (e as KeyboardEvent).shiftKey;
    if (!shift) {
        if (alreadySelected) {
            selectedBodyParts.value = [];
        } else {
            selectedBodyParts.value = [part];
        }
        return;
    }

    if (alreadySelected) {
        selectedBodyParts.value.splice(
            selectedBodyParts.value.indexOf(part),
            1
        );
    } else {
        selectedBodyParts.value.push(part);
    }
}

function modifySelectedColors(color: string) {
    let modifications: UndoState = {
        undo: [],
        redo: [],
    };

    color = color.replace("#", "");
    for (let part of selectedBodyParts.value) {
        if (props.avatarData.colors[part] !== color) {
            modifications.undo.push({
                type: part,
                value: props.avatarData.colors[part],
            });
            modifications.redo.push({
                type: part,
                value: color,
            });
        }
    }

    emit("modifyColors", modifications);
}

const wearingColors = computed<string[]>((): string[] => {
    return selectedBodyParts.value.map(
        (part: string) => props.avatarData?.colors[part].toLowerCase() ?? ""
    );
});
</script>
