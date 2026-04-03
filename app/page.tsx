/*
 * Goralys — application de gestion des sujets du Grand oral
 * Copyright (C) 2025-2026 Sami Saubion
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

'use client';

import Image from "next/image";

export default function Home() {
    return (
        <div className="flex flex-col min-h-screen font-sans w-full">

            <div className="self-center w-3/4 mt-11">
                <h1 className="font-bold text-xl mb-5">
                    Bienvenue sur Goralys,
                </h1>

                <p className="mb-2.5">
                    L&#39;application de gestion du grand oral au lycée Auguste et Jean Renoir.
                </p>

                <p>
                    Cette plateforme a été entièrement développée par Sami Saubion, élève du lycée.
                </p>
            </div>

            <div className="flex grow" />

            <div className="flex flex-col items-start gap-2 mb-4 ml-4">
                <Image
                    src="/logo/logo_renoir.png"
                    alt="Logo du lycée Auguste et Jean Renoir"
                    width={75}
                    height={10}
                />

                <footer className="text-xs opacity-70 self-center">
                    © 2026 Sami Saubion — AGPL-3.0 —
                    <a
                        href="https://github.com/SAMSAM-55/Goralys"
                        className="underline ml-1"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Source code
                    </a>
                </footer>
            </div>

        </div>
    );
}
