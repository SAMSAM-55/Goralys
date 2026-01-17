'use client';

import { useEffect } from "react";
import { useToast } from "@/app/ui/toast/toast-provider";
import {cacheUserDataClient} from "@/app/lib/user/user.client";
import {emitUserEvent} from "@/app/lib/auth/user-event";
import {goralysFetchClient} from "@/app/lib/fetch/fetch.client";

export default function FlashToastListener() {
    const toast = useToast();

    useEffect(() => {
        let cancelled = false;

        const run = async () => {
            try {
                const res = await goralysFetchClient('Toast/Get-Flash/', {
                    credentials: 'include',
                    cache: 'no-store',
                });

                const data = await res.json();

                if (cancelled || !data) return;

                if (data.toast) {
                    toast.showToast({
                        type: data.toast.toastType,
                        title: data.toast.toastTitle,
                        message: data.toast.toastMessage,
                    });
                }

                if (data.action === "login-success") {
                    await cacheUserDataClient();
                    emitUserEvent("login");
                }
            } catch {}
        };

        void run();

        return () => {
            cancelled = true;
        };
    }, [toast]);

    return null;
}
