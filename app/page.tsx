'use client';

import Cookies from "universal-cookie";
import {useEffect, useState} from "react";

export default function Home() {
    const [text, setText] = useState<string | null>(null);

    useEffect(() => {
        const cookies = new Cookies();
        const text = `${cookies.get("full-name")}(${cookies.get("username")}) your role is : ${cookies.get("user-role")}`;
        if (text) {
            setText(text);
        }
    }, []);

    return (
        <div className="flex flex-col min-h-screen font-sans">
            <p>Hello, there</p>
            {text && <p>You are : {text}</p>}
        </div>
    );
}
