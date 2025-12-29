export async function fetchCsrf (formId: string, setCsrfToken: CallableFunction): Promise<void> {
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