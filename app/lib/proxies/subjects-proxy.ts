import {NextRequest, NextResponse} from "next/server";
import {roleGuard} from "@/app/lib/proxies/guard/role-guard";

export async function SubjectsProxy(request: NextRequest) {
    const { pathname } = request.nextUrl;

    return roleGuard(request, {onSuccess: role => {
        if (pathname === "/subject") {
            return NextResponse.redirect(
                new URL(`/subject/${role}`, request.url)
            );
        } else {
            return NextResponse.next();
        }
    }, allowedRoles: ['admin', 'teacher', 'student']});
}