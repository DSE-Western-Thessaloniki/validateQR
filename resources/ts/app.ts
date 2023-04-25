import "./bootstrap";
import "../css/app.css";

import { createApp, h, type DefineComponent } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
// @ts-expect-error
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import { createPinia } from "pinia";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, (window as any).Ziggy)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
