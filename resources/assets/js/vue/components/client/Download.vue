<template>
    <div>
        <div>
            <div v-if="hasBought" class="buy-button-holder col-1-1 text-center">
                <button
                    @click="downloadLatest()"
                    class="buy-button very-bold text-center"
                >
                    DOWNLOAD
                    <div class="text-center">{{ latest }}</div>
                </button>
                <div
                    class="other-versions smaller-text"
                    @click="downloadModal = true"
                >
                    Looking for MacOS, Linux or other versions?
                </div>
            </div>
            <div v-else class="buy-button-holder col-1-1 text-center">
                <button
                    @click="purchase"
                    class="buy-button very-bold text-center"
                >
                    Purchase
                    <div class="text-center">${{ productData.price }}</div>
                </button>
                <div class="other-versions smaller-text"></div>
            </div>
        </div>

        <Modal
            title="Download Specific Version"
            v-show="downloadModal"
            @close="downloadModal = false"
        >
            <div class="text-center">
                <button class="green" @click="downloadLatest('windows')">
                    Windows
                </button>
                <button class="green" @click="downloadLatest('mac')">
                    MacOS
                </button>
                <button class="green" @click="downloadLatest('linux')">
                    Linux
                </button>
            </div>
            <div class="modal-buttons">
                <button
                    type="button"
                    class="cancel-button modal-close"
                    @click="downloadModal = false"
                >
                    Cancel
                </button>
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import { onMounted, reactive, ref } from "vue";
import { loadStripe } from "@stripe/stripe-js/pure";
import axios from "axios";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { BH } from "@/logic/bh";
import { Stripe } from "@stripe/stripe-js";

const props = defineProps<{
    authed: boolean;
    latest: string;
    hasBought: boolean;
}>();

onMounted(() => {
    if (!props.hasBought && props.authed) initializeData();
});

const downloadModal = ref<boolean>(false);

const productData = reactive({
    price: "24.50",
    productId: null,
});

let stripe: Stripe | null;

function initializeData() {
    axios.get(BH.apiUrl(`v1/products/all`)).then(({ data }) => {
        let product = data.products.find(
            (product: any) => product.name == "Client Access"
        );
        productData.productId = product.id;
    });
}

function downloadLatest(specific?: string) {
    // assume they are running windows
    let os = "windows";
    // get users os
    let platform = navigator.platform;

    if (platform.indexOf("Linux") !== -1) os = "linux";
    if (platform.indexOf("Mac") !== -1) os = "mac";

    if (specific) os = specific;

    window.location.href = BH.apiUrl(`v1/client/downloadInstaller/${os}`);
}

async function purchase() {
    if (!props.authed) return (window.location.href = "/login");

    stripe = await loadStripe(BH.stripe_public);

    await axios
        .post(BH.apiUrl(`v1/billing/createAsCustomer`))
        .catch(axiosSendErrorToNotification);

    axios
        .post(BH.apiUrl(`v1/billing/createSession/${productData.productId}`))
        .then(({ data }) => stripe?.redirectToCheckout({ sessionId: data.id }))
        .catch(axiosSendErrorToNotification);
}
</script>
