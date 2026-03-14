import Cookies from "universal-cookie";
import {CookieValue} from "@/app/lib/types";

export function setCookiesExpire(duration: number) {
    localStorage.setItem("goralys-cookies-expire", String(Date.now() + duration*1000));
}

export function setCookie(cookie: Cookies, key: string, value: CookieValue, maxAge: number) {
    cookie.set(key, value, {
        path: '/',
        maxAge: maxAge, // Expires in 1.5 hours
        httpOnly: false,
    });
    setCookiesExpire(maxAge)
}