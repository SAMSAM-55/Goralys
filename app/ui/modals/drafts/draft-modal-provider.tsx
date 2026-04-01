'use client';

import {createContext, useContext, useState, ReactNode} from "react";
import DraftModalElement from "@/app/ui/modals/drafts/draft-modal-element";
import {createPortal} from "react-dom";
import {DraftModalResult} from "@/app/lib/types";

export type DraftModalContext = {
    showDraftModal: () => Promise<DraftModalResult>;
};

const DraftModalContext = createContext<DraftModalContext | null>(null);

export function DraftModalProvider({ children }: { children: ReactNode }) {
    const [fileChosen, setChosenFile] = useState<{
        resolve: (value: DraftModalResult) => void;
    } | null>(null);
    const [visible, setVisible] = useState(false);

    function showDraftModal(): Promise<DraftModalResult> {
        return new Promise((resolve: (value: DraftModalResult) => void) => {
            setChosenFile({resolve});
            setVisible(false);
            requestAnimationFrame(() => setVisible(true));
        });
    }

    function handleChooseDraft(file: File | null) {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve({ type: "withDraft", file: file});
            setChosenFile(null);
        }, 500);
    }

    function handleCancel() {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve({ type: "withoutDraft" });
            setChosenFile(null);
        }, 500);
    }

    function handleClose() {
        setVisible(false);
        setTimeout(() => {
            fileChosen?.resolve({ type: "closed" });
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
