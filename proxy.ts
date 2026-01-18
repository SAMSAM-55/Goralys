import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export async function proxy(request: NextRequest) {
    const { pathname } = request.nextUrl;

    if (!pathname.startsWith("/subject")) {
        return NextResponse.next();
    }

    const apiUrl = process.env.NEXT_PUBLIC_API_DOMAIN;
    if (!apiUrl) {
        console.error("NEXT_PUBLIC_API_DOMAIN is not set");
        return NextResponse.next();
    }

    let res: Response;
    try {
        res = await fetch(`${apiUrl}/backend/API/User/Profile/GetRole/`, {
            method: "POST",
            headers: {
                cookie: request.headers.get("cookie") ?? "",
            },
            cache: "no-store",
        });
    } catch (err) {
        console.error("Error calling role API in middleware:", err);
        return NextResponse.next();
    }

    if (res.status === 401) {
        return NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );
    }

    if (!res.ok) {
        console.error("Role API returned non-ok status:", res.status);
        return NextResponse.next();
    }

    let data;
    try {
        data = await res.json();
    } catch (err) {
        console.error("Failed to parse JSON from role API:", err);
        return NextResponse.next();
    }

    const role = data?.role;
    if (!role) {
        console.error("Role API returned no role field:", data);
        return NextResponse.next();
    }

    if (pathname === "/subject") {
        return NextResponse.redirect(
            new URL(`/subject/${role}`, request.url)
        );
    }

    return NextResponse.next();
}

export const config = {
    matcher: "/subject/:path*",
};
