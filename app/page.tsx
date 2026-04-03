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
        <div className="flex min-h-screen w-full">

            {/* PARTIE GAUCHE */}
            <div className="flex flex-col justify-between w-1/2 p-10">

                <div>
                    <h1 className="font-bold text-xl mb-5">
                        Bienvenue sur Goralys,
                    </h1>

                    <p className="mb-2.5">
                        L&#39;application de gestion du Grand Oral au lycée Auguste et Jean Renoir.
                    </p>

                    <p>
                        Cette plateforme a été entièrement développée par Sami Saubion, élève du lycée.
                    </p>
                </div>

                <div>
                    <Image
                        src="/logo/logo_renoir.png"
                        alt="Logo du lycée"
                        width={75}
                        height={75}
                    />

                    <footer className="text-xs opacity-70 mt-4">
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

            {/* PARTIE DROITE (IMAGE) */}
            <div className="flex items-center justify-center w-2/5 h-screen">
				<div className="relative w-[90%] h-[90%]">
					<Image
						src="/logo/affiche.png"
						alt="affiche grand oral"
						fill
						className="object-contain"
					/>
			</div>
		</div>
    </div>
    );
}
