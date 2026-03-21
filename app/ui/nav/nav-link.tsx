'use client';

import Link from "next/link";
import {clsx} from "clsx";
import {usePathname} from "next/navigation";

export function NavLink({name, url, exact = false}: { name: string, url: string, exact?: boolean}) {
    const current = usePathname();

    const isActive =
        exact || url === "/"
            ? current === url
            : current === url || current.startsWith(`${url}/`);

    if (process.env.NODE_ENV === "development") {
        console.groupCollapsed(
            `%c NavLink %c "${name}" %c ${isActive ? "● ACTIVE" : "○ inactive"}`,
            "background:#6366f1;color:white;padding:2px 6px;border-radius:3px 0 0 3px;font-weight:bold",
            "background:#e0e7ff;color:#3730a3;padding:2px 6px;font-weight:bold",
            isActive
                ? "background:#0ea5e9;color:white;padding:2px 6px;border-radius:0 3px 3px 0;font-weight:bold"
                : "background:#e5e7eb;color:#6b7280;padding:2px 6px;border-radius:0 3px 3px 0"
        );
        console.log("  href      →", url);
        console.log("  pathname  →", current);
        console.log("  match     →", `"${current}" startsWith "${url}/" ?`, isActive);
        console.log("  exact     →", current === url);
        console.groupEnd();
    }

    return (
        <Link
            className={clsx(
                "hover:bg-sky-200 h-12.5 w-full items-center flex flex-row hover:text-sky-600 left-1 rounded-md transition-colors p-2",
                {
                    "bg-sky-200 text-sky-600": isActive,
                    "bg-gray-100 text-gray-900": !isActive,
                }
            )}
            href={url}
        >
            {name}
        </Link>
    );
}