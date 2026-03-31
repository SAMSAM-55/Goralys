'use client';

import {useState} from "react";
import {useToast} from "@/app/ui/toast/toast-provider";
import {ArrowUpTrayIcon} from "@heroicons/react/24/outline";

export default function InputTextFile({text, maxSizeKB, onFileSelected}: {text: string, maxSizeKB: number, onFileSelected: CallableFunction}) {
    const [fileName, setFileName] = useState<string | null>(null);
    const toast = useToast();

    return (
        <label htmlFor="doc" key={`input-file-label-${text}-max-size-${maxSizeKB}-kb`}
               className="flex items-center gap-0 rounded-xs border border-sky-400 border-dashed bg-sky-300 cursor-pointer">
            <ArrowUpTrayIcon className="size-7 ml-1 mr-2" />
            <div className="">
                <h4 className="text-base font-semibold text-gray-700">{fileName || text}</h4>
                <span className="text-sm text-gray-500">Max {maxSizeKB} KO (.txt, .odt, .docx)</span>
            </div>
            <input key={`input-file-input-${text}-max-size-${maxSizeKB}-kb`} type="file" id="doc" name="doc" accept=".txt,.odt,.docx" hidden
                   onChange={(e) => {
                       const newFile = e.target.files?.[0]
                       if (!newFile) {
                           toast.showToast({
                               type: "warning",
                               title: "Fichier",
                               message: "Merci de fournir un fichier"
                           });
                           return;
                       }

                       if (newFile.size > maxSizeKB * 1024) {
                           toast.showToast({
                               type: "warning",
                               title: "Fichier",
                               message: "Ce fichier est trop volumineux."
                           });
                           return;
                       }

                       if (newFile.name.endsWith(".txt") || newFile.name.endsWith(".odt") || newFile.name.endsWith(".docx")) {
                            setFileName(e.target.files?.[0]?.name || null);
                            onFileSelected(newFile);
                            return;
                       }

                       toast.showToast({
                           type: "warning",
                           title: "Fichier",
                           message: "Merci de fournir un fichier texte (.txt, .odt ou .docx)"
                       });
                   }}/>
        </label>
    );
}