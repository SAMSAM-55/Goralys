'use client'

import {setCookie} from "@/app/lib/cookies";
import Cookies from "universal-cookie";
import {goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {UserData} from "@/app/lib/types";

export async function cacheUserDataClient() {
    const res = await goralysFetchClient('User/Profile/Get');

    if (!res.ok) {return;}

    const data = (await res.json())['data'] as UserData;

    const cookie = new Cookies();

    setCookie(cookie, "username", data.username, 1.5*60*60);
    setCookie(cookie, "full-name", data.full_name, 1.5*60*60);
    setCookie(cookie, "user-role", data.role, 1.5*60*60);
}

export function emptyUserCacheClient() {
    const cookies = new Cookies();

    Object.keys(cookies.getAll())
        .forEach((name) => {
            cookies.remove(name, { path: "/" });
        });

    cookies.update();
}