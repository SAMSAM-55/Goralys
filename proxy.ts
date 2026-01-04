import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export async function proxy(request: NextRequest) {

    const { pathname } = request.nextUrl;

    console.log("Proxy hit at : ", pathname);

    if (!pathname.startsWith("/subject")) {
        return NextResponse.next();
    }

    console.log(`Proxy fetching: ${request.nextUrl.origin}/api/User/Profile/GetRole/`);

    const res = await fetch(
        `${request.nextUrl.origin}/api/User/Profile/GetRole/`,
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
