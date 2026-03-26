'use client';

import Image from "next/image";

export default function Home() {
    return (
        <div className="flex flex-col grow min-h-screen font-sans">
            <div className="self-center w-3/4 mt-11">

            <h1 className="font-bold text-xl mb-5">Bienvenue sur Goralys,</h1>
                <p className="mb-2.5">
                    L&#39;application de gestion du Grand Oral au lycée Auguste et Jean Renoir.
                </p>
                <p>
                    Cette plateforme a été entièrement développée par Sami Saubion, élève du lycée.
                </p>
            </div>
            <div className="flex grow mb-5" />
            <Image src="/logo/logo_renoir.png" alt="Logo du lycée Auguste et Jean Renoir" width={75} height={10}
            className="relative left-1 bottom-5"/>
        </div>
    );
}
