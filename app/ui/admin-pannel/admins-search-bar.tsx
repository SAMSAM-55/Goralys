'use client';

import { FloatingInput } from "@/app/ui/inputs/floating-input";
import React, { FormEvent, useEffect, useState } from "react";
import { User } from "@/app/lib/types";

type AdminsSearchBarProps = {
    admins: User[] | null;
    setCurrentAdminsAction: React.Dispatch<React.SetStateAction<User[] | null>>;
}

export function AdminsSearchBar({ admins, setCurrentAdminsAction }: AdminsSearchBarProps) {
    const [searchText, setSearchText] = useState("");

    const handleSearch = (e: FormEvent<HTMLInputElement>) => {
        setSearchText(e.currentTarget.value);
    };

    useEffect(() => {
        if (!admins) return;

        const search = searchText.trim().toLowerCase();

        const sorted = [...admins]
            .filter((u) => !search || u.fullName.trim().toLowerCase().includes(search))
            .sort((a, b) => a.fullName.trim().toLowerCase().localeCompare(b.fullName.trim().toLowerCase(), 'fr'));

        setCurrentAdminsAction(prev => {
            if (JSON.stringify(prev) === JSON.stringify(sorted)) return prev;
            return sorted;
        });
    }, [searchText, admins, setCurrentAdminsAction]);

    return (
        <div className="flex flex-row gap-2 items-end mb-4 w-175!">
            <FloatingInput id="admins-search" label="Rechercher par nom" onInput={handleSearch} />
        </div>
    );
}