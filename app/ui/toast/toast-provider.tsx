'use client';

import {createContext, useContext, useState, ReactNode} from "react";
import ToastElement from "@/app/ui/toast/toast-element";
import {Toast} from "@/app/lib/types";
import {createPortal} from "react-dom";

export type ToastContext = {
    showToast: (toast: Toast) => void;
};

const ToastContext = createContext<ToastContext | null>(null);

export function ToastProvider({ children }: { children: ReactNode }) {
    const [toast, setToast] = useState<Toast | null>(null);
    const [visible, setVisible] = useState(false);

    function showToast(toastInput: Toast) {
        setToast(toastInput);
        setVisible(false);

        requestAnimationFrame(() => setVisible(true));

        setTimeout(() => setVisible(false), 5000);
        setTimeout(() => setToast(null), 5500);
    }

    return (
        <ToastContext.Provider value={{ showToast }}>
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
