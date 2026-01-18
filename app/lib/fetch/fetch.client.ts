// Client only fetches helpers.

'use client';

const apiUrl = process.env.NEXT_PUBLIC_API_DOMAIN

import {emitAuthEvent} from "@/app/lib/auth/auth-event";
/**
 * Custom function to detect session expiration when fetching data.
 * If a 401-response code is detected, the user is redirected to the login page with a toast.
 * Else, the function returns the response of the fetch request.
 * @param input The url to fetch (relative to the public api domain).
 * @param requestOptions The options of the request, they are the same as for a normal fetch call.
 * @return Promise<Response> The result of the request.
 */
export async function goralysFetchClient(input: string | URL | Request, requestOptions? : RequestInit): Promise <Response> {
    const res = await fetch(`${apiUrl}/${input}`, {
        credentials: "include",
        ...requestOptions
    });

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

    const res = await fetch(`${apiUrl}/Security/CSRF/Create/`, {
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
