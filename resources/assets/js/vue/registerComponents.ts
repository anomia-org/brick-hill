import { defineAsyncComponent, createApp } from "vue";
import { createI18n } from "vue-i18n";
import "../axios/settings.js";
import "../axios/interceptors.js";

type Components = Record<string, () => Promise<typeof import("*.vue")>>;
type AppConfig = Record<string, Record<string, typeof defineAsyncComponent>>;

// https://github.com/intlify/vue-i18n-loader/issues/33
// https://github.com/intlify/bundle-tools/issues/13
// hopefully by time we need to support multiple languages they will have fixed this
const i18n = createI18n({
    legacy: false,
    locale: "en",
});

function registerComponents(components: Components) {
    if (
        document.readyState == "complete" ||
        document.readyState == "interactive"
    ) {
        mountComponents(components);
        return;
    }
    document.addEventListener("DOMContentLoaded", () => {
        return mountComponents(components);
    });
}

function registerGlobal(globalComponents: Components) {
    if (
        document.readyState == "complete" ||
        document.readyState == "interactive"
    ) {
        mountGlobal(globalComponents);
        return;
    }
    document.addEventListener("DOMContentLoaded", () => {
        return mountGlobal(globalComponents);
    });
}

async function mountComponents(components: Components) {
    for (let component in components) {
        if (
            components.hasOwnProperty(component) &&
            document.getElementById(`${component.toLowerCase()}-v`)
        ) {
            let comp = components[component];
            let appConfig: AppConfig = {
                components: {},
            };
            appConfig.components[component] = defineAsyncComponent(comp);
            swapRootElement(
                document.getElementById(
                    `${component.toLowerCase()}-v`
                ) as HTMLElement
            );
            let app = createApp(appConfig);
            app.use(i18n);
            app.mount(`#${component.toLowerCase()}-v`);
        }
    }
}

async function mountGlobal(globalComponents: Components) {
    for (let component in globalComponents) {
        if (globalComponents.hasOwnProperty(component)) {
            if (document.getElementById(`${component.toLowerCase()}-v`)) {
                let elements = Array.from(
                    document.getElementsByClassName(component.toLowerCase())
                ) as HTMLElement[];
                let x = 1;
                for (let element of elements) {
                    let id = swapRootElement(element, x);
                    let appConfig: AppConfig = {
                        components: {},
                    };
                    appConfig.components[component] = defineAsyncComponent(
                        globalComponents[component]
                    );
                    let app = createApp(appConfig);
                    app.use(i18n);
                    app.mount(`#${id}`);
                    x++;
                }
            }
        }
    }
}

/**
 * Vue3 changed some stuff around, the root app element is no longer able to be read properly if mounted.
 * Previously each component on the site would be its own app, but if the app cant read its own component
 * we need to create a new parent and attach the component to it so its no longer both.
 * This might just have to do with the way I mount it now though, but this appears to work fine for now.
 */
function swapRootElement(el: HTMLElement, index = 0) {
    let id = el.id;
    const newRoot = document.createElement("vue-comp");
    el.parentElement?.insertBefore(newRoot, el);
    el.removeAttribute("id");
    if (index > 0) {
        id = `${id}-${index}`;
    }
    newRoot.setAttribute("id", id);
    newRoot.appendChild(el);

    return id;
}

export { registerComponents, registerGlobal };
