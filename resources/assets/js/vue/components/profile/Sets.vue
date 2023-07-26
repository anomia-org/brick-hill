<template>
    <div>
        <div v-show="loaded && userSets.length > 0">
            <ul class="set-list" ref="sets">
                <li
                    class="set"
                    :class="{ active: current == index + 1 }"
                    v-for="(set, index) in userSets"
                    :key="index"
                >
                    <a :href="`/play/${set.id}`">
                        <div class="card">
                            <div
                                class="content ellipsis"
                                style="
                                    text-align: center;
                                    overflow: hidden;
                                    border-radius: 5px;
                                "
                            >
                                <span class="set-title">{{ set.name }}</span>
                                <img
                                    :src="`${BH.storage_domain}${set.thumbnail}`"
                                    style="
                                        width: 400px;
                                        max-width: 90%;
                                        margin: 10px auto 10px auto;
                                        display: block;
                                        height: 266.66px;
                                    "
                                />
                                <div class="set-stats">
                                    <ul>
                                        <li>
                                            <div>Visits</div>
                                            <div>
                                                {{ filterNumber(set.visits) }}
                                            </div>
                                        </li>
                                        <li>
                                            <div>Playing</div>
                                            <div>
                                                {{ filterNumber(set.playing) }}
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <div v-show="userSets.length > 1">
                <a class="slider-button left" @click="previous">
                    <i class="fas fa-angle-left"></i>
                </a>
                <a class="slider-button right" @click="next">
                    <i class="fas fa-angle-right"></i>
                </a>
            </div>
        </div>
        <div v-show="loaded && userSets.length == 0" class="card">
            <div class="content" style="text-align: center; border-radius: 5px">
                This user has no sets!
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { filterNumber } from "@/filters/index";
import { BH } from "@/logic/bh";
import axios from "axios";
import { ref } from "vue";

const props = defineProps<{
    user_id: string;
}>();

const current = ref<number>(1);
const loaded = ref<boolean>(false);
// if you name this variable sets the entire page stops working
// feature?
const userSets = ref<any[]>([]);

load();

function load() {
    axios.get(`/api/profile/sets/${props.user_id}`).then(({ data }) => {
        userSets.value = data.data;
        loaded.value = true;
    });
}

function next() {
    if (current.value + 1 > userSets.value.length) {
        current.value = 1;
    } else {
        current.value++;
    }
}

function previous() {
    if (current.value - 1 < 1) {
        current.value = userSets.value.length;
    } else {
        current.value--;
    }
}
</script>
