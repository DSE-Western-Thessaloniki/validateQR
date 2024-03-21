import { Role } from "./roles";

export function humanReadableFileSize(size: number, precision = 2): string {
    let i = -1;
    const byteUnits = [" kB", " MB", " GB", " TB", "PB", "EB", "ZB", "YB"];

    if (size < 1024) {
        return `${size} bytes`;
    }

    do {
        size /= 1024;
        i++;
    } while (size > 1024);

    return size.toFixed(precision) + byteUnits[i];
}

export const isAdmin = (user: App.Models.User) => {
    return user.role === Role.Administrator;
};

export const isAuthor = (user: App.Models.User) => {
    return user.role === Role.Author;
};
