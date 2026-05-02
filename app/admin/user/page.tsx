'use client';

// import {Button} from "@/app/ui/button";
import {useUsers} from "@/app/hooks/useUsers";
import UserCard from "@/app/ui/admin-pannel/user-card";
import {useState} from "react";
import {User} from "@/app/lib/types";
import {UsersSearchBar} from "@/app/ui/admin-pannel/users-search-bar";

export default function Page() {
    const {users, refetch, syncKey} = useUsers();
    const [currentUsers, setCurrentUsers] = useState<User[] | null>(users)

    return (
        <div className="relative flex flex-col grow h-fit items-center top-10">
            <div className="h-auto w-fit p-2 mt-4">
                <p className="underline text-2xl self-start mb-3">Les utilisateurs de l&apos;établissement :</p>
                <UsersSearchBar users={users} setCurrentUsers={setCurrentUsers} />
                <div className="flex flex-col gap-2">
                    {currentUsers?.map((u) => (
                        <UserCard key={u.role + u.publicId} user={u} onUpdateAction={refetch} syncKey={syncKey} />
                    ))}
                </div>
            </div>
        </div>
    );
}