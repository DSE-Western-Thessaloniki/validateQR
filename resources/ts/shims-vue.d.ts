import { route as routeFn } from "ziggy-js";

declare module "*.vue" {
    import { defineComponent } from "vue";
    const component: ReturnType<typeof defineComponent>;
    export default component;

    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}
