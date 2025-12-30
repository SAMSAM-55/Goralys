'use client';

import Link from "next/link";
import {clsx} from "clsx";
import {usePathname} from "next/navigation";

export function NavLink({name, url}: {name: string, url: string}) {
    const current = usePathname()

    return (
            <Link className={clsx(
                "hover:bg-sky-200 h-12.5 w-full items-center flex flex-row hover:text-sky-600 left-1 rounded-md transition-colors p-2",
                {
                    "bg-gray-100 text-gray-900": current !== url,
                    "bg-sky-200 text-sky-600": current === url
                })} href={url}>
                {name}
            </Link>
    );
}