'use client';

import {useEffect} from "react";
import {useRouter} from "next/navigation";
import {useToast} from "@/app/ui/toast/toast-provider";
import {onUserEvent} from "@/app/lib/auth/user-event";
import {emptyUserCacheClient} from "@/app/lib/user/user.client";

export function UserListener() {
    const router = useRouter();
    const toast = useToast();

    useEffect(() => {
        return onUserEvent(event => {
            if (event === "logout") {
                emptyUserCacheClient();
                setTimeout(() => {
                    window.location.href = '/user/login';
                }, 0);
            }
        });
    }, [toast, router]);

    return null;
}