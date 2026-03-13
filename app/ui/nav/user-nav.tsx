'use client';

import Link from "next/link";
import Cookies from "universal-cookie";
import {useEffect, useState} from "react";
import {onUserEvent} from "@/app/lib/auth/user-event";

export function UserNav() {
    const [text, setText] = useState<string | null>(null);
    const [loggedIn, setLoggedIn] = useState<boolean>(false);

    useEffect(() => {
        const cookies = new Cookies();

        const run = () => {
            const isLoggedIn = !!cookies.get("username");

            console.log(cookies)
            console.log(cookies.get("username"))

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

        return () => {
            unsubscribe?.();
        };
    }, []);

    return (
        <Link
            className="bg-gray-100 hover:bg-sky-200 h-12.5 w-full flex items-center gap-2
                       text-gray-900 hover:text-sky-600 rounded-md transition-colors p-2"
            href={loggedIn ? '/user/me' : '/user/login'}
        >
            {loggedIn && <i className="fas fa-user h-3 w-3" />}
            {text}
        </Link>
    );
}
