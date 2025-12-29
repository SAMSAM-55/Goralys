// Server only fetches helpers.

import {cookies} from "next/headers";
/**
 * Custom function to detect session expiration when fetching data.
 * If a 401-response code is detected, the user is redirected to the login page with a toast.
 * Else, the function returns the response of the fetch request.
 * @param input The url to fetch.
 * @param requestOptions The options of the request, they are the same as for a normal fetch call.
 * @return Promise<Response> The result of the request.
 */
export async function goralysFetch(input: string | URL | Request, requestOptions? : RequestInit): Promise<Response> {
    const cookieStore = await cookies();

    return fetch(input, {
        ...requestOptions,
        headers: {
            ...(requestOptions?.headers ?? {}),
            Cookie: cookieStore.toString(),
        },
        cache: "no-store",
    });
}

export async function fetchCsrf(formId: string): Promise<string | null> {
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

export async function tryJson<T = never>(res: Response): Promise<T | null> {
    const contentType = res.headers.get("content-type");

    if (!res.ok || !contentType?.includes("application/json")) {
        return null;
    }

    try {
        return await res.json();
    } catch {
        return null;
    }
}
