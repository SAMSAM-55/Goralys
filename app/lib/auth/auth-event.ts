import {AuthEvent} from "@/app/lib/types";

const listeners = new Set<(event: AuthEvent) => void>();

export function emitAuthEvent(event: AuthEvent) {
    listeners.forEach((l) => l(event));
}

export function onAuthEvent(callback: (event: AuthEvent) => void) {
    listeners.add(callback);
    return () => {
        listeners.delete(callback);
    }
}