'use client';

import {useImportTopicsModal} from "@/app/ui/modals/import-topics/import-topics-modal-provider";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {useToast} from "@/app/ui/toast/toast-provider";

export default function Page() {
    const modal = useImportTopicsModal();
    const toast = useToast()

    async function sendTopics() {
        const csrfToken = await fetchCsrfClient("import-topics");
        const file = await modal.showImportTopicsModal();

        if (file === "modalClosed") return;

        if (!file) {
            toast.showToast({
                type: 'warning',
                title: "Import des données",
                message: "Veuillez importer un fichier."
            })
            return;
        }

        const formData = new FormData();
        formData.append('csrf-token', csrfToken ?? '');
        formData.append('topics-file', file);

        const res = await goralysFetchClient(
            "Topics/Import/",
            {
                method: "POST",
                credentials: "include",
                body: formData,
            }
        );

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });
        }
    }

    return (
        <div className="flex flex-col grow justify-center items-center gap-1">
            <p>This is the admin page</p>
            <button onClick={sendTopics}>Click</button>
        </div>
    );
}
