'use client';

import { User } from "@/app/lib/types";
import { Card } from "@/app/ui/card";
import { Button } from "@/app/ui/button";
import {AcademicCapIcon, BookOpenIcon} from "@heroicons/react/24/outline";
import {usePasswordModal} from "@/app/ui/modals/password/password-modal-provider";
import {useToast} from "@/app/ui/toast/toast-provider";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import Cookies from "universal-cookie";

export default function UserCard({ user, onUpdateAction, syncKey }: { user: User, onUpdateAction: () => void, syncKey: string }) {
    const password = usePasswordModal();
    const toast = useToast();
    const cookies = new Cookies();

    const fetchAdmin = async (route: string, action: string) => {
        const pwd = await password.showPasswordModal();

        if (!pwd) return;

        if (pwd.trim() === "") {
            toast.showToast({
                type: "warning",
                title: "Mot de passe",
                message: "Veuillez saisir un mot de passe."
            });
            return;
        }
        const csrfToken = await fetchCsrfClient(action)
        const payload = {
            'target': user.publicId,
            'admin-password': pwd,
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient(route, {
            method: 'POST',
            body: JSON.stringify(payload),
        });

        const data = await res?.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });
        }

        if (data.toastType === 'info' && res.ok) {
            cookies.set(syncKey, "0", { path: '/' });
            onUpdateAction();
        }
    }

    const resetPassword = async () => await fetchAdmin('users/reset-password', 'reset-password');

    const deleteUser = async () => await fetchAdmin('users/delete', 'delete-user');

    return (
        <Card className="flex-row w-175! justify-between items-center bg-sky-200 gap-1 p-1 mb-1 mt-1">
            <div className="flex flex-row">
                {
                    // No admins here.
                    user.role == "teacher"
                        ? <BookOpenIcon width={27.5} className="mr-1.5"/>
                        : <AcademicCapIcon width={27.5} className="mr-1.5"/>
                }
                <strong>{user.fullName}</strong>
            </div>
            <div className="flex flex-row w-100 gap-1">
                <Button type="button" text="Réinitialiser le mot de passe" onClick={resetPassword} />
                <Button color="red" className="w-50!" type="button" text="Supprimer" onClick={deleteUser} />
            </div>
        </Card>
    );
}