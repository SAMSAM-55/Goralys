import {emitAuthEvent} from "@/app/lib/auth/auth-event";

/**
 * Custom function to detect session expiration when fetching data.
 * If a 401 response code is detected, the user is redirected to the login page with a toast.
 * Else, the function returns the response of the fetch request.
 */
export async function goralysFetch(input: string | URL | Request, requestOptions? : RequestInit) {
    const res = await fetch(input, requestOptions,);

    if (res.status === 401) {
        const temp = res.clone();

        const data = await temp.json();
        emitAuthEvent(data["authEvent"])
    }

    return res;
}

export async function fetchCsrf(formId: string, setCsrfToken: CallableFunction): Promise<void> {
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

    if (!res.ok) {
        console.error('An error occurred during the request');
        return;
    }

    const json = await res.json();
    setCsrfToken(json['csrf-token']);
}