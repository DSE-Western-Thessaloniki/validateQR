import type { PageProps } from "@inertiajs/core";

export interface PageWithFlashProps extends PageProps {
    flash: {
        message: string;
        success: string;
        danger: string;
        warning: string;
    };
}
