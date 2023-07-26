import { provide, onMounted, computed, ref, reactive } from "vue";
import { Tab } from "./tab_interface";

/**
 * Tabs and VerticalTabs use identical code even though they are different components
 * Use this to handle the code in one location
 * Ideally I should probably combine the components into one (?) but this works perfectly fine
 *
 * @param emit
 */
export function useTabs(emit: any) {
    const tabs = ref<Tab[]>([]);
    let activeTabIndex = ref<number | null>(null);

    provide(
        "tabsProvider",
        reactive({
            tabs,
            activeTabIndex,
        })
    );

    provide("addTab", (tab: Tab) => {
        tabs.value.push(tab);
        if (tab.show && activeTabIndex.value === null) {
            emit("loaded");
            selectTab(tab.name);
        }
    });

    provide("updateTab", (data: Tab) => {
        tabs.value.forEach((tab, i) => {
            if (tab.name === data.name) {
                tab.show = data.show;
                if (tab.show && i < (activeTabIndex.value ?? 0)) {
                    selectTab(tab.name);
                }
            }
        });
    });

    const selectTab = (name: string) => {
        tabs.value.forEach((tab, i) => {
            let isSelected = tab.name.toLowerCase() === name.toLowerCase();
            if (isSelected) activeTabIndex.value = i;
            tab.isActive = isSelected;
        });
    };

    const windowHash = ref<string>(window.location.hash);

    const windowHashToTabName = computed(() => {
        return decodeURI(windowHash.value.substring(1));
    });

    onMounted(() => {
        if (!tabs.value.length) return;

        if (window.location.hash) {
            selectTab(windowHashToTabName.value);
        }

        window.addEventListener("hashchange", () => {
            windowHash.value = window.location.hash;
            selectTab(windowHashToTabName.value);
        });
    });

    return {
        tabs,
        selectTab,
    };
}
