import type { PageProps } from "@inertiajs/core";

export interface PageWithAuthProps extends PageProps {
    auth: {
        user: App.Models.User;
    };
}
