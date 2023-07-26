<template>
    <div class="card no-rounded no-shadow flex blog-holder">
        <a class="blog-card ellipsis" :href="blog.url" v-for="blog in posts">
            <img
                v-if="blog.feature_image !== null"
                :src="blog.feature_image"
                class="feature-image flex flex-column"
            />
            <div v-else class="feature-image no-feature flex flex-column"></div>
            <div class="info flex flex-column">
                <div class="bold ellipsis title">{{ blog.title }}</div>
                <div class="author ellipsis smaller-text">
                    {{ blog.author }} •
                    {{
                        filterDate(blog.published_at, {
                            month: "long",
                            day: "numeric",
                        })
                    }}
                </div>
            </div>
        </a>
    </div>
</template>

<style lang="scss">
.blog-card {
    position: relative;
    border-radius: 3px;
    height: 150px;
    width: 500px;
    margin-right: 10px;

    &:hover .feature-image {
        transform: scale(1.2);
    }

    .feature-image {
        background-size: cover;
        position: absolute;
        height: 100%;
        width: 100%;
        object-fit: cover;

        transition: all 200ms;

        &.no-feature {
            background: linear-gradient(180deg, #00aaff 0%, #0088ff 100%);
        }
    }

    .info {
        position: relative;
        height: 40%;
        top: 60%;
        background-color: #33333350;
        border-radius: 0px 0px 3px 3px;
        padding-left: 10px;
        backdrop-filter: blur(1.5px);
        text-shadow: 0px 2px 2px rgba(0, 0, 0, 0.6);
        color: #fff;

        .title {
            margin-bottom: 0;
            margin-top: 10px;
            width: calc(90%);
        }

        .author {
            margin-top: 5px;
        }
    }
}

.blog-holder {
    overflow: hidden;
}

@media handheld, only screen and (max-width: 767px) {
    .blog-holder {
        flex-direction: column;
    }
    .blog-card {
        margin-top: 10px;
        margin-bottom: 10px;
        width: 100%;
    }
}
</style>

<script setup lang="ts">
import axios from "axios";
import { ref } from "vue";
import { filterDate } from "@/filters/index";

const BLOG_HOST = "https://blog.brick-hill.com";
const BLOG_KEY = "16a6bc5197c1f4ef1a2b9d84ae";

const GET_POSTS_API = `${BLOG_HOST}/ghost/api/content/posts/?fields=title,url,feature_image,published_at&limit=3&include=authors&key=${BLOG_KEY}`;

type PostAuthor = {
    name: string;
};

type Post = {
    title: string;
    feature_image: string | null;
    published_at: string;
    authors: PostAuthor[];
    author: string;
    url: string;
};

const posts = ref<Post[]>([]);

async function getPosts() {
    let tempPosts: Post[] = (
        await axios.get(GET_POSTS_API, {
            withCredentials: false,
        })
    ).data.posts;

    tempPosts.forEach((post: Post) => {
        post.author = post.authors.map((author) => author.name).join(" & ");
    });

    posts.value = tempPosts;
}

getPosts();
</script>
