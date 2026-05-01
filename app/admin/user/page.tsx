'use client';

import {Button} from "@/app/ui/button";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";

export default function Page() {
    const fetchUsers = async () => {
        const csrfToken = await fetchCsrfClient('get-all-users');
        const payload = {
            'csrf-token': csrfToken
        }

        await goralysFetchClient('users/all', {
            method: 'POST',
            body: JSON.stringify(payload),
        })
    }

    return (
        <>
            <p>User admin panel</p>
            <Button text={"Fetch users"} type='button' onClick={fetchUsers} />
        </>
    );
}