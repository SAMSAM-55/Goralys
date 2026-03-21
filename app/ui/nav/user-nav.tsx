'use client';

import Link from "next/link";
import Cookies from "universal-cookie";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";
import { onUserEvent } from "@/app/lib/auth/user-event";
import clsx from "clsx";

export function UserNav() {
    const [text, setText] = useState<string | null>(null);
    const [loggedIn, setLoggedIn] = useState<boolean>(false);
    const current = usePathname();

    const targetUrl = loggedIn ? '/user/me' : '/user/login';
    const isActive = current === targetUrl || current.startsWith(`${targetUrl}/`);

    useEffect(() => {
        const cookies = new Cookies();

        const run = () => {
            const isLoggedIn = !!cookies.get("username");
            setLoggedIn(isLoggedIn);
            setText(isLoggedIn ? cookies.get("full-name") : "Se connecter");
        };

        run();
    }, []);

    useEffect(() => {
        const unsubscribe = onUserEvent((event) => {
            const cookies = new Cookies();
            const isLoggedIn = event === "login";

            if (!isLoggedIn) {
                cookies.remove("full-name");
                cookies.remove("user-role");
                cookies.remove("username");
            }

            setLoggedIn(isLoggedIn);
            setText(isLoggedIn ? (cookies.get("full-name") ?? "") : "Se connecter");
        });

        return () => { unsubscribe?.(); };
    }, []);

    if (process.env.NODE_ENV === "development") {
        console.groupCollapsed(
            `%c UserNav %c ${loggedIn ? "logged in" : "logged out"} %c ${isActive ? "● ACTIVE" : "○ inactive"}`,
            "background:#6366f1;color:white;padding:2px 6px;border-radius:3px 0 0 3px;font-weight:bold",
            loggedIn
                ? "background:#dcfce7;color:#166534;padding:2px 6px;font-weight:bold"
                : "background:#fee2e2;color:#991b1b;padding:2px 6px;font-weight:bold",
            isActive
                ? "background:#0ea5e9;color:white;padding:2px 6px;border-radius:0 3px 3px 0;font-weight:bold"
                : "background:#e5e7eb;color:#6b7280;padding:2px 6px;border-radius:0 3px 3px 0"
        );
        console.log("  href     →", targetUrl);
        console.log("  pathname →", current);
        console.log("  active   →", isActive);
        console.groupEnd();
    }

    return (
        <Link
            className={clsx(
                "h-12.5 w-full flex items-center gap-2 rounded-md transition-colors p-2",
                "hover:bg-sky-200 hover:text-sky-600",
                {
                    "bg-sky-200 text-sky-600": isActive,
                    "bg-gray-100 text-gray-900": !isActive,
                }
            )}
            href={targetUrl}
        >
            {loggedIn && <i className="fas fa-user h-3 w-3" />}
            {text}
        </Link>
    );
}