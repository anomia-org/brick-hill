<template>
    <div>
        <div v-if="BH.user">
            <div class="small-text mb-10">Enter your comment:</div>
            <textarea
                class="width-100 mb2"
                v-model="writingComment"
                placeholder="Enter Comment"
                style="height: 80px"
            ></textarea>
            <button class="button blue" @click="submitComment">COMMENT</button>
        </div>
        <hr />
        <div v-if="comments.length == 0" class="text-center">
            There are no comments
        </div>
        <div v-for="(comment, index) in comments" :key="index">
            <div class="comment">
                <div class="col-1-7">
                    <a class="user-link" :href="`/user/${comment.author_id}/`">
                        <div class="comment-holder ellipsis">
                            <img
                                :src="
                                    thumbnailStore.getThumbnail({
                                        id: comment.author_id,
                                        type: ThumbnailType.AvatarFull,
                                    })
                                "
                            />
                        </div>
                    </a>
                </div>
                <div class="col-10-12">
                    <div class="body">
                        <div class="bold">
                            <a :href="`/user/${comment.author_id}/`">
                                {{ comment.author.username }}
                            </a>

                            <template v-if="comment.author_id == creatorId">
                                <SvgSprite
                                    class="svg-icon-text"
                                    style="margin-left: 10px"
                                    square="20"
                                    svg="ui/icon_creator.svg"
                                />
                                <span class="bold creator-text small-text">
                                    CREATOR
                                </span>
                            </template>
                        </div>
                        <span v-if="BH.user" class="absolute right top">
                            <a
                                :href="`/report/comment/${comment.id}/`"
                                class="dark-gray-text"
                            >
                                REPORT
                            </a>
                        </span>
                        <a
                            v-if="permissionStore.can('ban')"
                            class="absolute bottom dark-gray-text"
                            style="right: 95px"
                            :href="`/user/${comment.author_id}/ban/comment/${comment.id}`"
                        >
                            BAN
                        </a>
                        <a
                            v-if="permissionStore.can('manage comments')"
                            class="absolute right bottom dark-gray-text"
                            style="cursor: pointer"
                            @click="scrubComment(comment)"
                        >
                            {{ comment.scrubbed == 1 ? "UNSCRUB" : "SCRUB" }}
                        </a>
                        <div
                            style="margin: 10px 0 20px; word-break: break-word"
                        >
                            {{ comment.comment }}
                        </div>
                        <div class="light-gray-text absolute bottom">
                            {{ filterDatetimeLong(comment.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <hr />
        </div>
    </div>
</template>

<style lang="scss">
.creator-text {
    @include themify() {
        color: themed("buttons", "bits", "bg");
    }
}
</style>

<script setup lang="ts">
import axios from "axios";
import { onMounted, ref, toRef, withDefaults, watch } from "vue";
import { BH } from "@/logic/bh";
import { filterDatetimeLong } from "@/filters/index";
import { hasInfiniteScroll } from "@/logic/infinite_scroll";
import { permissionStore } from "@/store/modules/permission";
import { axiosSendErrorToNotification } from "@/logic/notifications";
import InfiniteScrollAPI from "@/logic/apis/InfiniteScrollAPI";
import ModelRelation from "@/logic/data/relations";
import SvgSprite from "../global/SvgSprite.vue";
import { thumbnailStore } from "@/store/modules/thumbnails";
import ThumbnailType from "@/logic/data/thumbnails";

const props = withDefaults(
    defineProps<{
        polyId: number;
        type: ModelRelation;
        creatorId?: number;
        allowLoad?: boolean;
    }>(),
    {
        allowLoad: true,
    }
);

const queryParams: any = new Proxy(
    new URLSearchParams(window.location.search),
    {
        get: (searchParams, prop: string) => searchParams.get(prop),
    }
);

const commentsAPI = new InfiniteScrollAPI<any>(
    `v1/comments/${props.type}/${props.polyId}`,
    10,
    queryParams.commentableCursor ?? ""
);
const { currentData: comments } = commentsAPI;

let setPaused: (state: boolean) => void;

onMounted(() => {
    ({ setPaused } = hasInfiniteScroll(() => commentsAPI.loadNextPage()));
});

const allowLoad = toRef(props, "allowLoad");

watch(allowLoad, (val) => setPaused(!val));

permissionStore.loadCan("ban", "manage comments");

const writingComment = ref<string>();

function submitComment() {
    axios
        .post("/comments/create", {
            commentable_id: props.polyId,
            polymorphic_type: props.type,
            comment: writingComment.value,
        })
        .then(() => {
            commentsAPI.refreshAPI();
        })
        .catch(axiosSendErrorToNotification);
}

function scrubComment(comment: any) {
    axios
        .post(BH.apiUrl(`v1/comments/${comment.id}/scrub`))
        .then(({ data }) => {
            comment.comment = "[ Content Removed ]";
            comment.scrubbed = data.scrubbed;
        });
}
</script>
