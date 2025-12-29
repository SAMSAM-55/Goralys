import {UserEvent} from "@/app/lib/types";

const listeners = new Set<(event: UserEvent) => void>();

export function emitUserEvent(event: UserEvent) {
    listeners.forEach((l) => l(event));
}

export function onUserEvent(callback: (event: UserEvent) => void) {
    listeners.add(callback);
    return () => {
        listeners.delete(callback);
    }
}