/*
 * Copyright (C) 2026 Sami Saubion
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

'use client';

import {useEffect} from "react";
import {useRouter} from "next/navigation";
import {useToast} from "@/app/ui/toast/toast-provider";
import {onAuthEvent} from "@/app/lib/auth/auth-event";
import {emitUserEvent} from "@/app/lib/auth/user-event";

export function AuthListener() {
    const router = useRouter();
    const toast = useToast();

    useEffect(() => {
        return onAuthEvent(event => {
            if (event === "expired") {
                toast.showToast({
                    type: "warning",
                    title: "Session",
                    message: "Votre session a expiré, vous avez été déconnecté.",
                })

                setTimeout(() => {
                    router.replace('/user/login');
                }, 0);
            } else if (event === "unauthenticated") {
                toast.showToast({
                    type: "info",
                    title: "Connexion",
                    message: "Veuillez vous connecter pour accéder à votre espace",
                });

                setTimeout(() => {
                    router.replace('/user/login');
                }, 0);
            }
            emitUserEvent("logout");
        })
    }, [toast, router])

    return null;
}