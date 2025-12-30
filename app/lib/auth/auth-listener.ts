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
                    message: "Connectez-vous pour accéder à vos questions.",
                });

                setTimeout(() => {
                    router.replace('/user/login');
                }, 0);
            }
            console.log("Logout event emitted");
            emitUserEvent("logout");
        })
    }, [toast, router])

    return null;
}