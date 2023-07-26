<template>
    <div>
        <div class="col-1-3 mb-20">
            <div class="mb-10">Name:</div>
            <input type="text" v-model="setName" />

            <span style="position: absolute" v-show="nameError">
                <SvgSprite
                    class="svg-icon"
                    square="16"
                    svg="notifications/error.svg"
                />
                <span>{{ nameError }}</span>
            </span>
        </div>

        <div class="col-2-3 mb-20">
            <div class="mb-10">Genre:</div>
            <select v-model="setGenre" style="margin-right: 20px">
                <option v-for="genre in genres" :key="genre" :value="genre">
                    {{ genre }}
                </option>
            </select>
        </div>
        <div class="mb-20">
            <div class="mb-10">Description:</div>
            <textarea
                style="height: 150px; width: 60%"
                v-model="setDescription"
            ></textarea>
            <div v-show="descError">
                <SvgSprite
                    class="svg-icon"
                    square="16"
                    svg="notifications/error.svg"
                />
                <span>{{ descError }}</span>
            </div>
        </div>

        <button class="blue" @click="save" :disabled="!formMeta.valid">
            Save
        </button>
    </div>
</template>

<script setup lang="ts">
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import axios from "axios";
import { useField, useForm } from "vee-validate";
import { computed, ref } from "vue";
import { object, string } from "yup";
import SvgSprite from "@/components/global/SvgSprite.vue";

const props = defineProps<{
    initSetName: string;
    initSetGenre: string;
    initSetDescription: string;
}>();

const schema = object({
    setName: string().required().min(3).max(100).label("Name"),
    setDescription: string().max(2000).label("Description"),
});

const formValues = {
    setName: props.initSetName,
    setDescription: props.initSetDescription,
};

const { meta: formMeta } = useForm({
    validationSchema: schema,
    initialValues: formValues,
});

const { value: setName, errorMessage: nameError } = useField<string>("setName");
const { value: setDescription, errorMessage: descError } =
    useField<string>("setDescription");

const genres = [
    "None",
    "Adventure",
    "Roleplay",
    "Action",
    "Simulation",
    "Showcase",
    "Minigame",
    "Platformer",
];

const setGenre = ref<string>(props.initSetGenre || "None");

const submitableGenre = computed(() => {
    if (setGenre.value == "None") return undefined;

    return setGenre.value;
});

function save() {
    axios
        .post(``, {
            title: setName.value,
            genre: submitableGenre.value,
            description: setDescription.value,
        })
        .then(() => successToNotification("Set saved successfully"))
        .catch(axiosSendErrorToNotification);
}
</script>
