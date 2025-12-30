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
        <div className="w-[200px] h-auto flex flex-col m-0 p-2 rounded-xl">
            <div className="flex flex-col gap-2">
                {links.map((link) => (
                    <NavLink key={link.url} name={link.name} url={link.url} />
                ))}
            </div>
            <div className="flex-grow bg-gray-100 rounded-xl my-3"></div>
            <UserNav />
        </div>
    );
}
