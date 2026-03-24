'use client';

import { NavLink } from "@/app/ui/nav/nav-link";
import {UserNav} from "@/app/ui/nav/user-nav";
import Cookies from "universal-cookie";
import {useEffect, useState} from "react";
import {UserRole, USER_ROLES} from "@/app/lib/types";

export function SideNav() {
    const [role, setRole] = useState<UserRole['role']>("none");

    useEffect(() => {
        const cookies = new Cookies();

        const run = () => {
            const current: string = cookies.get("user-role") ?? "none";
            setRole(USER_ROLES.includes(current as UserRole['role'])
                ? current as UserRole['role']
                : "none");
        }

        const onChange = () => {
            const role: string = cookies.get("user-role") ?? "none";
            setRole(USER_ROLES.includes(role as UserRole['role'])
                ? role as UserRole['role']
                : "none");
        };

        run();
        cookies.addChangeListener(onChange);
        return () => cookies.removeChangeListener(onChange);
    }, []);

    function getSubjectLinkText() {
        switch (role) {
            case "student":
            case "none":
                return "Mes Questions";
            case "teacher": return "Mes Elèves";
            case "admin": return "Administration";
        }
    }

    const links: { name: string; url: string }[] = [
        { name: "Accueil", url: "/" },
        { name: getSubjectLinkText(), url: "/subject" },
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
