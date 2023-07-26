<template>
    <div>
        <div class="col-1-2 push-1-4">
            <div class="card">
                <div class="top orange">Purchase {{ productName }}</div>
                <div
                    class="content"
                    style="position: relative; min-height: 120px"
                >
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { loadStripe } from "@stripe/stripe-js/pure";
import axios from "axios";
import { BH } from "@/logic/bh";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import { onMounted } from "vue";

const props = defineProps<{
    productName: string;
    productId: number;
}>();

// wrap in mounted so the loading html will show up
onMounted(async () => {
    const stripe = await loadStripe(BH.stripe_public);

    // user must be a stripe customer before storing a session
    // this could create a lot of extra stripe customers but also fixes an issue where a user can create multiple
    await axios
        .post(BH.apiUrl(`v1/billing/createAsCustomer`))
        .catch(axiosSendErrorToNotification);

    axios
        .post(BH.apiUrl(`v1/billing/createSession/${props.productId}`))
        .then(({ data }) => stripe?.redirectToCheckout({ sessionId: data.id }))
        .catch(axiosSendErrorToNotification);
});
</script>
