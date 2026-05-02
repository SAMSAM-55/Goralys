'use client';

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import { User } from "@/app/lib/types";
import { useToast } from "@/app/ui/toast/toast-provider";
import Cookies from "universal-cookie";
import { fetchUsersClient } from "@/app/lib/user/user.client";

export function useUsers() {
    const [users, setUsers] = useState<User[] | null>(null);
    const { showToast } = useToast();
    const showToastRef = useRef(showToast);
    useEffect(() => { showToastRef.current = showToast; }, [showToast]);

    const cookiesRef = useRef<Cookies>(new Cookies());
    const inFlightRef = useRef<Promise<void> | null>(null);

    const fetchUsers = useCallback(async () => {
        const cookies = cookiesRef.current;
        const cacheKey = 'users-cache';
        const syncKey = 'users-synced';

        if (!cookies.get("username")) return;

        if (inFlightRef.current) return inFlightRef.current;

        let resolve: () => void;
        inFlightRef.current = new Promise<void>(r => { resolve = r; });

        try {
            if (cookies.get(syncKey) == "1") {
                const cached = JSON.parse(sessionStorage.getItem(cacheKey) ?? 'null');
                setUsers(prev => {
                    if (JSON.stringify(prev) === JSON.stringify(cached)) return prev;
                    return cached;
                });
                return;
            }

            const res = await fetchUsersClient();
            const data = await res?.json();

            if (data?.toast) {
                showToastRef.current({
                    type: data.toastType,
                    title: data.toastTitle,
                    message: data.toastMessage,
                });
            }

            cookies.set(syncKey, "1", { path: '/' });
            sessionStorage.setItem(cacheKey, JSON.stringify(data));

            const result = Array.isArray(data) ? data as User[] : null;
            console.log(result);
            setUsers(prev => {
                if (JSON.stringify(prev) === JSON.stringify(result)) return prev;
                return result;
            });
        } finally {
            inFlightRef.current = null;
            resolve!();
        }
    }, []);

    useEffect(() => {
        const cookies = new Cookies();
        const onChange = () => {
            if (inFlightRef.current) return;
            if (cookies.get('users-synced') != "1") {
                void fetchUsers();
            }
        };

        cookies.addChangeListener(onChange);
        return () => cookies.removeChangeListener(onChange);
    }, [fetchUsers]);

    useEffect(() => {
        void fetchUsers();
    }, [fetchUsers]);

    return useMemo(() => ({ users, refetch: fetchUsers, syncKey: 'users-synced' }), [users, fetchUsers]);
}