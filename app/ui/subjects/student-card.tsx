'use client';

import {Subject} from "@/app/lib/types";
import {SubjectInputStudent} from "@/app/ui/inputs/subject-input-student";
import {Button} from "@/app/ui/button";
import {useState} from "react";
import CommentStudent from "@/app/ui/subjects/comment-student";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {useToast} from "@/app/ui/toast/toast-provider";
import Cookies from "universal-cookie";

export default function StudentCard({subjectData, onUpdateAction}: {subjectData: Subject, onUpdateAction: () => void}) {
    const toast = useToast();
    const [subject, setSubject] = useState<string | null>(subjectData.subject);
    const cookies = new Cookies();

    async function saveDraft() {
        const csrfToken = await fetchCsrfClient("save-draft");

        const payload = {
            'teacher-token': subjectData.teacherToken,
            'student-token': subjectData.studentToken,
            'topic': subjectData.topic,
            'draft': subject,
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient("/api/Subjects/Student/SaveDraft/", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify(payload),
        })

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });
        }

        if (data.toastType === 'info' && res.ok) {
            cookies.set('subjects-synced-student', false);
            onUpdateAction();
        }
    }

    async function sendSubject() {
        if (subject?.trim() === subjectData.lastRejected?.trim()) {
            toast.showToast({
                type: "warning",
                title: "Envoi",
                message: "Cette question n’a pas été modifiée depuis son invalidation. Merci de la corriger avant de la renvoyer."
            });
            return;
        }

        const csrfToken = await fetchCsrfClient("submit-subject");

        const payload = {
            'teacher-token': subjectData.teacherToken,
            'student-token': subjectData.studentToken,
            'topic': subjectData.topic,
            'subject': subject,
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient("/api/Subjects/Student/Submit", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });

            if (data.toastType === 'info' && res.ok) {
                cookies.set('subjects-synced-student', false);
                onUpdateAction();
            }
        }
    }

    return (
        <div className="h-fit w-200 flex flex-col bg-sky-200 gap-1 p-1 mb-1 mt-1" >
            <div className="flex flex-row w-full justify-between">
                <strong>{subjectData.topic}</strong>
                <strong>{subjectData.teacher}</strong>
            </div>
            <SubjectInputStudent id={subjectData.studentToken + subjectData.teacherToken + "-subject"} label="Votre Question"
                                 disabled={subjectData.status === 'submitted' || subjectData.status === 'approved'} value={subjectData.subject}
                                 onChange={(e) => {setSubject(e.target.value)}} status={subjectData.status}/>
            <CommentStudent subjectData={subjectData} disabled={true} />
            {!(subjectData.status === "submitted" || subjectData.status === "approved")
            && <>
                <Button className="mb-1! mt-1!" text="Envoyer la question" type="button" onClick={sendSubject} />
                <Button className="mb-1! mt-1!" text="Enregistrer commme brouillon" type="button" onClick={saveDraft} />
            </>}
        </div>
    );
}