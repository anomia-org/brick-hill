<template>
    <div class="new-theme">
        <footer>
            <div v-if="shouldFollowUs" class="footer-social very-bold flex">
                <p class="blue-text no-mobile">
                    FOLLOW US
                </p>
                <a href="https://discord.gg/brick-hill" target="_blank">
                    <SvgSprite svg="social/discord.svg" />
                </a>
                <a href="https://twitter.com/hillofbricks" target="_blank">
                    <SvgSprite svg="social/twitter.svg" />
                </a>
                <a href="https://www.twitch.tv/hillofbricks" target="_blank">
                    <SvgSprite svg="social/twitch.svg" />
                </a>
                <a
                    href="https://www.youtube.com/c/hillofbricks/"
                    target="_blank"
                >
                    <SvgSprite svg="social/youtube.svg" />
                </a>
                <a
                    href="https://www.instagram.com/hillofbricks/"
                    target="_blank"
                >
                    <SvgSprite svg="social/instagram.svg" />
                </a>
                <a href="https://www.tiktok.com/@hillofbricks" target="_blank">
                    <SvgSprite svg="social/tiktok.svg" />
                </a>
            </div>
            <div class="footer-links flex">
                <a
                    href="https://mooshimity.com"
                    target="_blank"
                    style="height: 60px; max-width: 360px"
                >
                    <SvgSprite
                        svg="social/mooshimity.svg"
                        style="width: 100%; height: 100%"
                    />
                </a>
                <a href="/terms">TERMS OF SERVICE</a>
                <a href="/rules">RULES OF CONDUCT</a>
                <a href="/privacy">PRIVACY POLICY</a>
                <a href="/staff">STAFF</a>
                <a href="https://merch.brick-hill.com">MERCH STORE</a>
            </div>
            <div class="footer-divider"></div>
            <p class="footer-copyright">
                © {{ new Date().getFullYear() }} Mooshimity. All Rights Reserved
                Brick Hill™ is a registered trademark of Mooshimity, Ltd.
            </p>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import SvgSprite from "./SvgSprite.vue";

const shouldFollowUs = computed<boolean>(
    () => location.pathname === "/" && document.title === "Brick Hill"
);
</script>
