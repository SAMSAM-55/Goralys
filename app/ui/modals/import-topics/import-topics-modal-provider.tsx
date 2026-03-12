'use client';

import {createContext, useContext, useState, ReactNode} from "react";
import ImportTopicsModalElement  from "@/app/ui/modals/import-topics/import-topics-modal-element";
import {createPortal} from "react-dom";

export type ImportTopicsModalContext = {
    showImportTopicsModal: () => Promise<File | string | null>;
};

const ImportTopicsModalContext = createContext<ImportTopicsModalContext | null>(null);

export function ImportTopicsModalProvider({ children }: { children: ReactNode }) {
    const [fileChosen, setChosenFile] = useState<{
        resolve: (value: File | string | null) => void;
    } | null>(null);
    const [visible, setVisible] = useState(false);

    function showImportTopicsModal(): Promise<File | string | null> {
        return new Promise((resolve: (value: File | string | null) => void) => {
            setChosenFile({resolve});
            setVisible(false);
            requestAnimationFrame(() => setVisible(true));
        });
    }

    function handleImportTopics(file: File | null) {
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
        <ImportTopicsModalContext.Provider value={{ showImportTopicsModal }}>
            {children}

            {fileChosen && typeof document !== "undefined" &&
                createPortal(
                    <div className="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm">
                        <ImportTopicsModalElement
                            visible={visible}
                            onImportTopics={handleImportTopics}
                            onCancel={handleCancel}
                            onCloseModal={handleClose}
                        />
                    </div>,
                    document.getElementById("import-topics-modal-root")!
                )
            }
        </ImportTopicsModalContext.Provider>
    );
}

export function useImportTopicsModal() {
    const context = useContext(ImportTopicsModalContext);
    if (!context) {
        throw new Error("useImportTopicsModal must be used within a ConfirmProvider");
    }
    return context;
}
