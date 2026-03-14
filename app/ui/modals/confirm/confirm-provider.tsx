'use client';

import {createContext, useContext, useState, ReactNode} from "react";
import ConfirmElement from "@/app/ui/modals/confirm/confirm-element";
import {ConfirmOptions} from "@/app/lib/types";
import {createPortal} from "react-dom";

export type ConfirmContext = {
    showConfirm: (options: ConfirmOptions) => Promise<boolean>;
};

const ConfirmContext = createContext<ConfirmContext | null>(null);

export function ConfirmProvider({ children }: { children: ReactNode }) {
    const [confirmState, setConfirmState] = useState<{
        options: ConfirmOptions;
        resolve: (value: boolean) => void;
    } | null>(null);
    const [visible, setVisible] = useState(false);

    function showConfirm(options: ConfirmOptions): Promise<boolean> {
        return new Promise((resolve) => {
            setConfirmState({ options, resolve });
            setVisible(false);
            requestAnimationFrame(() => setVisible(true));
        });
    }

    function handleConfirm() {
        setVisible(false);
        setTimeout(() => {
            confirmState?.resolve(true);
            setConfirmState(null);
        }, 500);
    }

    function handleCancel() {
        setVisible(false);
        setTimeout(() => {
            confirmState?.resolve(false);
            setConfirmState(null);
        }, 500);
    }

    return (
        <ConfirmContext.Provider value={{ showConfirm }}>
            {children}

            {confirmState && typeof document !== "undefined" &&
                createPortal(
                    <div className="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm">
                        <ConfirmElement 
                            {...confirmState.options} 
                            visible={visible} 
                            onConfirm={handleConfirm} 
                            onCancel={handleCancel} 
                        />
                    </div>,
                    document.getElementById("confirm-root")!
                )
            }
        </ConfirmContext.Provider>
    );
}

export function useConfirm() {
    const context = useContext(ConfirmContext);
    if (!context) {
        throw new Error("useConfirm must be used within a ConfirmProvider");
    }
    return context;
}
