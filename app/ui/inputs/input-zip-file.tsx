'use client';

import {useState} from "react";
import {useToast} from "@/app/ui/toast/toast-provider";
import {ArrowUpTrayIcon} from "@heroicons/react/24/outline";

export default function InputZipFile({text, onFileSelected}: {text: string, onFileSelected: CallableFunction}) {
    const [fileName, setFileName] = useState<string | null>(null);
    const toast = useToast();

    return (
        <label htmlFor="doc" key={`input-file-label-${text}`}
               className="flex items-center gap-0 rounded-xs border border-sky-400 border-dashed bg-sky-200 cursor-pointer">
            <ArrowUpTrayIcon className="size-7 ml-1 mr-2" />
            <div className="">
                <h4 className="text-base font-semibold text-gray-700">{fileName || text}</h4>
                <span className="text-sm text-gray-500">(.zip)</span>
            </div>
            <input key={`input-file-input-${text}`} type="file" id="doc" name="doc" accept=".zip" hidden
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

                       if (newFile.name.endsWith(".zip")) {
                            setFileName(e.target.files?.[0]?.name || null);
                            onFileSelected(newFile);
                            return;
                       }

                       toast.showToast({
                           type: "warning",
                           title: "Fichier",
                           message: "Merci de fournir un fichier zip (.zip)"
                       });
                   }}/>
        </label>
    );
}