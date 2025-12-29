import {setCookie} from "@/app/lib/cookies";
import Cookies from "universal-cookie";
import {goralysFetch} from "@/app/lib/fetch";

export async function cacheUserData() {
    const res = await goralysFetch('/api/User/Profile/Get');

    if (!res.ok) {return;}

    const data = (await res.json())['data'];

    const cookie = new Cookies();

    setCookie(cookie, "username", data['username'], 1.5*60*60);
    setCookie(cookie, "full-name", data['full_name'], 1.5*60*60);
    setCookie(cookie, "user-role", data['role'], 1.5*60*60);
}