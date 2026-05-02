'use client';

import {useAdmins, useVirtualAdmins} from "@/app/hooks/useUsers";
import {useEffect, useState} from "react";
import {User} from "@/app/lib/types";
import {AdminsSearchBar} from "@/app/ui/admin-pannel/admins-search-bar";
import AdminCard from "@/app/ui/admin-pannel/admin-card";
import CreateAdminElement from "@/app/ui/admin-pannel/create-admin-element";

export default function Page() {
    const {users: admins, refetch, syncKey} = useAdmins();
    const [currentAdmins, setCurrentAdmins] = useState<User[] | null>(null);
    // eslint-disable-next-line react-hooks/set-state-in-effect
    useEffect(() => setCurrentAdmins(admins), [admins]);

    const {users: virtualAdmins, refetch: virtualRefetch, syncKey: virtualSyncKey} = useVirtualAdmins();
    const [currentVirtualAdmins, setCurrentVirtualAdmins] = useState<User[] | null>(null);
    // eslint-disable-next-line react-hooks/set-state-in-effect
    useEffect(() => setCurrentVirtualAdmins(virtualAdmins), [virtualAdmins]);

    return (
        <div className="relative flex flex-col grow h-fit items-center top-10">
            <div className="h-auto w-fit p-2 mt-4 flex flex-col 3xl:flex-row 3xl:items-start 3xl:gap-10">
                <div className="mb-5">
                    <p className="underline text-2xl self-start mb-3">Les administrateurs de l&apos;établissement :</p>
                    <AdminsSearchBar admins={admins} setCurrentAdminsAction={setCurrentAdmins}/>
                    <div className="flex flex-col gap-2">
                        {currentAdmins?.map((u) => (
                            <AdminCard key={u.role + u.publicId} admin={u} onUpdateAction={refetch} syncKey={syncKey}/>
                        ))}
                    </div>
                </div>
                <div>
                    <p className="underline text-2xl self-start mb-3">Administrateurs non créés :</p>
                    <CreateAdminElement onUpdateAction={async () => {await refetch(); await virtualRefetch()}}
                                        syncKey={syncKey} virtualSyncKey={virtualSyncKey} />
                    <AdminsSearchBar admins={virtualAdmins} setCurrentAdminsAction={setCurrentVirtualAdmins}/>
                    <div className="flex flex-col gap-2">
                        {currentVirtualAdmins?.map((u) => (
                            <AdminCard key={u.role + u.publicId + "-virtual"} admin={u} onUpdateAction={virtualRefetch}
                                      syncKey={virtualSyncKey}/>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}