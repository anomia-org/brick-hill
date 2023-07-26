<template>
    <div class="flex mobile-flex-horiz-center">
        <div v-if="is_sender">
            <form method="POST" action="/trade">
                <input type="hidden" name="_token" :value="BH.csrf">
                <input type="hidden" name="trade_id" :value="trade_id">
                <button type="submit" name="type" value="cancel" class="button small smaller-text red">CANCEL</button>
            </form>
        </div>

        <div class="flex flex-wrap" v-else>
            <button @click="modals.accept_trade.active = true" class="button small smaller-text blue mr-10">ACCEPT</button>
            <a :href="`/trade/create/${receiver_id}?counter=${trade_id}`" class="button small smaller-text yellow">COUNTER</a>
            <form method="POST" action="/trade" class="inline ml-5">
                <input type="hidden" name="_token" :value="BH.csrf">
                <input type="hidden" name="trade_id" :value="trade_id">
                <button type="submit" name="type" value="decline" class="button small smaller-text red">DECLINE</button>
            </form>
        </div>

        <Modal title="Accept Trade" v-show="modals.accept_trade.active" @close="modals.accept_trade.active = false">
            <span>Are you sure you want to accept this trade?</span>
            <form method="POST" action="/trade">
                <input type="hidden" name="_token" :value="BH.csrf">
                <input type="hidden" name="trade_id" :value="trade_id">

                <div class="modal-buttons">
                    <button class="button small smaller-text blue" style="margin-right:10px" type="submit" name="type" value="accept">Accept</button>
                    <button class="button small smaller-text red modal-close" @click="modals.accept_trade.active = false" type="button">Cancel</button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import Modal from "@/components/global/Modal.vue";
import { BH } from "@/logic/bh";
import { reactive } from "vue";

defineProps<{
    is_sender: boolean,
    trade_id: number,
    receiver_id: number
}>();

const modals = reactive({
    accept_trade: {
        active: false,
    },
});
</script>

<style scoped>
button {
    margin: 0px 6px;
}
</style>