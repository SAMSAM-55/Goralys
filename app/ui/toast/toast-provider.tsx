'use client';

import {createContext, useContext, useState, ReactNode, useCallback} from "react";
import ToastElement from "@/app/ui/toast/toast-element";
import {Toast} from "@/app/lib/types";
import {createPortal} from "react-dom";

export type ToastContext = {
    showToast: (toast: Toast, duration?: number) => void,
    cacheToast: (toast: Toast, duration?: number) => void,
};

const ToastContext = createContext<ToastContext | null>(null);

export function ToastProvider({ children }: { children: ReactNode }) {
    const [toast, setToast] = useState<Toast | null>(null);
    const [visible, setVisible] = useState(false);

    const cacheToast = useCallback((toastInput: Toast, duration: number = 5500) => {
        if (!toastInput.expires) toastInput.expires = Date.now() + duration;
        sessionStorage.setItem("flash_toast", JSON.stringify(toastInput));
    }, []);

    const showToast = useCallback((toastInput: Toast, duration: number = 5500) => {
        if (!toastInput.expires) toastInput.expires = Date.now() + duration;
        setToast(toastInput);
        setVisible(false);

        requestAnimationFrame(() => setVisible(true));

        setTimeout(() => setVisible(false), duration - 500);
        setTimeout(() => setToast(null), duration);
        cacheToast(toastInput, duration);
    }, [cacheToast]);

    return (
        <ToastContext.Provider value={{ showToast, cacheToast }}>
            {children}

            {toast && typeof document !== "undefined" &&
                createPortal(
                    <ToastElement {...toast} visible={visible} />,
                    document.getElementById("toast-root")!
                )
            }
        </ToastContext.Provider>
    );
}

export function useToast() {
    const context = useContext(ToastContext);
    if (!context) {throw new Error("useToast must be used in a valid context.")}
    return context;
}
