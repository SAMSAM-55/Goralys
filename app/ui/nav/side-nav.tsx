import { NavLink } from "@/app/ui/nav/nav-link";
import {UserNav} from "@/app/ui/nav/user-nav";

export function SideNav() {
    const links: { name: string; url: string }[] = [
        {
            name: "Accueil",
            url: "/",
        },
        {
            name: "Mes Questions",
            url: "/subject",
        },
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
