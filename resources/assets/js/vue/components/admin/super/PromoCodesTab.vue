<template>
    <div>
        <div class="card">
            <div class="top green">New Promo Code</div>
            <div class="content">
                <div class="margin-5">
                    This code will be able to be used infinite times until the
                    expiration date is reached
                </div>
                <div class="block">
                    <input
                        class="margin-5"
                        v-model="newPromoCode"
                        type="text"
                        placeholder="Promo Code"
                    />
                    <input
                        class="margin-5"
                        v-model="newPromoItem"
                        type="number"
                        min="1"
                        placeholder="Item ID"
                    />
                    <input
                        class="margin-5"
                        v-model="newPromoDate"
                        type="date"
                    />
                    <input
                        class="margin-5"
                        v-model="newPromoTime"
                        type="time"
                    />
                </div>
                <div v-for="error in newPromoErrors">{{ error }}</div>
                <AreYouSure
                    @accepted="submitNewPromoCode"
                    :buttonDisabled="!newPromoMeta.valid"
                >
                    Submit
                </AreYouSure>
            </div>
        </div>
        <div class="card">
            <div class="top green">Mass Import Promo Codes</div>
            <div class="content">
                <div class="margin-5">
                    These codes will always be single use
                </div>
                <textarea
                    class="margin-5 block"
                    v-model="massImport.codes"
                    placeholder="Promo Codes"
                ></textarea>
                <input
                    class="margin-5 block"
                    v-model="massImportItem"
                    type="number"
                    min="1"
                    placeholder="Item ID"
                />
                <div v-for="error in massImportErrors">{{ error }}</div>
                <AreYouSure
                    @accepted="submitMassImportPromo"
                    :buttonDisabled="!massImportMeta.valid"
                >
                    Submit
                </AreYouSure>
            </div>
        </div>
        <div class="card">
            <div class="top green">Code Lookup</div>
            <div class="content">
                <input
                    class="margin-5"
                    v-model="codeLookup.code"
                    type="text"
                    placeholder="Promo Code"
                />
                <button class="green" @click="lookupCode">LOOKUP</button>
                <div
                    v-if="
                        codeLookup.data &&
                        Object.keys(codeLookup.data).length > 0
                    "
                >
                    <div v-if="codeLookup.data.expires_at !== null">
                        Expires at: {{ codeLookup.data.expires_at }}
                    </div>
                    <div>
                        Is Single Use: {{ codeLookup.data.is_single_use }}
                    </div>
                    <div v-if="codeLookup.data.is_single_use">
                        Is Redeemed: {{ codeLookup.data.is_redeemed }}
                    </div>
                    <div v-if="codeLookup.data.is_redeemed">
                        Redeemed By:
                        <a
                            target="_blank"
                            :href="`/user/${codeLookup.data.redeemed_by.id}`"
                        >
                            {{ codeLookup.data.redeemed_by.username }}
                        </a>
                    </div>
                    <div>
                        Item:
                        <a
                            target="_blank"
                            :href="`/shop/${codeLookup.data.item.id}`"
                        >
                            {{ codeLookup.data.item.name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm, useField } from "vee-validate";
import { object, string, number } from "yup";

import AreYouSure from "../../global/AreYouSure.vue";
import axios from "axios";
import {
    axiosSendErrorToNotification,
    successToNotification,
} from "@/logic/notifications";
import { reactive } from "vue";

const newPromoSchema = object({
    newPromoCode: string().required().label("Promocode"),
    newPromoItem: number()
        .typeError("Item ID must be a number")
        .required()
        .min(1)
        .label("Item ID"),
    newPromoDate: string()
        .required()
        .test("is-greater", "Date must be after today", (value) => {
            // safari returns the date as YYYY-MM-DD but only accepts dates under the format YYYY/MM/DD
            // whoever designed that should be castrated
            let safariSafeDate = value.replace(/-/g, "/");
            return (
                new Date(`${safariSafeDate}`) >=
                new Date(`${new Date().toDateString()} UTC`)
            );
        })
        .label("Date"),
    newPromoTime: string()
        .required()
        .test("is-greater", "Time must be after now", (value, context) => {
            const { newPromoDate } = context.parent;
            let safariSafeDate = newPromoDate?.replace(/-/g, "/");
            return new Date(`${safariSafeDate} ${value}`) >= new Date();
        })
        .label("Time"),
});

const { errors: newPromoErrors, meta: newPromoMeta } = useForm({
    validationSchema: newPromoSchema,
});

const { value: newPromoCode } = useField<string>("newPromoCode");
const { value: newPromoItem } = useField<number>("newPromoItem");
const { value: newPromoDate } = useField<string>("newPromoDate");
const { value: newPromoTime } = useField<string>("newPromoTime");

const massImportSchema = object({
    massImportItem: number().required().min(1).label("Item ID"),
});

const { errors: massImportErrors, meta: massImportMeta } = useForm({
    validationSchema: massImportSchema,
});

const { value: massImportItem } = useField<number>("massImportItem");

function submitNewPromoCode() {
    axios
        .post(`/v1/admin/promocodes/singleCode`, {
            code: newPromoCode.value.replaceAll("-", "").replaceAll(" ", ""),
            item: newPromoItem.value,
            date: new Date(`${newPromoDate.value} ${newPromoTime.value}`),
        })
        .then(() => successToNotification("Code successfully added"))
        .catch(axiosSendErrorToNotification);
}

const massImport = reactive({
    codes: "",
});

function submitMassImportPromo() {
    axios
        .post(`/v1/admin/promocodes/massImport`, {
            codes: massImport.codes
                .replaceAll("-", "")
                .replaceAll(" ", "")
                .split("\n"),
            item: massImportItem.value,
        })
        .then(() => successToNotification("Codes successfully added"))
        .catch(axiosSendErrorToNotification);
}

const codeLookup = reactive({
    code: "",
    data: {} as any,
});

function lookupCode() {
    axios
        .get(
            `/v1/admin/promocodes/lookup/${codeLookup.code.replaceAll("-", "")}`
        )
        .then(({ data }) => {
            codeLookup.data = data.data;
        })
        .catch(axiosSendErrorToNotification);
}
</script>
