'use client';

import Link from "next/link";
import Image from "next/image";
import {Card} from "@/app/ui/card";
import LoginForm from "@/app/ui/user/forms/login-form";
import {useEffect} from "react";
import {useRouter, useSearchParams} from "next/navigation";
import {useToast} from "@/app/ui/toast/toast-provider";
import {emitUserEvent} from "@/app/lib/auth/user-event";

export default function LoginPageClient() {
    const searchParams = useSearchParams();
    const toast = useToast();
    const router = useRouter();

    useEffect(() => {
        const reason = searchParams.get('reason');

        if (!reason) return;

        if (reason === 'expired') {
            toast.showToast({
                type: "warning",
                title: "Session",
                message: "Votre session a expirée, vous avez été déconnecté."
            })
        } else if (reason === 'unauthenticated') {
            toast.showToast({
                type: "info",
                title: "Connexion",
                message: "Veuillez vous connecter pour accéder à vos question"
            })
        }

        emitUserEvent("logout");

        router.replace('/user/login');
    }, [searchParams, router]); // The toast dependency is ignored to avoid render loop.

    return (
        <div className="flex grow content-center justify-center items-center">
            <div className="grid w-5xl gap-1 grid-cols-2">
                <Card className="flex-col h-65 bg-sky-300">
                    <Image src="/user/login.svg" alt="Login illustration." width={200} height={150} />

                    <h1 className="text-xl">Bon retour chez Goralys !</h1>
                    <p className="text-xs">Reprenez vos questions là vous les avez laissées.
                        Vous n&apos;avez pas encore de compte ? Pas de panique, créez-en un sur la
                        <Link className="text-sky-600 underline" href="/user/register"> page d&apos;enregistrement</Link></p>
                </Card>
                <LoginForm />
            </div>
        </div>
    );
}
