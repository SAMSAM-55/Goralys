'use client';

import {clsx} from "clsx";
import {ImportTopicsModalProps} from "@/app/lib/types";
import {Button} from "@/app/ui/button";
import {useEffect, useRef, useState} from "react";
import InputZipFile from "@/app/ui/inputs/input-zip-file";
import {QuestionMarkCircleIcon} from "@heroicons/react/24/outline";

export default function ImportTopicsModalElement({ visible, onImportTopics, onCancel, onCloseModal }: ImportTopicsModalProps) {
    const [topicsFile, setTopicsFile] = useState<File | null>(null);
    const modalRef = useRef<HTMLDivElement>(null);

    const onConfirm = () => {
        onImportTopics(topicsFile);
    };

    useEffect(() => {
        if (!visible) return;

        const handleClickOutside = (e: MouseEvent) => {
            if (
                modalRef.current &&
                !modalRef.current.contains(e.target as Node)
            ) {
                onCloseModal();
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        document.addEventListener("keypress", (e) => {
            if (e.key === "Escape") onCloseModal();
        });
        return () => {
            document.removeEventListener("mousedown", handleClickOutside);
            document.removeEventListener("keypress", (e) => {
                if (e.key === "Escape") onCloseModal();
            });
        };

    }, [visible, onCloseModal]);

    return (
        <div
            ref={modalRef}
            className={clsx(
                "fixed flex flex-col gap-2 p-3 w-125 bg-sky-300 rounded shadow overflow-hidden left-1/2 -translate-x-1/2 top-1 ",
                "after:absolute after:left-0 after:top-0 after:h-full after:w-1.25 after:content-[''] after:bg-blue-600",
                "transition-all duration-500 z-50 ",
                {
                    "translate-y-0 opacity-100": visible,
                    "-translate-y-5 opacity-0": !visible,
                }
            )}
            role="dialog"
            aria-modal="true"
        >
            <div className="flex gap-3">
                <div className="w-11 h-11 flex self-center items-center justify-center">
                    <QuestionMarkCircleIcon className="size-15 text-blue-600" />
                </div>

                <div className="flex flex-col justify-center flex-1">
                    <strong className="text-md">Import des données</strong>
                    <span className="text-sm">Vous pouvez importer les données sous forme d&#39;un fichiez zip</span>
                </div>
            </div>

            <InputZipFile text="Téverser une archive" onFileSelected={setTopicsFile} />

            <div className="flex justify-between gap-2 mt-2">
                <Button
                    className="bg-gray-400! before:bg-gray-500! text-white! border-none! shadow-none! mt-0! mb-0! h-11! w-50!"
                    text="Annuler"
                    type="button"
                    onClick={onCancel}
                />
                <Button
                    className="bg-blue-600! before:bg-blue-700! text-white! border-none! shadow-none! mt-0! mb-0! h-11! w-50!"
                    text="Envoyer les données"
                    type="button"
                    onClick={onConfirm}
                />
            </div>
        </div>
    );
}
