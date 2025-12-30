import Cookies from "universal-cookie";

export function setCookiesExpire(duration: number) {
    localStorage.setItem("goralys-cookies-expire", String(Date.now() + duration*1000));
}

export function setCookie(cookie: Cookies, key: string, value: any, maxAge: number) {
    cookie.set(key, value, {
        path: '/',
        maxAge: maxAge, // Expires in 1.5 hours
        httpOnly: false,
    });
    setCookiesExpire(maxAge)
}