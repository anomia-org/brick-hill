<template>
    <div>
        <div class="tag-dropdown">
            <div class="tag-dropdown-initiator" id="tag-dropdown">
                <SvgSprite
                    class="svg-icon svg-white tag-arrow"
                    square="12"
                    svg="ui/dropdown_arrow.svg"
                />
            </div>
            <!-- if selectedTags.length == maxTagDisplay, display all tags, else the final tag will be replaced with the +x more text -->
            <Tag
                v-for="tag in selectedTags.slice(
                    0,
                    maxTagDisplay -
                        (selectedTags.length == maxTagDisplay ? 0 : 1)
                )"
                :tag="tag"
                @remove="toggleTag(tag)"
            />
            <Tag
                v-if="selectedTags.length > maxTagDisplay"
                :tag="`+${selectedTags.length - 1} more`"
                :no-remove="true"
            />
        </div>
        <Dropdown activator="tag-dropdown">
            <ul class="tag-drop">
                <div v-for="(category, name) in tagList">
                    <div class="category">{{ name }}</div>
                    <li
                        class="tag"
                        :class="{ selected: selectedTags.includes(tag) }"
                        v-for="tag in category"
                    >
                        <div @click="toggleTag(tag)">{{ tag }}</div>
                    </li>
                </div>
            </ul>
        </Dropdown>
    </div>
</template>

<style lang="scss">
ul.tag-drop {
    width: 200px;
    max-height: 200px;
    overflow-y: scroll;

    .tag {
        * {
            border-radius: 2px;
            margin: 2px 3px 2px 3px;
            padding: 5px 0 5px 10px;
        }

        &.selected * {
            color: #fff;

            @include themify($themes) {
                background-color: themed("tag-color");
            }
        }
    }

    li *::first-letter,
    .category::first-letter {
        text-transform: capitalize;
    }

    .category {
        padding: 5px 0 5px 5px;
        font-weight: 600;

        @include themify() {
            color: themed("text-colors", "light");
        }
    }
}
.tag-dropdown {
    position: relative;
    border-radius: 1px;
    border: 1px solid;
    height: 34px;
    padding: 0 40px 5px 5px;
    white-space: nowrap;
    overflow: hidden;

    @include themify($themes) {
        border-color: themed("inputs", "blend-border");
    }

    .tag-dropdown-initiator {
        z-index: 0;
        height: 100%;
        width: 100%;
        top: 0;
        right: 0;
        position: absolute;
        cursor: pointer;
    }

    .tag-arrow {
        height: 12px;
        position: absolute;
        right: 10px;
        top: 9px;
    }
}
</style>

<script setup lang="ts">
import { ref, watch } from "vue";
import Dropdown from "components/global/Dropdown.vue";
import SvgSprite from "@/components/global/SvgSprite.vue";
import Tag from "./Tag.vue";

const props = defineProps<{
    maxTagDisplay: number;
    selectedTags: string[];
}>();

const emit = defineEmits(["update:selectedTags"]);

const tagList = {
    colors: ["blue", "orange", "green", "red", "purple", "pink"],
};

const selectedTags = ref<string[]>(props.selectedTags);

function toggleTag(tag: string) {
    if (selectedTags.value.includes(tag)) {
        selectedTags.value.splice(selectedTags.value.indexOf(tag), 1);
    } else {
        selectedTags.value.push(tag);
    }
    emit("update:selectedTags", selectedTags.value);
}

watch(
    () => props.selectedTags,
    (tags) => {
        selectedTags.value = tags;
        emit("update:selectedTags", tags);
    }
);
</script>
