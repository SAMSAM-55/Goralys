export async function fetchTest() {
    const data = {
        'form-id': "login"
    }

    const res = await fetch(
        "/api/Security/CSRF/Create/", {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            credentials: "include",
            body: JSON.stringify(data),
        });

    if (!res.ok) {
        console.log("An error occurred during the request");
        return;
    }

    const token = (await res.json())['csrf-token'];

    console.log("Token : ", token);
}