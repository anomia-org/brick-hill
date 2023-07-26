<template>
    <div ref="dropdown">
        <a v-if="typeof activator === 'undefined'">
            <i
                ref="arrowRef"
                @click="toggle"
                class="far fa-caret-square-down dropdown-arrow"
            ></i>
        </a>

        <Teleport to="body">
            <div
                ref="slot"
                class="dropdown-content"
                :class="[contentclass, { active: isOpen }]"
                :style="{
                    top: `${top + arrowHeight + 20}px`,
                    left: `${left - width / 2 + arrowWidth / 2}px`,
                }"
            >
                <div class="dropdown-arrow"></div>
                <slot></slot>
            </div>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";

const props = defineProps<{
    activator?: string;
    contentclass?: string;
}>();

const dropdown = ref<HTMLDivElement>();
const arrowRef = ref<HTMLElement>();
const slot = ref<HTMLDivElement>();

let initiator: any = null;

/**
 * Is the dropdown currently open
 */
const isOpen = ref<boolean>(false);

defineExpose({
    isOpen,
    show,
    hide,
});

onMounted(() => {
    initiator =
        arrowRef.value || document.getElementById(props.activator ?? "");
    initiator.addEventListener("click", toggle);
    document.body.addEventListener("click", outsideClick);

    /*const observe = new ResizeObserver((entries) => {
        console.log(entries);
        setVals();
    });

    if (typeof slot.value !== "undefined") observe.observe(initiator);*/
});

function outsideClick(e: Event) {
    if (
        !initiator.contains(e.target) &&
        !slot.value?.contains(e.target as HTMLElement)
    ) {
        isOpen.value = false;
    }
}

const left = ref<number>(0);
const top = ref<number>(0);
const arrowHeight = ref<number>(0);
const arrowWidth = ref<number>(0);
const width = ref<number>(-1);

function setVals() {
    let x = initiator.getBoundingClientRect();
    left.value = x.left + document.documentElement.scrollLeft;
    top.value = x.top + document.documentElement.scrollTop;
    arrowWidth.value = x.width;
    arrowHeight.value = x.height;
    width.value = slot.value?.clientWidth ?? -1;
}

function toggle(e: Event) {
    // if the initiator is also in a button the event should only be handled by the dropdown
    e.stopImmediatePropagation();
    setVals();
    isOpen.value = !isOpen.value;
}

/**
 * Make the dropdown visible
 */
function show(): void {
    setVals();
    isOpen.value = true;
}

/**
 * Hide the dropdown
 */
function hide(): void {
    isOpen.value = false;
}
</script>
