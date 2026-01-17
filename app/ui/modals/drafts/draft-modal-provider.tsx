'use client';

import {createContext, useContext, useState, ReactNode} from "react";
import DraftModalElement from "@/app/ui/modals/drafts/draft-modal-element";
import {createPortal} from "react-dom";

export type DraftModalContext = {
    showDraftModal: () => Promise<File | string | null>;
};

const DraftModalContext = createContext<DraftModalContext | null>(null);

export function DraftModalProvider({ children }: { children: ReactNode }) {
    const [fileChosen, setChosenFile] = useState<{
        resolve: (value: File | string | null) => void;
    } | null>(null);
    const [visible, setVisible] = useState(false);

    function showDraftModal(): Promise<File | string | null> {
        return new Promise((resolve: (value: File | string | null) => void) => {
            setChosenFile({resolve});
            setVisible(false);
            requestAnimationFrame(() => setVisible(true));
        });
    }

    function handleChooseDraft(file: File | null) {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve(file);
            setChosenFile(null);
        }, 500);
    }

    function handleCancel() {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve(null);
            setChosenFile(null);
        }, 500);
    }

    function handleClose() {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve("modalClosed");
            setChosenFile(null);
        }, 500);
    }

    return (
        <DraftModalContext.Provider value={{ showDraftModal }}>
            {children}

            {fileChosen && typeof document !== "undefined" &&
                createPortal(
                    <div className="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm">
                        <DraftModalElement
                            visible={visible}
                            onChooseDraft={handleChooseDraft}
                            onCancel={handleCancel}
                            onCloseModal={handleClose}
                        />
                    </div>,
                    document.getElementById("draft-modal-root")!
                )
            }
        </DraftModalContext.Provider>
    );
}

export function useDraftModal() {
    const context = useContext(DraftModalContext);
    if (!context) {
        throw new Error("useDraftModal must be used within a ConfirmProvider");
    }
    return context;
}
