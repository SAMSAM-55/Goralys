'use client';

import { NavLink } from "@/app/ui/nav/nav-link";
import {UserNav} from "@/app/ui/nav/user-nav";
import Cookies from "universal-cookie";
import {useEffect, useState} from "react";

export function SideNav() {
    const [isAdmin, setIsAdmin] = useState(false); // false par défaut = même valeur que SSR

    useEffect(() => {
        const cookies = new Cookies();

        const run = () => {
            setIsAdmin(cookies.get("user-role") === "admin");
        }

        const onChange = () => {
            setIsAdmin(cookies.get("user-role") === "admin");
        };

        run();
        cookies.addChangeListener(onChange);
        return () => cookies.removeChangeListener(onChange);
    }, []);

    const links: { name: string; url: string }[] = [
        { name: "Accueil", url: "/" },
        { name: isAdmin ? "Administration" : "Mes Questions", url: "/subject" },
    ];

    return (
        <div className="min-w-50 w-50 h-auto max-h-screen sticky top-0 flex flex-col m-0 p-2 rounded-xl">
            <div className="flex flex-col gap-2">
                {links.map((link) => (
                    <NavLink key={link.url} name={link.name} url={link.url} />
                ))}
            </div>
            <div className="grow bg-gray-100 rounded-xl my-3"></div>
            <UserNav />
        </div>
    );
}
