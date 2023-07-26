<template>
    <div class="mb-20">
        <div
            class="mb-20"
            :class="{ 'col-1-2': !editPage, 'col-1-1': editPage }"
        >
            <div class="mb-10">Name:</div>
            <input type="text" v-model="itemName" placeholder="My Item" />

            <div style="position: absolute" v-show="nameError">
                <SvgSprite
                    class="svg-icon svg-notif-icon"
                    square="16"
                    svg="notifications/error.svg"
                />
                <span>{{ nameError }}</span>
            </div>
        </div>
        <div class="col-1-2 mb-20" v-if="!editPage">
            <div class="mb-10">Type:</div>
            <select
                :value="modelValue"
                @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            >
                <option value="tshirt">T-Shirt</option>
                <option value="shirt">Shirt</option>
                <option value="pants">Pants</option>
            </select>
        </div>
    </div>
    <div class="mb-20">
        <div class="mb-10">Description:</div>
        <textarea
            style="width: 100%; height: 150px"
            type="text"
            v-model="itemDescription"
            :placeholder="randomDescPlaceholder"
        ></textarea>

        <div style="position: absolute" v-show="descError">
            <SvgSprite
                class="svg-icon svg-notif-icon"
                square="16"
                svg="notifications/error.svg"
            />
            <span>{{ descError }}</span>
        </div>
    </div>
    <div class="mb-20">
        <div class="mb-10">Price:</div>
        <div class="mb-10">
            <input
                type="checkbox"
                v-model="itemFree"
                @change="itemOffsale = false"
                id="free"
            />
            <label for="free">Free</label>
        </div>
        <div v-show="!itemFree" class="mb-10">
            <input type="checkbox" v-model="itemOffsale" id="offsale" />
            <label for="offsale">Offsale</label>
        </div>
        <div v-show="!itemFree && !itemOffsale">
            <SvgSprite
                class="svg-icon-medium-text svg-icon-margin"
                square="20"
                svg="shop/currency/bucks_full_color.svg"
            />
            <input
                class="thin blend"
                v-model="itemBucks"
                style="width: 100px; margin-right: 20px"
                type="number"
                min="1"
                placeholder="0 bucks"
            />
            <SvgSprite
                class="svg-icon-medium-text svg-icon-margin"
                square="20"
                svg="shop/currency/bits_full_color.svg"
            />
            <input
                class="thin blend"
                v-model="itemBits"
                style="width: 100px"
                type="number"
                min="1"
                placeholder="0 bits"
            />
        </div>
        <div style="position: absolute" v-show="bucksError || bitsError">
            <SvgSprite
                class="svg-icon svg-notif-icon"
                square="16"
                svg="notifications/error.svg"
            />
            <span>{{ bucksError || bitsError }}</span>
        </div>
    </div>
    <div style="margin-bottom: 5px">
        <button
            class="blue"
            @click="emitChange"
            :disabled="!formMeta.valid || (externalValid ?? false)"
        >
            {{ editPage ? "SAVE" : "UPLOAD" }}
        </button>
    </div>
    <!--
            <SvgSprite
                class="svg-icon-text"
                square="16"
                svg="notifications/info.svg"
            />
            <span class="small-text">This will cost 5 bucks</span>
            -->
</template>

<style lang="scss">
.left-container {
    border-right: 1px solid;

    @include themify($themes) {
        border-color: themed("inputs", "blend-border");
    }
}
</style>

<script setup lang="ts">
import { computed } from "vue";
import { useForm, useField } from "vee-validate";
import { object, string, number, boolean } from "yup";
import { ItemData } from "./types";

import SvgSprite from "@/components/global/SvgSprite.vue";

const props = defineProps<{
    editPage: boolean;
    initItemData?: ItemData;
    externalValid?: boolean;
    modelValue?: string;
}>();

const emit = defineEmits<{
    (e: "saved", item: ItemData): void;
    (e: "update:modelValue", data: string): void;
}>();

const schema = object({
    itemName: string().required().min(3).max(50).label("Name"),
    itemDescription: string().max(500).label("Description"),
    itemFree: boolean().required().label("Free"),
    itemOffsale: boolean().required().label("Offsale"),
    itemBucks: number()
        .nullable()
        .transform((_, val) => (val === Number(val) ? val : null))
        .when(["itemFree", "itemOffsale"], {
            is: false,
            then: (schema) =>
                schema
                    .min(1)
                    .max(2147483647)
                    .test({
                        name: "currency-not-empty",
                        message: "Bucks and bits must not both be empty",
                        // exclusive doesnt appear to work? error appears twice
                        // only one error can be displayed at once so its not reallllly an issue
                        exclusive: true,
                        test: (bucks, context) =>
                            bucks || context.parent.itemBits,
                    }),
        })
        .label("Bucks"),
    itemBits: number()
        .nullable()
        .transform((_, val) => (val === Number(val) ? val : null))
        .when(["itemFree", "itemOffsale"], {
            is: false,
            then: (schema) =>
                schema
                    .min(1)
                    .max(2147483647)
                    .test({
                        name: "currency-not-empty",
                        message: "Bucks and bits must not both be empty",
                        exclusive: true,
                        test: (bits, context) =>
                            bits || context.parent.itemBucks,
                    }),
        })
        .label("Bits"),
});

const formValues = {
    itemName: props.initItemData?.title,
    itemDescription: props.initItemData?.description,
    itemOffsale: props.initItemData?.offsale ?? true,
    itemFree: props.initItemData?.free ?? false,
    itemBucks: props.initItemData?.bucks,
    itemBits: props.initItemData?.bits,
};

const { meta: formMeta } = useForm({
    validationSchema: schema,
    initialValues: formValues,
});

const { value: itemName, errorMessage: nameError } =
    useField<string>("itemName");
const { value: itemDescription, errorMessage: descError } =
    useField<string>("itemDescription");
const { value: itemFree } = useField<boolean>("itemFree");
const { value: itemOffsale } = useField<boolean>("itemOffsale");
const { value: itemBucks, errorMessage: bucksError } = useField("itemBucks");
const { value: itemBits, errorMessage: bitsError } = useField("itemBits");

const quirkyPlaceholders = [
    "This is my shirt!",
    "Now in blue!",
    "Warning - loose parts!",
    "Avatar sold separately!",
    "Cozy pants!",
    "Brick building, brick build together part piece construct make create set.",
];
const randomDescPlaceholder = computed(
    () =>
        quirkyPlaceholders[
            Math.floor(Math.random() * quirkyPlaceholders.length)
        ]
);

function emitChange() {
    emit("saved", {
        title: itemName.value,
        description: itemDescription.value,
        bucks: itemBucks.value,
        bits: itemBits.value,
        offsale: itemOffsale.value,
        free: itemFree.value,
        selectedType: props.modelValue,
    });
}
</script>
