import { NextResponse } from "next/server";
import type { NextRequest } from "next/server";

export async function proxy(request: NextRequest) {
    const { pathname } = request.nextUrl;

    if (!pathname.startsWith("/subject")) {
        return NextResponse.next();
    }

    const hasSession = request.cookies.has("GORALYSSESSID");
    if (!hasSession) {
        return NextResponse.redirect(new URL("/user/login?reason=unauthenticated", request.url));
    }

    const apiUrl = process.env.NEXT_PUBLIC_API_DOMAIN;
    if (!apiUrl) {
        console.error("NEXT_PUBLIC_API_DOMAIN is not set in proxy");
        return NextResponse.redirect(
            new URL("/user/login?reason=server_error", request.url)
        );
    }

    const clientOrigin = request.headers.get("origin") ?? request.nextUrl.origin;

    let res: Response;
    try {
        res = await fetch(`${apiUrl}/User/Profile/GetRole/`, {
            method: "GET",
            headers: {
                cookie: request.headers.get("cookie") ?? "",
                "X-Forwarded-Origin": clientOrigin,
                "Cache-Control": "no-cache, no-store, must-revalidate",
                "Pragma": "no-cache",
                "Expires": "0",
            },
            cache: "no-store",
        });
    } catch (err) {
        console.error("Error calling role API in proxy:", err);
        return NextResponse.redirect(
            new URL("/user/login?reason=server_error", request.url)
        );
    }

    if (res.status === 401) {
        const response = NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );

        response.headers.set('Cache-Control', 'no-store, max-age=0');
        return response;
    }

    if (!res.ok) {
        console.error("Role API returned non-ok status:", res.status);
        return NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );
    }

    let data;
    try {
        data = await res.json();
    } catch (err) {
        console.error("Failed to parse JSON from role API:", err);
        return NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );
    }

    const role = data?.role;
    if (!role) {
        console.error("Role API returned no role field:", data);
        return NextResponse.redirect(
            new URL("/user/login?reason=unauthenticated", request.url)
        );
    }

    let response: NextResponse;

    if (pathname === "/subject" && role) {
        response = NextResponse.redirect(
            new URL(`/subject/${role}`, request.url)
        );
    } else {
        response = NextResponse.next();
    }

    return response;
}

export const config = {
    matcher: "/subject/:path*",
};
