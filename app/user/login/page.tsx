import {Suspense} from "react";
import LoginPageClient from "@/app/ui/user/login-page-client";

export default function Page() {
    return (
        <Suspense fallback={null}>
            <LoginPageClient />
        </Suspense>
    );
}
