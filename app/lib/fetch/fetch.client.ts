// Client only fetches helpers.

'use client';

import {emitAuthEvent} from "@/app/lib/auth/auth-event";
/**
 * Custom function to detect session expiration when fetching data.
 * If a 401-response code is detected, the user is redirected to the login page with a toast.
 * Else, the function returns the response of the fetch request.
 * @param input The url to fetch.
 * @param requestOptions The options of the request, they are the same as for a normal fetch call.
 * @return Promise<Response> The result of the request.
 */
export async function goralysFetchClient(input: string | URL | Request, requestOptions? : RequestInit): Promise <Response> {
    const res = await fetch(input, {
        credentials: "include",
        ...requestOptions
    });

    console.log(res);
    console.log("Data: ", await res.clone().json());

    if (res.status === 401) {
        try {
            const data = await res.clone().json();
            emitAuthEvent(data?.authEvent ?? "unauthenticated");
        } catch {
            emitAuthEvent("unauthenticated");
        }
    }

    return res;
}

export async function fetchCsrfClient(formId: string): Promise<string | null> {
    const data = {
        'form-id': formId,
    };

    const res = await fetch('/api/Security/CSRF/Create/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify(data),
    });

    if (!res.ok) return null;

    const json = await res.json();
    return json['csrf-token'];
}

export async function tryJsonClient<T = never>(res: Response): Promise<T | null> {
    const contentType = res.headers.get("content-type");

    if (!res.ok || !contentType?.includes("application/json")) {
        emitAuthEvent("unauthenticated");
        return null;
    }

    try {
        return await res.json();
    } catch {
        emitAuthEvent("unauthenticated");
        return null;
    }
}
