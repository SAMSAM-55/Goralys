'use client';

import {Button} from "@/app/ui/button";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {useToast} from "@/app/ui/toast/toast-provider";
import {emitUserEvent} from "@/app/lib/auth/user-event";

export default function Page() {
    const toast = useToast();

    async function logout() {
        const payload = {'csrf-token': await fetchCsrfClient("logout")};

        await goralysFetchClient("/api/User/Auth/Logout", {
            method: "POST",
            body: JSON.stringify(payload),
        });
        emitUserEvent("logout");

        toast.showToast({
            type: "success",
            title: "Déconnexion",
            message: "Vous avez bien été déconnecté"
        })
    }

    return <Button key="logout-button" text="Logout" type="button" onClick={logout} />
}