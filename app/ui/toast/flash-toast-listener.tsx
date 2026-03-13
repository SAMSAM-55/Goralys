'use client';

import { useEffect } from "react";
import { useToast } from "@/app/ui/toast/toast-provider";
import {goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {GoralysActionHandler} from "@/app/lib/fetch/goralys-action-handler";

export default function FlashToastListener() {
    const toast = useToast();
    const handler = new GoralysActionHandler();

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

                await handler.handle(res);

                if (data.toast) {
                    toast.showToast({
                        type: data.toast.toastType,
                        title: data.toast.toastTitle,
                        message: data.toast.toastMessage,
                    });
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
