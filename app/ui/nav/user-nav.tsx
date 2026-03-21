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