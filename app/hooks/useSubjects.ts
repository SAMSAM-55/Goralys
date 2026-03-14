'use client';

import { useCallback, useEffect, useRef, useState } from "react";
import { Subject, UserRole } from "@/app/lib/types";
import { useToast } from "@/app/ui/toast/toast-provider";
import Cookies from "universal-cookie";
import { fetchSubjectsForRoleClient } from "@/app/lib/subjects/subjects.client";

export function useSubjects(role: UserRole['role']) {
    const [subjects, setSubjects] = useState<Subject[] | null>(null);
    const toast = useToast();

    const cookiesRef = useRef<Cookies>(new Cookies());
    const cookies = cookiesRef.current;

    const cacheKey = `subjects-cache-${role}`;
    const syncKey = `subjects-synced-${role}`;

    const inFlightRef = useRef<Promise<void> | null>(null);

    const fetchSubjects = useCallback(async () => {
        if (inFlightRef.current) {
            return inFlightRef.current;
        }

        inFlightRef.current = (async () => {
            try {
                if (cookies.get(syncKey) === "1") {
                    setSubjects(cookies.get(cacheKey));
                    return;
                }

                const res = await fetchSubjectsForRoleClient({ role });
                const data = await res?.json();

                if (data?.toast) {
                    toast.showToast({
                        type: data.toastType,
                        title: data.toastTitle,
                        message: data.toastMessage,
                    });
                }

                cookies.set(cacheKey, data);
                cookies.set(syncKey, "1");

                setSubjects(Array.isArray(data) ? data : null);
            } finally {
                inFlightRef.current = null;
            }
        })();

        return inFlightRef.current;
    }, [cookies, syncKey, role, cacheKey, toast]);


    useEffect(() => {
        void fetchSubjects();
    }, [fetchSubjects]);

    return { subjects, refetch: fetchSubjects };
}
