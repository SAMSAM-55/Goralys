import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export async function proxy(request: NextRequest) {

    const { pathname } = request.nextUrl;

    if (!pathname.startsWith("/subject")) {
        return NextResponse.next();
    }

    const apiUrl = process.env.NEXT_PUBLIC_API_DOMAIN
    const res = await fetch(
        `${apiUrl}/backend/API/User/Profile/GetRole/`,
        {
            method: "POST",
            headers: {
                Cookie: request.headers.get("cookie") ?? "",
            },
        }
    );

    if (res.status === 401) {
        return NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );
    }

    const { role } = await res.json();

    if (pathname === "/subject") {
        return NextResponse.redirect(
            new URL(`/subject/${role}`, request.url)
        );
    }

    return NextResponse.next();
}

export const config = {
    matcher: '/subject/:path*'
}
